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
        <p>{{ $collaborator->email }} · {{ $collaborator->department ?: 'Sem departamento' }} · Concluiu em {{ $collaborator->completed_at?->format('d/m/Y H:i') ?? '—' }}</p>
    </div>

    @if($scoreData['total'] === 0)
        <div class="empty-data">
            <div class="icon">📊</div>
            <h3>Sem dados de treinamento</h3>
            <p>Este colaborador ainda não concluiu o treinamento.</p>
        </div>
    @else
        @include('partials.posture-detail', ['scoreData' => $scoreData])
    @endif
</div>
</body>
</html>
