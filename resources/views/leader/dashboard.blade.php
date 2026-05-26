<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Líder — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: #f4f5f7;
            color: #111;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: #111;
            border-bottom: 3px solid #CC0000;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
        }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-brand .shield { font-size: 22px; }
        .header-brand .brand-name {
            color: #fff;
            font-weight: 900;
            font-size: 15px;
            letter-spacing: 1px;
        }
        .header-brand .brand-sub {
            color: #888;
            font-size: 10px;
            letter-spacing: 0.5px;
            display: block;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .header-user {
            color: #ccc;
            font-size: 13px;
            text-align: right;
        }
        .header-user strong { color: #fff; display: block; }
        .btn-logout {
            background: transparent;
            border: 1px solid #444;
            color: #ccc;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-logout:hover { border-color: #CC0000; color: #CC0000; }

        /* Main */
        .main { max-width: 1100px; margin: 0 auto; padding: 32px 24px; }

        .page-header { margin-bottom: 28px; }
        .page-header h1 { font-size: 22px; font-weight: 800; color: #111; }
        .page-header p { color: #666; font-size: 14px; margin-top: 4px; }

        /* Stats grid */
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 24px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-top: 3px solid #eee;
        }
        .stat-card.red { border-top-color: #CC0000; }
        .stat-card.green { border-top-color: #16a34a; }
        .stat-card.yellow { border-top-color: #d97706; }
        .stat-card.blue { border-top-color: #2563eb; }
        .stat-label { font-size: 11px; font-weight: 700; color: #999; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 8px; }
        .stat-value { font-size: 36px; font-weight: 900; color: #111; line-height: 1; }
        .stat-sub { font-size: 12px; color: #888; margin-top: 4px; }

        /* Table */
        .section-title {
            font-size: 16px;
            font-weight: 800;
            color: #111;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .badge-count {
            background: #f0f0f0;
            color: #666;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .table-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
            margin-bottom: 28px;
        }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #f9f9f9;
            font-size: 11px;
            font-weight: 700;
            color: #999;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        tbody td {
            padding: 14px 20px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #f5f5f5;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #fafafa; }

        .status-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .pill-done { background: #dcfce7; color: #16a34a; }
        .pill-pending { background: #fef9c3; color: #854d0e; }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #999;
            font-size: 14px;
        }
        .empty-state .icon { font-size: 36px; margin-bottom: 12px; }

        /* Progress bar */
        .progress-wrap { background: #eee; border-radius: 4px; height: 6px; margin-top: 8px; }
        .progress-bar { background: #CC0000; border-radius: 4px; height: 6px; transition: width 0.3s; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-brand">
        <span class="shield">🛡️</span>
        <div>
            <span class="brand-name">GUARDIÃO DIGITAL</span>
            <span class="brand-sub">by M2 Cloud & Security</span>
        </div>
    </div>
    <div class="header-right">
        <div class="header-user">
            <strong>{{ $leader->name }}</strong>
            {{ $leader->company->name }}
        </div>
        <form method="POST" action="{{ route('leader.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Sair</button>
        </form>
    </div>
</div>

<div class="main">
    <div class="page-header">
        <h1>Painel de Treinamentos</h1>
        <p>Acompanhe o progresso da equipe no programa de conscientização em segurança.</p>
    </div>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-card blue">
            <div class="stat-label">Total de Colaboradores</div>
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

    @if($completed->count() > 0)
    <!-- Concluídos -->
    <div class="section-title">
        ✅ Concluídos
        <span class="badge-count">{{ $completed->count() }}</span>
    </div>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Departamento</th>
                    <th>Pontuação</th>
                    <th>Concluído em</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($completed as $collab)
                <tr>
                    <td><strong>{{ $collab->name ?? '—' }}</strong></td>
                    <td>{{ $collab->email }}</td>
                    <td>{{ $collab->department ?? '—' }}</td>
                    <td>
                        @if($collab->score !== null && $collab->total_questions)
                            {{ $collab->score }}/{{ $collab->total_questions }}
                            ({{ round($collab->score / $collab->total_questions * 100) }}%)
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $collab->completed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td><span class="status-pill pill-done">Concluído</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Pendentes -->
    <div class="section-title">
        ⏳ Pendentes
        <span class="badge-count">{{ $pending->count() }}</span>
    </div>
    <div class="table-card">
        @if($pending->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Departamento</th>
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
                    <td>{{ $collab->department ?? '—' }}</td>
                    <td>{{ $collab->invited_at?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $collab->first_access_at?->format('d/m/Y H:i') ?? 'Ainda não acessou' }}</td>
                    <td><span class="status-pill pill-pending">Pendente</span></td>
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
