@php
    $collaborators = $company->collaborators;
    $completed     = $collaborators->whereNotNull('completed_at');
    $total         = $collaborators->count();
    $rate          = $total > 0 ? round($completed->count() / $total * 100) : 0;
    $avgScore      = $completed
        ->filter(fn($c) => $c->score !== null && $c->total_questions > 0)
        ->map(fn($c) => round($c->score / $c->total_questions * 100))
        ->avg() ?? 0;
@endphp

<style>
    .m2-results { font-family: ui-sans-serif, system-ui, sans-serif; color: #111; }
    .m2-cards {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;
        margin-bottom: 22px;
    }
    .m2-card {
        background: #f9f9f9; border-radius: 8px; padding: 14px;
        border-top: 3px solid var(--card-color, #2563eb);
    }
    .m2-card-label {
        font-size: 10px; color: #888;
        text-transform: uppercase; letter-spacing: 1px;
    }
    .m2-card-value {
        font-size: 28px; font-weight: 900; color: var(--card-color, #111);
        margin-top: 2px;
    }

    /* Postura corporativa */
    .m2-posture-section {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 18px 20px;
        margin-bottom: 22px;
    }
    .m2-section-title {
        font-size: 12px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.8px;
        color: #444;
        margin-bottom: 14px;
        display: flex; align-items: center; gap: 8px;
    }
    .m2-posture-overview {
        display: flex; align-items: center; justify-content: space-between;
        gap: 14px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 14px;
    }
    .m2-posture-badge {
        color: #fff; font-weight: 800; font-size: 13px; letter-spacing: 0.3px;
        padding: 8px 16px; border-radius: 999px;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .m2-posture-pct { font-size: 28px; font-weight: 900; color: #111; line-height: 1; }
    .m2-posture-pct-sub { font-size: 11px; color: #888; margin-top: 3px; }

    .m2-cat-row {
        display: grid; grid-template-columns: 1fr 220px; gap: 14px;
        align-items: center; padding: 6px 0;
    }
    .m2-cat-name { font-size: 13px; color: #333; font-weight: 500; }
    .m2-bar { display: flex; align-items: center; gap: 10px; }
    .m2-bar-track { flex: 1; height: 8px; background: #eee; border-radius: 4px; overflow: hidden; }
    .m2-bar-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }
    .m2-bar-fill.green { background: #16a34a; }
    .m2-bar-fill.yellow { background: #ea580c; }
    .m2-bar-fill.red { background: #dc2626; }
    .m2-bar-pct { font-size: 12px; font-weight: 700; color: #333; min-width: 40px; text-align: right; }

    /* Cenários problemáticos */
    .m2-problem-list {
        display: flex; flex-direction: column; gap: 10px;
    }
    .m2-problem-item {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 12px;
        align-items: center;
        background: #fef2f2;
        border-left: 3px solid #dc2626;
        border-radius: 6px;
        padding: 10px 14px;
    }
    .m2-problem-icon { font-size: 20px; line-height: 1; }
    .m2-problem-label { font-size: 13px; color: #111; font-weight: 600; }
    .m2-problem-platform { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
    .m2-problem-rate {
        font-size: 13px; font-weight: 800; color: #b91c1c;
        background: rgba(220, 38, 38, 0.12);
        padding: 4px 10px; border-radius: 999px;
        white-space: nowrap;
    }

    /* Tabela */
    .m2-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .m2-table thead tr { background: #f9f9f9; }
    .m2-table th {
        padding: 8px 12px; text-align: left;
        font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .m2-table td { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; }
    .m2-table td.center { text-align: center; }
    .m2-status-done { color: #16a34a; font-weight: 700; }
    .m2-status-pending { color: #d97706; font-weight: 700; }
    .m2-collab-email { color: #888; font-size: 11px; }

    .m2-empty {
        text-align: center; color: #999; padding: 24px 16px; font-size: 12.5px;
    }

    @media (max-width: 720px) {
        .m2-cards { grid-template-columns: repeat(2, 1fr); }
        .m2-cat-row { grid-template-columns: 1fr; gap: 4px; }
        .m2-posture-overview { flex-direction: column; align-items: flex-start; gap: 8px; }
    }
</style>

<div class="m2-results">

    {{-- Cards: total / concluídos / conclusão / média --}}
    <div class="m2-cards">
        <div class="m2-card" style="--card-color: #2563eb;">
            <div class="m2-card-label">Total</div>
            <div class="m2-card-value">{{ $total }}</div>
        </div>
        <div class="m2-card" style="--card-color: #16a34a;">
            <div class="m2-card-label">Concluídos</div>
            <div class="m2-card-value">{{ $completed->count() }}</div>
        </div>
        <div class="m2-card" style="--card-color: #CC0000;">
            <div class="m2-card-label">Conclusão</div>
            <div class="m2-card-value">{{ $rate }}%</div>
        </div>
        <div class="m2-card" style="--card-color: #d97706;">
            <div class="m2-card-label">Média Acertos</div>
            <div class="m2-card-value">{{ round($avgScore) }}%</div>
        </div>
    </div>

    {{-- Postura corporativa --}}
    @if($companyScore['total_answers'] > 0)
        <div class="m2-posture-section">
            <div class="m2-section-title">🎯 Postura Corporativa por Categoria</div>
            <div class="m2-posture-overview">
                <div class="m2-posture-badge" style="background: {{ $companyScore['posture_color'] }};">
                    <span>{{ $companyScore['posture_icon'] }}</span>
                    <span>{{ $companyScore['posture_name'] }}</span>
                </div>
                <div style="text-align: right;">
                    <div class="m2-posture-pct">{{ $companyScore['percentage'] }}%</div>
                    <div class="m2-posture-pct-sub">{{ $companyScore['total_answers'] }} respostas · {{ $companyScore['completed_count'] }} concluintes</div>
                </div>
            </div>

            @include('partials.thermometer', ['thermometer' => $companyScore['thermometer'], 'variant' => 'corporate'])

            @if(empty($companyScore['by_category']))
                <div class="m2-empty">Sem dados de categoria.</div>
            @else
                @foreach($companyScore['by_category'] as $cat)
                    <div class="m2-cat-row">
                        <div class="m2-cat-name">{{ $cat['label'] }} <span style="color:#999; font-size:11px;">({{ $cat['hits'] }}/{{ $cat['total'] }})</span></div>
                        <div class="m2-bar">
                            <div class="m2-bar-track">
                                <div class="m2-bar-fill {{ $cat['percentage'] >= 80 ? 'green' : ($cat['percentage'] >= 50 ? 'yellow' : 'red') }}"
                                    style="width: {{ $cat['percentage'] }}%"></div>
                            </div>
                            <span class="m2-bar-pct">{{ $cat['percentage'] }}%</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Cenários problemáticos --}}
        @if(!empty($companyScore['problem_scenarios']))
            <div class="m2-posture-section">
                <div class="m2-section-title">⚠️ Cenários com Maior Taxa de Erro</div>
                <div class="m2-problem-list">
                    @foreach($companyScore['problem_scenarios'] as $scn)
                        <div class="m2-problem-item">
                            <div class="m2-problem-icon">{{ $scn['avatar'] }}</div>
                            <div>
                                <div class="m2-problem-label">{{ $scn['label'] }}</div>
                                <div class="m2-problem-platform">{{ strtoupper($scn['platform']) }} · {{ $scn['total'] }} respostas</div>
                            </div>
                            <div class="m2-problem-rate">{{ $scn['error_rate'] }}% erro</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    {{-- Tabela de colaboradores --}}
    <div class="m2-section-title">👥 Colaboradores</div>
    @if($total === 0)
        <div class="m2-empty">Nenhum colaborador cadastrado.</div>
    @else
        <table class="m2-table">
            <thead>
                <tr>
                    <th>Colaborador</th>
                    <th class="center">Pontuação</th>
                    <th>Status</th>
                    <th>Concluído em</th>
                </tr>
            </thead>
            <tbody>
                @foreach($collaborators as $c)
                    <tr>
                        <td>
                            <strong>{{ $c->name ?? '—' }}</strong>
                            <br><span class="m2-collab-email">{{ $c->email }}</span>
                        </td>
                        <td class="center">
                            @if($c->score !== null && $c->total_questions > 0)
                                {{ round($c->score / $c->total_questions * 100) }}%
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($c->completed_at)
                                <span class="m2-status-done">✔ Concluído</span>
                            @else
                                <span class="m2-status-pending">⏳ Pendente</span>
                            @endif
                        </td>
                        <td style="color:#888; font-size:12px;">
                            {{ $c->completed_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
