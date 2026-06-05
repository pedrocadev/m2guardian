<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Collaborator;
use App\Services\ScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderController extends Controller
{
    public function dashboard(ScoreService $scoreService)
    {
        $leader = Auth::guard('leader')->user();
        $leader->load('company.collaborators.trainingSession');

        $company = $leader->company;
        $collaborators = $company->collaborators;
        $completed = $collaborators->whereNotNull('completed_at');
        $pending = $collaborators->whereNull('completed_at');

        $completionRate = $collaborators->count() > 0
            ? round(($completed->count() / $collaborators->count()) * 100)
            : 0;

        $avgScore = $completed->filter(fn($c) => $c->score !== null && $c->total_questions > 0)
            ->map(fn($c) => round($c->score / $c->total_questions * 100))
            ->avg() ?? 0;

        // Métricas Pro — só computadas, mas sempre passadas (view controla o blur)
        $departmentStats = $collaborators
            ->groupBy('department')
            ->map(fn($group) => [
                'total'     => $group->count(),
                'completed' => $group->whereNotNull('completed_at')->count(),
                'avg_score' => $group->whereNotNull('completed_at')
                    ->filter(fn($c) => $c->score !== null && $c->total_questions > 0)
                    ->map(fn($c) => round($c->score / $c->total_questions * 100))
                    ->avg() ?? null,
            ])
            ->sortByDesc('total');

        $sessionIds = $company->collaborators
            ->pluck('trainingSession')
            ->filter()
            ->pluck('id');

        $scenarioStats = collect();
        if ($sessionIds->isNotEmpty()) {
            $scenarioStats = Answer::whereIn('training_session_id', $sessionIds)
                ->selectRaw('scenario_id, COUNT(*) as total, SUM(is_correct) as correct_count')
                ->groupBy('scenario_id')
                ->with('scenario:id,label,platform,avatar')
                ->get()
                ->map(fn($row) => [
                    'label'    => $row->scenario?->label ?? 'Cenário #' . $row->scenario_id,
                    'avatar'   => $row->scenario?->avatar ?? '❓',
                    'platform' => $row->scenario?->platform ?? '',
                    'total'    => $row->total,
                    'correct'  => $row->correct_count,
                    'rate'     => $row->total > 0 ? round($row->correct_count / $row->total * 100) : 0,
                ])
                ->sortBy('rate');
        }

        $isPro = $company->isPro();
        $companyScore = $scoreService->forCompany($company);

        return view('leader.dashboard', compact(
            'leader',
            'company',
            'collaborators',
            'completed',
            'pending',
            'completionRate',
            'avgScore',
            'departmentStats',
            'scenarioStats',
            'isPro',
            'companyScore'
        ));
    }

    public function collaboratorScore(int $id, ScoreService $scoreService)
    {
        $leader = Auth::guard('leader')->user();
        $collaborator = Collaborator::with('trainingSession', 'company')
            ->where('id', $id)
            ->where('company_id', $leader->company_id)
            ->firstOrFail();

        $scoreData = $scoreService->forCollaborator($collaborator);

        return view('leader.collaborator-score', compact('leader', 'collaborator', 'scoreData'));
    }

    public function logout(Request $request)
    {
        Auth::guard('leader')->logout();
        $request->session()->invalidate();
        return redirect('/');
    }
}
