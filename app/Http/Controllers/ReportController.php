<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function downloadPdf()
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

        $avgScore = $completed
            ->filter(fn($c) => $c->score !== null && $c->total_questions > 0)
            ->map(fn($c) => round($c->score / $c->total_questions * 100))
            ->avg() ?? 0;

        $sessionIds = $collaborators
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
                ]);
        }

        $isPro = $company->isPro();
        $generatedAt = now()->format('d/m/Y \à\s H:i');

        $pdf = Pdf::loadView('reports.training-pdf', compact(
            'leader',
            'company',
            'collaborators',
            'completed',
            'pending',
            'completionRate',
            'avgScore',
            'scenarioStats',
            'isPro',
            'generatedAt'
        ))->setPaper('a4', 'portrait');

        $filename = 'relatorio-treinamento-' . $company->slug . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
