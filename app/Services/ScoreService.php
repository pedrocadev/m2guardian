<?php

namespace App\Services;

use App\Models\Collaborator;
use App\Models\Company;
use App\Models\Scenario;
use App\Models\TrainingSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScoreService
{
    private const STRONG_THRESHOLD = 80;
    private const EVOLUTION_THRESHOLD = 60;

    private const LEVELS = [
        'n5' => ['min' => 100, 'tag' => 'NÍVEL 5', 'name' => 'Guardião Digital Certificado'],
        'n4' => ['min' => 85,  'tag' => 'NÍVEL 4', 'name' => 'Guardião Estratégico'],
        'n3' => ['min' => 70,  'tag' => 'NÍVEL 3', 'name' => 'Guardião Atento'],
        'n2' => ['min' => 50,  'tag' => 'NÍVEL 2', 'name' => 'Aprendiz Guardião'],
        'n1' => ['min' => 0,   'tag' => 'NÍVEL 1', 'name' => 'Em Alerta'],
    ];

    private const POSTURES = [
        'guardia'    => ['min' => 100, 'name' => 'Postura Guardiã',     'color' => '#16a34a', 'icon' => '🏆'],
        'madura'     => ['min' => 70,  'name' => 'Postura Madura',      'color' => '#15803d', 'icon' => '🛡️'],
        'atenta'     => ['min' => 50,  'name' => 'Postura Atenta',      'color' => '#ca8a04', 'icon' => '👁️'],
        'evolucao'   => ['min' => 30,  'name' => 'Postura em Evolução', 'color' => '#ea580c', 'icon' => '🌱'],
        'inicial'    => ['min' => 0,   'name' => 'Postura Inicial',     'color' => '#dc2626', 'icon' => '⚠️'],
    ];

    public function forCollaborator(Collaborator $collaborator): array
    {
        $session = $collaborator->trainingSession;
        if (!$session) {
            return $this->emptyCollaboratorResult();
        }
        return $this->forSession($session);
    }

    /**
     * Métricas de uma tentativa específica (útil quando o colaborador refez o teste
     * e o painel do líder mostra cada tentativa separadamente).
     */
    public function forSession(TrainingSession $session): array
    {
        if ($session->total_questions === 0) {
            return $this->emptyCollaboratorResult();
        }

        $percentage = (int) round($session->score / $session->total_questions * 100);
        $level = $this->resolveLevel($percentage);
        $byCategory = $this->sessionCategoryBreakdown($session->id);

        return [
            'level_key'    => $level['key'],
            'level_tag'    => $level['tag'],
            'level_name'   => $level['name'],
            'level_number' => $level['number'],
            'percentage'   => $percentage,
            'score'        => $session->score,
            'total'        => $session->total_questions,
            'passed'       => $session->passed,
            'by_category'  => $byCategory,
            'strong_points'    => $this->filterCategories($byCategory, '>=', self::STRONG_THRESHOLD),
            'evolution_points' => $this->filterCategories($byCategory, '<',  self::EVOLUTION_THRESHOLD),
            'thermometer'  => $this->buildThermometer($percentage, 'level'),
        ];
    }

    public function forCompany(Company $company): array
    {
        $latestSessionIds = $this->latestSessionIdsForCompany($company->id);

        if ($latestSessionIds->isEmpty()) {
            $percentage = 0;
            $total = 0;
            $hits = 0;
        } else {
            $stats = DB::table('answers')
                ->whereIn('answers.training_session_id', $latestSessionIds)
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN answers.is_correct = 1 THEN 1 ELSE 0 END) as hits')
                ->first();

            $total = (int) ($stats->total ?? 0);
            $hits  = (int) ($stats->hits ?? 0);
            $percentage = $total > 0 ? (int) round($hits / $total * 100) : 0;
        }

        $posture = $this->resolvePosture($percentage);

        return [
            'percentage'         => $percentage,
            'total_answers'      => $total,
            'total_hits'         => $hits,
            'posture_key'        => $posture['key'],
            'posture_name'       => $posture['name'],
            'posture_color'      => $posture['color'],
            'posture_icon'       => $posture['icon'],
            'by_category'        => $this->companyCategoryBreakdown($latestSessionIds),
            'problem_scenarios'  => $this->companyProblemScenarios($latestSessionIds),
            'completed_count'    => $this->completedCount($latestSessionIds),
            'thermometer'        => $this->buildThermometer($percentage, 'posture'),
        ];
    }

    /**
     * IDs das últimas TrainingSessions COMPLETAS (uma por colaborador ativo) de uma empresa.
     * Filtrar por completed_at evita que agregados oscilem enquanto alguém está refazendo:
     * durante o refazer, a nova session (ainda vazia) seria a "última" e sumiria os dados
     * da tentativa anterior — usamos a última já finalizada.
     */
    private function latestSessionIdsForCompany(int $companyId): Collection
    {
        return DB::table('training_sessions')
            ->join('collaborators', 'collaborators.id', '=', 'training_sessions.collaborator_id')
            ->where('collaborators.company_id', $companyId)
            ->whereNull('collaborators.deleted_at')
            ->whereNotNull('training_sessions.completed_at')
            ->groupBy('collaborators.id')
            ->select(DB::raw('MAX(training_sessions.id) as latest_id'))
            ->pluck('latest_id');
    }

    private function sessionCategoryBreakdown(int $sessionId): array
    {
        $rows = DB::table('answers')
            ->join('scenarios', 'scenarios.id', '=', 'answers.scenario_id')
            ->where('answers.training_session_id', $sessionId)
            ->whereNotNull('scenarios.category')
            ->groupBy('scenarios.category')
            ->select([
                'scenarios.category',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN answers.is_correct = 1 THEN 1 ELSE 0 END) as hits'),
            ])
            ->get();

        return $this->buildCategoryArray($rows);
    }

    private function companyCategoryBreakdown(Collection $latestSessionIds): array
    {
        if ($latestSessionIds->isEmpty()) {
            return [];
        }

        $rows = DB::table('answers')
            ->join('scenarios', 'scenarios.id', '=', 'answers.scenario_id')
            ->whereIn('answers.training_session_id', $latestSessionIds)
            ->whereNotNull('scenarios.category')
            ->groupBy('scenarios.category')
            ->select([
                'scenarios.category',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN answers.is_correct = 1 THEN 1 ELSE 0 END) as hits'),
            ])
            ->get();

        return $this->buildCategoryArray($rows);
    }

    private function companyProblemScenarios(Collection $latestSessionIds, int $limit = 3): array
    {
        if ($latestSessionIds->isEmpty()) {
            return [];
        }

        $rows = DB::table('answers')
            ->join('scenarios', 'scenarios.id', '=', 'answers.scenario_id')
            ->whereIn('answers.training_session_id', $latestSessionIds)
            ->groupBy('scenarios.id', 'scenarios.label', 'scenarios.platform', 'scenarios.avatar')
            ->select([
                'scenarios.id',
                'scenarios.label',
                'scenarios.platform',
                'scenarios.avatar',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN answers.is_correct = 1 THEN 1 ELSE 0 END) as hits'),
            ])
            ->havingRaw('COUNT(*) >= 2')
            ->get();

        $problems = [];
        foreach ($rows as $row) {
            $errorRate = (int) round((1 - ($row->hits / $row->total)) * 100);
            if ($errorRate < 30) {
                continue;
            }
            $problems[] = [
                'id'         => (int) $row->id,
                'label'      => $row->label,
                'platform'   => $row->platform,
                'avatar'     => $row->avatar,
                'error_rate' => $errorRate,
                'total'      => (int) $row->total,
                'hits'       => (int) $row->hits,
            ];
        }

        usort($problems, fn ($a, $b) => $b['error_rate'] <=> $a['error_rate']);
        return array_slice($problems, 0, $limit);
    }

    private function completedCount(Collection $latestSessionIds): int
    {
        if ($latestSessionIds->isEmpty()) {
            return 0;
        }

        return (int) DB::table('training_sessions')
            ->whereIn('id', $latestSessionIds)
            ->whereNotNull('completed_at')
            ->count();
    }

    private function buildCategoryArray($rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $pct = $row->total > 0 ? (int) round($row->hits / $row->total * 100) : 0;
            $result[$row->category] = [
                'key'        => $row->category,
                'label'      => Scenario::CATEGORIES[$row->category] ?? $row->category,
                'percentage' => $pct,
                'hits'       => (int) $row->hits,
                'total'      => (int) $row->total,
            ];
        }
        uasort($result, fn ($a, $b) => $b['percentage'] <=> $a['percentage']);
        return $result;
    }

    private function filterCategories(array $byCategory, string $op, int $threshold): array
    {
        $filtered = array_filter($byCategory, fn ($c) => match ($op) {
            '>=' => $c['percentage'] >= $threshold,
            '<'  => $c['percentage'] < $threshold,
        });
        return array_values($filtered);
    }

    private function resolveLevel(int $percentage): array
    {
        foreach (self::LEVELS as $key => $config) {
            if ($percentage >= $config['min']) {
                return [
                    'key'    => $key,
                    'tag'    => $config['tag'],
                    'name'   => $config['name'],
                    'number' => (int) substr($key, 1),
                ];
            }
        }
        return ['key' => 'n1', 'tag' => self::LEVELS['n1']['tag'], 'name' => self::LEVELS['n1']['name'], 'number' => 1];
    }

    private function resolvePosture(int $percentage): array
    {
        foreach (self::POSTURES as $key => $config) {
            if ($percentage >= $config['min']) {
                return [
                    'key'   => $key,
                    'name'  => $config['name'],
                    'color' => $config['color'],
                    'icon'  => $config['icon'],
                ];
            }
        }
        return [
            'key'   => 'inicial',
            'name'  => self::POSTURES['inicial']['name'],
            'color' => self::POSTURES['inicial']['color'],
            'icon'  => self::POSTURES['inicial']['icon'],
        ];
    }

    private function emptyCollaboratorResult(): array
    {
        return [
            'level_key' => 'n1', 'level_tag' => 'NÍVEL 1', 'level_name' => 'Em Alerta', 'level_number' => 1,
            'percentage' => 0, 'score' => 0, 'total' => 0, 'passed' => false,
            'by_category' => [], 'strong_points' => [], 'evolution_points' => [],
            'thermometer' => $this->buildThermometer(0, 'level'),
        ];
    }

    /**
     * Constrói os dados do termômetro gameficado.
     * $type: 'level' (N1-N5 individual) ou 'posture' (corporativa).
     */
    private function buildThermometer(int $percentage, string $type): array
    {
        $segments = $type === 'level'
            ? [
                ['key' => 'n1', 'short' => 'N1', 'name' => 'Em Alerta',            'color' => '#dc2626', 'min' => 0,   'max' => 49],
                ['key' => 'n2', 'short' => 'N2', 'name' => 'Aprendiz Guardião',    'color' => '#ea580c', 'min' => 50,  'max' => 69],
                ['key' => 'n3', 'short' => 'N3', 'name' => 'Guardião Atento',      'color' => '#ca8a04', 'min' => 70,  'max' => 84],
                ['key' => 'n4', 'short' => 'N4', 'name' => 'Guardião Estratégico', 'color' => '#16a34a', 'min' => 85,  'max' => 99],
                ['key' => 'n5', 'short' => 'N5', 'name' => 'Certificado',          'color' => '#CC0000', 'min' => 100, 'max' => 100],
            ]
            : [
                ['key' => 'inicial',  'short' => 'INI', 'name' => 'Inicial',     'color' => '#dc2626', 'min' => 0,   'max' => 29],
                ['key' => 'evolucao', 'short' => 'EVO', 'name' => 'Em Evolução', 'color' => '#ea580c', 'min' => 30,  'max' => 49],
                ['key' => 'atenta',   'short' => 'ATE', 'name' => 'Atenta',      'color' => '#ca8a04', 'min' => 50,  'max' => 69],
                ['key' => 'madura',   'short' => 'MAD', 'name' => 'Madura',      'color' => '#16a34a', 'min' => 70,  'max' => 99],
                ['key' => 'guardia',  'short' => 'GRD', 'name' => 'Guardiã',     'color' => '#CC0000', 'min' => 100, 'max' => 100],
            ];

        $currentIndex = 0;
        foreach ($segments as $i => $seg) {
            if ($percentage >= $seg['min'] && $percentage <= $seg['max']) {
                $currentIndex = $i;
                break;
            }
        }

        $current = $segments[$currentIndex];
        $next = $segments[$currentIndex + 1] ?? null;

        return [
            'segments'      => $segments,
            'percentage'    => $percentage,
            'current_key'   => $current['key'],
            'current_name'  => $current['name'],
            'current_color' => $current['color'],
            'next_name'     => $next['name'] ?? null,
            'next_min'      => $next['min'] ?? null,
            'gap'           => $next ? $next['min'] - $percentage : null,
            'is_top'        => $next === null,
        ];
    }
}
