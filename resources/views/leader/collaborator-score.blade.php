<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postura — {{ $collaborator->name ?? $collaborator->email }} — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f4f5f7;
            color: #111;
            min-height: 100vh;
        }
        .header {
            background: #111; color: #fff;
            padding: 14px 24px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 3px solid #CC0000;
        }
        .header-brand { display: flex; align-items: center; }
        .header-brand img { height: 44px; width: auto; display: block; filter: drop-shadow(0 4px 14px rgba(204, 0, 0, 0.35)); }
        .back-link {
            color: #ccc; text-decoration: none; font-size: 12px;
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 6px;
            transition: background 0.15s;
        }
        .back-link:hover { background: rgba(255,255,255,0.1); color: #fff; }

        .main { max-width: 900px; margin: 32px auto; padding: 0 24px; }
        .page-header { margin-bottom: 22px; }
        .page-header h1 { font-size: 22px; font-weight: 800; color: #111; margin-bottom: 4px; }
        .page-header p { font-size: 13px; color: #666; }

        .empty-data {
            background: #fff;
            border-radius: 12px;
            padding: 60px 24px;
            text-align: center;
            color: #999;
        }
        .empty-data .icon { font-size: 48px; margin-bottom: 12px; }
        .empty-data h3 { font-size: 16px; font-weight: 700; color: #555; margin-bottom: 6px; }
        .empty-data p { font-size: 13px; line-height: 1.5; }

        /* ─── Tentativas ─── */
        .attempt-block {
            background: #fff;
            border-radius: 14px;
            padding: 24px 26px;
            margin-bottom: 22px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .attempt-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 16px;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }
        .attempt-title {
            font-size: 15px;
            font-weight: 800;
            color: #111;
            letter-spacing: 0.2px;
        }
        .attempt-title .num {
            display: inline-block;
            background: #111;
            color: #fff;
            font-size: 11px;
            padding: 2px 10px;
            border-radius: 20px;
            margin-right: 8px;
            letter-spacing: 1px;
        }
        .attempt-meta {
            font-size: 12px;
            color: #888;
            margin-top: 2px;
            font-weight: 500;
        }
        .attempt-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .attempt-badge.passed {
            background: #d1fae5;
            color: #065f46;
        }
        .attempt-badge.failed {
            background: #fee2e2;
            color: #991b1b;
        }
        .attempt-badge.incomplete {
            background: #fef3c7;
            color: #92400e;
        }

        /* Nota "última tentativa" no bloco atual */
        .current-tag {
            display: inline-block;
            background: #CC0000;
            color: #fff;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 1.5px;
            padding: 2px 8px;
            border-radius: 3px;
            margin-left: 8px;
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="header-brand">
        <img src="{{ asset('images/backgrounds/Logo_guardiao.png') }}" alt="Guardião Digital">
    </div>
    <a href="{{ route('leader.dashboard') }}" class="back-link">← Voltar ao Dashboard</a>
</div>

<div class="main">
    <div class="page-header">
        <h1>{{ $collaborator->name ?? $collaborator->email }}</h1>
        <p>
            {{ $collaborator->email }} · {{ $collaborator->department ?: 'Sem departamento' }}
            @if($totalAttempts > 1)
                · <strong>{{ $totalAttempts }} tentativas</strong>
            @endif
        </p>
    </div>

    @if($totalAttempts === 0)
        <div class="empty-data">
            <div class="icon">📊</div>
            <h3>Sem dados de treinamento</h3>
            <p>Este colaborador ainda não iniciou o treinamento.</p>
        </div>
    @else
        @foreach($attempts as $i => $attempt)
            @php
                $session = $attempt['session'];
                $scoreData = $attempt['score_data'];
                $isCurrent = $i === 0;
                $badgeClass = !$session->isCompleted()
                    ? 'incomplete'
                    : ($session->passed ? 'passed' : 'failed');
                $badgeText = !$session->isCompleted()
                    ? 'EM ANDAMENTO'
                    : ($session->passed ? 'APROVADO' : 'REPROVADO');
            @endphp

            <div class="attempt-block">
                <div class="attempt-header">
                    <div>
                        <div class="attempt-title">
                            <span class="num">TENTATIVA {{ $attempt['attempt_number'] }}</span>
                            {{ $session->completed_at?->format('d/m/Y H:i') ?? 'Em andamento' }}
                            @if($isCurrent && $totalAttempts > 1)
                                <span class="current-tag">MAIS RECENTE</span>
                            @endif
                        </div>
                        <div class="attempt-meta">
                            @if($session->isCompleted())
                                Iniciada {{ $session->started_at->format('d/m/Y') }} · Duração {{ $session->duration_seconds ? floor($session->duration_seconds / 60) . ' min' : '—' }}
                            @else
                                Iniciada {{ $session->started_at->format('d/m/Y H:i') }} · ainda não concluída
                            @endif
                        </div>
                    </div>
                    <div class="attempt-badge {{ $badgeClass }}">{{ $badgeText }}</div>
                </div>

                @if($scoreData['total'] === 0)
                    <div class="empty-data" style="padding: 30px 20px;">
                        <p>Sem respostas registradas nessa tentativa.</p>
                    </div>
                @else
                    @include('partials.posture-detail', ['scoreData' => $scoreData])
                @endif
            </div>
        @endforeach
    @endif
</div>
</body>
</html>
