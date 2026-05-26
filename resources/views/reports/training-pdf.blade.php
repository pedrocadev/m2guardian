<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Treinamento — {{ $company->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; background: #fff; }

        .header { background: #111; color: #fff; padding: 20px 28px; border-bottom: 4px solid #CC0000; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .brand { font-size: 18px; font-weight: 900; letter-spacing: 1px; }
        .brand-sub { font-size: 9px; color: #888; letter-spacing: 0.5px; margin-top: 2px; }
        .header-meta { text-align: right; font-size: 10px; color: #aaa; }
        .header-meta strong { color: #fff; display: block; font-size: 14px; margin-bottom: 2px; }
        .report-title { margin-top: 14px; font-size: 13px; color: #ccc; }
        .report-title strong { color: #fff; }

        .content { padding: 24px 28px; }

        .section { margin-bottom: 24px; }
        .section-title { font-size: 11px; font-weight: 700; color: #CC0000; letter-spacing: 1px; text-transform: uppercase; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 12px; }

        .stats-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; }
        .stats-row { display: table-row; }
        .stat-box { display: table-cell; background: #f9f9f9; border-radius: 6px; padding: 12px 14px; border-left: 3px solid #eee; width: 25%; }
        .stat-box.red { border-left-color: #CC0000; }
        .stat-box.green { border-left-color: #16a34a; }
        .stat-box.yellow { border-left-color: #d97706; }
        .stat-box.blue { border-left-color: #2563eb; }
        .stat-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
        .stat-value { font-size: 24px; font-weight: 900; color: #111; }

        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        thead th { background: #111; color: #fff; padding: 8px 10px; text-align: left; font-size: 10px; letter-spacing: 0.5px; }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; }
        tbody tr:nth-child(even) td { background: #fafafa; }

        .pill { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 700; }
        .pill-done { background: #dcfce7; color: #16a34a; }
        .pill-pending { background: #fef9c3; color: #854d0e; }

        .score-cell { white-space: nowrap; }
        .score-bar-wrap { display: inline-block; width: 60px; height: 5px; background: #eee; border-radius: 3px; vertical-align: middle; margin-right: 4px; }
        .score-bar-fill { height: 5px; border-radius: 3px; }
        .score-bar-fill.green { background: #16a34a; }
        .score-bar-fill.yellow { background: #d97706; }
        .score-bar-fill.red { background: #dc2626; }

        .pro-lock { background: #f9f9f9; border: 1px dashed #ddd; border-radius: 6px; padding: 16px; text-align: center; color: #bbb; font-size: 11px; }

        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #eee; text-align: center; font-size: 9px; color: #aaa; }
        .footer strong { color: #CC0000; }

        .watermark-demo { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 80px; font-weight: 900; color: rgba(204,0,0,0.05); z-index: -1; letter-spacing: 10px; }
    </style>
</head>
<body>

@if(!$isPro)
<div class="watermark-demo">DEMO</div>
@endif

<div class="header">
    <div class="header-top">
        <div>
            <div class="brand">🛡️ GUARDIÃO DIGITAL</div>
            <div class="brand-sub">by M2 Cloud & Security</div>
        </div>
        <div class="header-meta">
            <strong>{{ $company->name }}</strong>
            Plano: {{ $isPro ? 'PRO' : 'DEMO' }}<br>
            Gerado em: {{ $generatedAt }}
        </div>
    </div>
    <div class="report-title">
        Relatório de Treinamento em Segurança — <strong>{{ $leader->name }}</strong>
    </div>
</div>

<div class="content">

    {{-- Resumo executivo --}}
    <div class="section">
        <div class="section-title">Resumo Executivo</div>
        <table class="stats-grid" style="border-spacing: 6px;">
            <tr class="stats-row">
                <td class="stat-box blue">
                    <div class="stat-label">Colaboradores</div>
                    <div class="stat-value">{{ $collaborators->count() }}</div>
                </td>
                <td class="stat-box green">
                    <div class="stat-label">Concluídos</div>
                    <div class="stat-value">{{ $completed->count() }}</div>
                </td>
                <td class="stat-box yellow">
                    <div class="stat-label">Pendentes</div>
                    <div class="stat-value">{{ $pending->count() }}</div>
                </td>
                <td class="stat-box red">
                    <div class="stat-label">Taxa de Conclusão</div>
                    <div class="stat-value">{{ $completionRate }}%</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Colaboradores concluídos --}}
    @if($completed->count() > 0)
    <div class="section">
        <div class="section-title">Colaboradores — Treinamentos Concluídos ({{ $completed->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Concluído em</th>
                    <th>Pontuação</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($completed->sortByDesc('completed_at') as $collab)
                <tr>
                    <td><strong>{{ $collab->name ?? '—' }}</strong></td>
                    <td>{{ $collab->email }}</td>
                    <td>{{ $collab->completed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td class="score-cell">
                        @if($collab->score !== null && $collab->total_questions)
                            @php $pct = round($collab->score / $collab->total_questions * 100) @endphp
                            <div class="score-bar-wrap">
                                <div class="score-bar-fill {{ $pct >= 80 ? 'green' : ($pct >= 50 ? 'yellow' : 'red') }}"
                                    style="width: {{ $pct }}%;"></div>
                            </div>
                            {{ $pct }}%
                        @else —
                        @endif
                    </td>
                    <td><span class="pill pill-done">Concluído</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Pendentes --}}
    @if($pending->count() > 0)
    <div class="section">
        <div class="section-title">Colaboradores — Pendentes ({{ $pending->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Convidado em</th>
                    <th>Primeiro acesso</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pending as $collab)
                <tr>
                    <td>{{ $collab->name ?? '—' }}</td>
                    <td>{{ $collab->email }}</td>
                    <td>{{ $collab->invited_at?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $collab->first_access_at?->format('d/m/Y H:i') ?? 'Não acessou' }}</td>
                    <td><span class="pill pill-pending">Pendente</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Desempenho por cenário (Pro) --}}
    <div class="section">
        <div class="section-title">Desempenho por Cenário</div>
        @if($isPro && $scenarioStats->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>Cenário</th>
                    <th>Plataforma</th>
                    <th>Respostas</th>
                    <th>Acertos</th>
                    <th>Taxa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scenarioStats->sortBy('rate') as $stat)
                <tr>
                    <td>{{ $stat['avatar'] }} {{ $stat['label'] }}</td>
                    <td style="text-transform:uppercase; font-size:9px; color:#888;">{{ $stat['platform'] }}</td>
                    <td>{{ $stat['total'] }}</td>
                    <td>{{ $stat['correct'] }}</td>
                    <td class="score-cell">
                        <div class="score-bar-wrap">
                            <div class="score-bar-fill {{ $stat['rate'] >= 80 ? 'green' : ($stat['rate'] >= 50 ? 'yellow' : 'red') }}"
                                style="width: {{ $stat['rate'] }}%;"></div>
                        </div>
                        {{ $stat['rate'] }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @elseif(!$isPro)
        <div class="pro-lock">🔒 Disponível apenas no plano Pro — entre em contato com a M2 Cloud para fazer upgrade.</div>
        @else
        <p style="color:#aaa; font-size:11px;">Nenhum treinamento concluído ainda.</p>
        @endif
    </div>

    {{-- Média geral (Pro) --}}
    @if($isPro && $completed->count() > 0)
    <div class="section">
        <div class="section-title">Média Geral de Acertos</div>
        <table>
            <tr>
                <td style="font-size: 32px; font-weight: 900; color: {{ $avgScore >= 80 ? '#16a34a' : ($avgScore >= 50 ? '#d97706' : '#dc2626') }}; padding: 8px 0;">
                    {{ round($avgScore) }}%
                </td>
                <td style="padding-left: 16px; color: #666; font-size: 12px; vertical-align: middle;">
                    @if($avgScore >= 80) Excelente desempenho geral da equipe.
                    @elseif($avgScore >= 50) Desempenho moderado — atenção aos cenários com menor taxa de acerto.
                    @else Atenção! A equipe apresentou dificuldades significativas. Recomenda-se reforço de treinamento.
                    @endif
                </td>
            </tr>
        </table>
    </div>
    @endif

</div>

<div class="footer">
    Relatório gerado pelo <strong>Guardião Digital</strong> · M2 Cloud & Security · {{ $generatedAt }}<br>
    Este documento é confidencial e destinado exclusivamente a {{ $company->name }}.
</div>

</body>
</html>
