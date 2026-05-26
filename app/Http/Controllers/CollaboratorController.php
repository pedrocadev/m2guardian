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
            'scenario_id'      => ['required', 'integer', 'exists:scenarios,id'],
            'chosen_option_key' => ['required', 'string', 'max:8', 'regex:/^[a-z]$/i'],
            'response_time_ms'  => ['nullable', 'integer', 'min:0', 'max:600000'],
            'question_index'    => ['required', 'integer', 'min:0'],
        ]);

        $collaborator = Auth::guard('collaborator')->user()->load('company', 'trainingSession');

        if ($collaborator->hasCompleted()) {
            return redirect()->route('training.completed');
        }

        $session = $collaborator->trainingSession;
        if (!$session || $session->isCompleted()) {
            return redirect()->route('training.index');
        }

        $scenarios = $this->getScenariosFor($collaborator);
        $scenario = $scenarios->firstWhere('id', $validated['scenario_id']);

        if (!$scenario) {
            abort(403);
        }

        $alreadyAnswered = Answer::where('training_session_id', $session->id)
            ->where('scenario_id', $scenario->id)
            ->exists();

        if (!$alreadyAnswered) {
            $question = collect($scenario->content['messages'])
                ->where('type', 'question')
                ->values()
                ->get($validated['question_index']);

            $isCorrect = false;
            if ($question) {
                $chosen = collect($question['options'])->firstWhere('key', $validated['chosen_option_key']);
                $isCorrect = (bool) ($chosen['correct'] ?? false);
            }

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

        $answeredIds = Answer::where('training_session_id', $session->id)->pluck('scenario_id');
        $allDone = $scenarios->every(fn($s) => $answeredIds->contains($s->id));

        if ($allDone) {
            $this->completeTraining($collaborator, $session, $scenarios);
            return redirect()->route('training.completed');
        }

        $next = $scenarios->first(fn($s) => !$answeredIds->contains($s->id));
        return redirect()->route('training.show', $next->id);
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
