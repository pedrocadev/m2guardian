<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Collaborator;
use App\Models\Scenario;
use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollaboratorController extends Controller
{
    public function index()
    {
        $collaborator = Auth::guard('collaborator')->user()->load('company', 'trainingSession');

        if ($collaborator->hasCompleted()) {
            return redirect()->route('training.completed');
        }

        $scenarios = $this->getScenariosFor($collaborator);
        $session = $this->getOrCreateSession($collaborator, $scenarios, request());
        $answeredIds = Answer::where('training_session_id', $session->id)->pluck('scenario_id');
        $nextScenario = $scenarios->first(fn($s) => !$answeredIds->contains($s->id));

        return view('training.index', compact('collaborator', 'scenarios', 'session', 'answeredIds', 'nextScenario'));
    }

    public function show($id)
    {
        $collaborator = Auth::guard('collaborator')->user()->load('company', 'trainingSession');

        if ($collaborator->hasCompleted()) {
            return redirect()->route('training.completed');
        }

        $scenarios = $this->getScenariosFor($collaborator);
        $scenario = $scenarios->firstWhere('id', (int) $id);

        if (!$scenario) {
            abort(403);
        }

        $session = $collaborator->trainingSession;
        if (!$session) {
            return redirect()->route('training.index');
        }

        $alreadyAnswered = Answer::where('training_session_id', $session->id)
            ->where('scenario_id', $scenario->id)
            ->exists();

        if ($alreadyAnswered) {
            $answeredIds = Answer::where('training_session_id', $session->id)->pluck('scenario_id');
            $next = $scenarios->first(fn($s) => !$answeredIds->contains($s->id));
            return redirect()->route($next ? 'training.show' : 'training.completed', $next?->id);
        }

        $position = $scenarios->search(fn($s) => $s->id === $scenario->id) + 1;
        $total = $scenarios->count();

        return view('training.show', compact('collaborator', 'scenario', 'session', 'position', 'total'));
    }

    public function answer(Request $request)
    {
        $validated = $request->validate([
            'scenario_id'       => ['required', 'integer', 'exists:scenarios,id'],
            'chosen_option_key' => ['required', 'string', 'max:8', 'regex:/^[a-z]$/i'],
            'response_time_ms'  => ['nullable', 'integer', 'min:0', 'max:600000'],
            'question_index'    => ['required', 'integer', 'min:0'],
        ]);

        $collaborator = Auth::guard('collaborator')->user()->load('company', 'trainingSession');

        if ($collaborator->hasCompleted()) {
            return $this->respondAnswer($request, ['next_url' => route('training.completed')]);
        }

        $session = $collaborator->trainingSession;
        if (!$session || $session->isCompleted()) {
            return $this->respondAnswer($request, ['next_url' => route('training.index')]);
        }

        $scenarios = $this->getScenariosFor($collaborator);
        $scenario = $scenarios->firstWhere('id', $validated['scenario_id']);

        if (!$scenario) {
            abort(403);
        }

        // Busca a pergunta específica
        $question = collect($scenario->content['messages'])
            ->where('type', 'question')
            ->values()
            ->get($validated['question_index']);

        if (!$question) {
            abort(422, 'Pergunta inválida');
        }

        $chosen    = collect($question['options'])->firstWhere('key', $validated['chosen_option_key']);
        $isCorrect = (bool) ($chosen['correct'] ?? false);
        $feedback  = $chosen['feedback'] ?? '';

        // Salva (ou ignora se já respondida — evita duplicar contagem)
        $existingAnswer = Answer::where('training_session_id', $session->id)
            ->where('scenario_id', $scenario->id)
            ->where('question_index', $validated['question_index'])
            ->first();

        if (!$existingAnswer) {
            Answer::create([
                'training_session_id' => $session->id,
                'collaborator_id'     => $collaborator->id,
                'scenario_id'         => $scenario->id,
                'scenario_version'    => $scenario->version,
                'question_index'      => $validated['question_index'],
                'chosen_option_key'   => $validated['chosen_option_key'],
                'is_correct'          => $isCorrect,
                'response_time_ms'    => $validated['response_time_ms'],
                'answered_at'         => now(),
            ]);
        }

        // Cenário concluído? Todas as perguntas dele foram respondidas?
        $totalQuestionsInScenario = collect($scenario->content['messages'])
            ->where('type', 'question')
            ->count();

        $answeredInScenario = Answer::where('training_session_id', $session->id)
            ->where('scenario_id', $scenario->id)
            ->distinct('question_index')
            ->count('question_index');

        $scenarioComplete = $answeredInScenario >= $totalQuestionsInScenario;

        // Treinamento todo concluído? Todos os cenários têm todas as perguntas respondidas?
        $trainingComplete = false;
        $nextUrl = null;

        if ($scenarioComplete) {
            $allScenariosDone = $scenarios->every(function ($s) use ($session) {
                $total = collect($s->content['messages'])->where('type', 'question')->count();
                $done  = Answer::where('training_session_id', $session->id)
                    ->where('scenario_id', $s->id)
                    ->distinct('question_index')
                    ->count('question_index');
                return $done >= $total;
            });

            if ($allScenariosDone) {
                $this->completeTraining($collaborator, $session, $scenarios);
                $trainingComplete = true;
                $nextUrl = route('training.completed');
            } else {
                $next = $scenarios->first(function ($s) use ($session) {
                    $total = collect($s->content['messages'])->where('type', 'question')->count();
                    $done  = Answer::where('training_session_id', $session->id)
                        ->where('scenario_id', $s->id)
                        ->distinct('question_index')
                        ->count('question_index');
                    return $done < $total;
                });
                $nextUrl = $next ? route('training.show', $next->id) : route('training.completed');
            }
        }

        return $this->respondAnswer($request, [
            'is_correct'        => $isCorrect,
            'feedback'          => $feedback,
            'scenario_complete' => $scenarioComplete,
            'training_complete' => $trainingComplete,
            'next_url'          => $nextUrl,
        ]);
    }

    private function respondAnswer(Request $request, array $data)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($data);
        }

        // Fallback (testes legados não-AJAX): redireciona como antes
        if (!empty($data['next_url'])) {
            return redirect($data['next_url']);
        }
        return back();
    }

    public function completed()
    {
        $collaborator = Auth::guard('collaborator')->user()->load('company', 'trainingSession.answers');
        $session = $collaborator->trainingSession;

        if (!$session || !$session->isCompleted()) {
            return redirect()->route('training.index');
        }

        return view('training.completed', compact('collaborator', 'session'));
    }

    public function logout(Request $request)
    {
        Auth::guard('collaborator')->logout();
        $request->session()->invalidate();
        return redirect('/');
    }

    private function getScenariosFor(Collaborator $collaborator)
    {
        $company = $collaborator->company;

        if ($company->license === 'demo') {
            return Scenario::whereNull('company_id')
                ->where('demo_eligible', true)
                ->where('status', 'active')
                ->orderBy('id')
                ->take(3)
                ->get();
        }

        return Scenario::where(function ($q) use ($company) {
            $q->whereNull('company_id')->orWhere('company_id', $company->id);
        })->where('status', 'active')->orderBy('id')->get();
    }

    private function getOrCreateSession(Collaborator $collaborator, $scenarios, Request $request): TrainingSession
    {
        if ($collaborator->trainingSession) {
            return $collaborator->trainingSession;
        }

        $totalQuestions = $scenarios->sum(function ($s) {
            return collect($s->content['messages'])->where('type', 'question')->count();
        });

        return TrainingSession::create([
            'collaborator_id'  => $collaborator->id,
            'started_at'       => now(),
            'total_scenarios'  => $scenarios->count(),
            'total_questions'  => $totalQuestions,
            'client_ip'        => $request->ip(),
            'client_user_agent' => $request->userAgent(),
        ]);
    }

    private function completeTraining(Collaborator $collaborator, TrainingSession $session, $scenarios): void
    {
        $answers = Answer::where('training_session_id', $session->id)->get();
        $score = $answers->where('is_correct', true)->count();
        $totalQuestions = $answers->count();
        $duration = $session->started_at->diffInSeconds(now());

        $session->update([
            'completed_at'    => now(),
            'score'           => $score,
            'total_questions' => $totalQuestions,
            'duration_seconds' => $duration,
        ]);

        $collaborator->update([
            'completed_at'    => now(),
            'score'           => $score,
            'total_questions' => $totalQuestions,
        ]);
    }
}
