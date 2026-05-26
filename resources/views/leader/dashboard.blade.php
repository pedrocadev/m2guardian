<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Líder — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f5f7; color: #111; min-height: 100vh; }

        .header { background: #111; border-bottom: 3px solid #CC0000; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; height: 60px; }
        .header-brand { display: flex; align-items: center; gap: 10px; }
        .brand-name { color: #fff; font-weight: 900; font-size: 15px; letter-spacing: 1px; }
        .brand-sub { color: #888; font-size: 10px; letter-spacing: 0.5px; display: block; }
        .header-right { display: flex; align-items: center; gap: 20px; }
        .header-user { color: #ccc; font-size: 13px; text-align: right; }
        .header-user strong { color: #fff; display: block; }
        .btn-logout { background: transparent; border: 1px solid #444; color: #ccc; padding: 6px 14px; border-radius: 4px; font-size: 12px; cursor: pointer; text-decoration: none; }
        .btn-logout:hover { border-color: #CC0000; color: #CC0000; }

        .main { max-width: 1100px; margin: 0 auto; padding: 32px 24px; }

        .page-header { margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .page-header h1 { font-size: 22px; font-weight: 800; }
        .page-header p { color: #666; font-size: 14px; margin-top: 4px; }

        .license-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 1px; }
        .license-badge.demo { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
        .license-badge.pro { background: #111; color: #fff; }

        .demo-banner { background: linear-gradient(135deg, #fff8e1, #fff3cd); border: 1px solid #fde047; border-radius: 10px; padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .demo-banner p { font-size: 13px; color: #854d0e; }
        .demo-banner strong { color: #713f12; }
        .btn-upgrade { background: #CC0000; color: #fff; padding: 8px 18px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none; letter-spacing: 0.5px; white-space: nowrap; }

        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 10px; padding: 22px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-top: 3px solid #eee; }
        .stat-card.red { border-top-color: #CC0000; }
        .stat-card.green { border-top-color: #16a34a; }
        .stat-card.yellow { border-top-color: #d97706; }
        .stat-card.blue { border-top-color: #2563eb; }
        .stat-label { font-size: 11px; font-weight: 700; color: #999; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 6px; }
        .stat-value { font-size: 34px; font-weight: 900; color: #111; line-height: 1; }
        .stat-sub { font-size: 12px; color: #888; margin-top: 4px; }
        .progress-wrap { background: #eee; border-radius: 4px; height: 5px; margin-top: 8px; }
        .progress-bar { background: #CC0000; border-radius: 4px; height: 5px; }

        .section-title { font-size: 15px; font-weight: 800; color: #111; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
        .badge-count { background: #f0f0f0; color: #666; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px; }

        .table-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 28px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f9f9f9; font-size: 11px; font-weight: 700; color: #999; letter-spacing: 0.5px; text-transform: uppercase; padding: 12px 20px; text-align: left; border-bottom: 1px solid #eee; }
        tbody td { padding: 13px 20px; font-size: 14px; color: #333; border-bottom: 1px solid #f5f5f5; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #fafafa; }

        .pill { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .pill-done { background: #dcfce7; color: #16a34a; }
        .pill-pending { background: #fef9c3; color: #854d0e; }

        .score-bar { display: flex; align-items: center; gap: 8px; }
        .score-bar-track { flex: 1; background: #eee; border-radius: 4px; height: 6px; }
        .score-bar-fill { background: #CC0000; border-radius: 4px; height: 6px; }
        .score-bar-fill.green { background: #16a34a; }
        .score-bar-fill.yellow { background: #d97706; }
        .score-bar-fill.red { background: #dc2626; }
        .score-text { font-size: 12px; font-weight: 700; color: #555; min-width: 36px; text-align: right; }

        /* Pro lock overlay */
        .pro-section { position: relative; margin-bottom: 28px; }
        .pro-blur { filter: blur(5px); pointer-events: none; user-select: none; }
        .pro-overlay {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.5);
            border-radius: 10px;
            z-index: 10;
        }
        .pro-lock-card {
            background: #fff;
            border: 2px solid #111;
            border-radius: 12px;
            padding: 24px 32px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        }
        .pro-lock-card .lock-icon { font-size: 28px; margin-bottom: 8px; }
        .pro-lock-card h3 { font-size: 15px; font-weight: 800; color: #111; margin-bottom: 6px; }
        .pro-lock-card p { font-size: 12px; color: #666; margin-bottom: 16px; }

        .empty-state { text-align: center; padding: 44px 20px; color: #999; font-size: 14px; }
        .empty-state .icon { font-size: 32px; margin-bottom: 10px; }

        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }
        @media (max-width: 720px) { .two-col { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="header">
    <div class="header-brand">
        <span style="font-size:22px;">🛡️</span>
        <div>
            <span class="brand-name">GUARDIÃO DIGITAL</span>
            <span class="brand-sub">by M2 Cloud & Security</span>
        </div>
    </div>
    <div class="header-right">
        <div class="header-user">
            <strong>{{ $leader->name }}</strong>
            {{ $company->name }}
        </div>
        <form method="POST" action="{{ route('leader.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Sair</button>
        </form>
    </div>
</div>

<div class="main">

    <div class="page-header">
        <div>
            <h1>Painel de Treinamentos</h1>
            <p>Acompanhe o progresso da equipe no programa de conscientização em segurança.</p>
        </div>
        <span class="license-badge {{ $isPro ? 'pro' : 'demo' }}">
            {{ $isPro ? '⭐ PRO' : '🔒 DEMO' }}
        </span>
    </div>

    @if(!$isPro)
    <div class="demo-banner">
        <p>Você está no plano <strong>Demo</strong>. Algumas métricas avançadas estão bloqueadas. Fale com a M2 Cloud para fazer upgrade.</p>
        <a href="mailto:suporte@m2cloud.com.br" class="btn-upgrade">Fazer upgrade →</a>
    </div>
    @endif

    {{-- Stats --}}
    <div class="stats">
        <div class="stat-card blue">
            <div class="stat-label">Colaboradores</div>
            <div class="stat-value">{{ $collaborators->count() }}</div>
            <div class="stat-sub">Convidados para o treinamento</div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">Concluídos</div>
            <div class="stat-value">{{ $completed->count() }}</div>
            <div class="stat-sub">Treinamentos finalizados</div>
        </div>
        <div class="stat-card yellow">
            <div class="stat-label">Pendentes</div>
            <div class="stat-value">{{ $pending->count() }}</div>
            <div class="stat-sub">Aguardando acesso</div>
        </div>
        <div class="stat-card red">
            <div class="stat-label">Taxa de Conclusão</div>
            <div class="stat-value">{{ $completionRate }}%</div>
            <div class="progress-wrap">
                <div class="progress-bar" style="width: {{ $completionRate }}%"></div>
            </div>
        </div>
    </div>

    {{-- Métricas Pro: Média de acertos + Desempenho por Departamento --}}
    <div class="two-col">
        {{-- Média de acertos --}}
        <div class="pro-section">
            @if(!$isPro)<div class="pro-blur">@endif
            <div class="table-card" style="padding: 22px 24px;">
                <div class="stat-label" style="margin-bottom:6px;">Média de Acertos (Pro)</div>
                <div class="stat-value" style="font-size:40px; color: {{ $avgScore >= 80 ? '#16a34a' : ($avgScore >= 50 ? '#d97706' : '#dc2626') }}">
                    {{ round($avgScore) }}%
                </div>
                <div class="stat-sub" style="margin-top:6px;">Entre os colaboradores que concluíram</div>
            </div>
            @if(!$isPro)</div>
            <div class="pro-overlay">
                <div class="pro-lock-card">
                    <div class="lock-icon">🔒</div>
                    <h3>Recurso Pro</h3>
                    <p>Disponível no plano Pro</p>
                    <a href="mailto:suporte@m2cloud.com.br" class="btn-upgrade">Fazer upgrade</a>
                </div>
            </div>
            @endif
        </div>

        {{-- Desempenho por Departamento --}}
        <div class="pro-section">
            @if(!$isPro)<div class="pro-blur">@endif
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Departamento</th>
                            <th>Conclusão</th>
                            <th>Média</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departmentStats as $dept => $stats)
                        <tr>
                            <td><strong>{{ $dept ?: 'Não informado' }}</strong></td>
                            <td>{{ $stats['completed'] }}/{{ $stats['total'] }}</td>
                            <td>
                                @if($stats['avg_score'] !== null)
                                    <div class="score-bar">
                                        <div class="score-bar-track">
                                            <div class="score-bar-fill {{ $stats['avg_score'] >= 80 ? 'green' : ($stats['avg_score'] >= 50 ? 'yellow' : 'red') }}"
                                                style="width: {{ $stats['avg_score'] }}%"></div>
                                        </div>
                                        <span class="score-text">{{ round($stats['avg_score']) }}%</span>
                                    </div>
                                @else
                                    <span style="color:#ccc;">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center; color:#ccc; padding:20px;">Sem dados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(!$isPro)</div>
            <div class="pro-overlay">
                <div class="pro-lock-card">
                    <div class="lock-icon">🔒</div>
                    <h3>Recurso Pro</h3>
                    <p>Disponível no plano Pro</p>
                    <a href="mailto:suporte@m2cloud.com.br" class="btn-upgrade">Fazer upgrade</a>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Desempenho por Cenário (Pro) --}}
    <div class="pro-section">
        <div class="section-title">📊 Desempenho por Cenário</div>
        @if(!$isPro)<div class="pro-blur">@endif
        <div class="table-card">
            @if($scenarioStats->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>Cenário</th>
                        <th>Plataforma</th>
                        <th>Respostas</th>
                        <th>Taxa de Acerto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scenarioStats as $stat)
                    <tr>
                        <td><strong>{{ $stat['avatar'] }} {{ $stat['label'] }}</strong></td>
                        <td style="text-transform:uppercase; font-size:11px; color:#888;">{{ $stat['platform'] }}</td>
                        <td>{{ $stat['correct'] }}/{{ $stat['total'] }}</td>
                        <td>
                            <div class="score-bar">
                                <div class="score-bar-track">
                                    <div class="score-bar-fill {{ $stat['rate'] >= 80 ? 'green' : ($stat['rate'] >= 50 ? 'yellow' : 'red') }}"
                                        style="width: {{ $stat['rate'] }}%"></div>
                                </div>
                                <span class="score-text">{{ $stat['rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <div class="icon">📊</div>
                Nenhum treinamento concluído ainda.
            </div>
            @endif
        </div>
        @if(!$isPro)</div>
        <div class="pro-overlay">
            <div class="pro-lock-card">
                <div class="lock-icon">🔒</div>
                <h3>Recurso Pro</h3>
                <p>Veja quais cenários sua equipe<br>tem mais dificuldade</p>
                <a href="mailto:suporte@m2cloud.com.br" class="btn-upgrade">Fazer upgrade →</a>
            </div>
        </div>
        @endif
    </div>

    {{-- Tabela de colaboradores --}}
    @if($completed->count() > 0)
    <div class="section-title">✅ Concluídos <span class="badge-count">{{ $completed->count() }}</span></div>
    <div class="table-card">
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
                @foreach($completed as $collab)
                <tr>
                    <td><strong>{{ $collab->name ?? '—' }}</strong></td>
                    <td>{{ $collab->email }}</td>
                    <td>{{ $collab->completed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td>
                        @if($collab->score !== null && $collab->total_questions)
                            @php $pct = round($collab->score / $collab->total_questions * 100) @endphp
                            <div class="score-bar">
                                <div class="score-bar-track">
                                    <div class="score-bar-fill {{ $pct >= 80 ? 'green' : ($pct >= 50 ? 'yellow' : 'red') }}"
                                        style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="score-text">{{ $pct }}%</span>
                            </div>
                        @else
                            —
                        @endif
                    </td>
                    <td><span class="pill pill-done">Concluído</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section-title">⏳ Pendentes <span class="badge-count">{{ $pending->count() }}</span></div>
    <div class="table-card">
        @if($pending->count() > 0)
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
                    <td><strong>{{ $collab->name ?? '—' }}</strong></td>
                    <td>{{ $collab->email }}</td>
                    <td>{{ $collab->invited_at?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $collab->first_access_at?->format('d/m/Y H:i') ?? 'Ainda não acessou' }}</td>
                    <td><span class="pill pill-pending">Pendente</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @elseif($collaborators->count() === 0)
        <div class="empty-state">
            <div class="icon">👥</div>
            Nenhum colaborador convidado ainda.<br>
            <span style="font-size:12px;">Solicite ao responsável M2 Cloud que cadastre sua equipe.</span>
        </div>
        @else
        <div class="empty-state">
            <div class="icon">🎉</div>
            Todos os colaboradores já concluíram o treinamento!
        </div>
        @endif
    </div>

</div>
</body>
</html>
