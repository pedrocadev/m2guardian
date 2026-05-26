<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treinamento — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f5f7; color: #111; min-height: 100vh; }

        .header { background: #111; border-bottom: 3px solid #CC0000; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; height: 60px; }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand-name { color: #fff; font-weight: 900; font-size: 15px; letter-spacing: 1px; }
        .brand-sub { color: #888; font-size: 10px; letter-spacing: 0.5px; display: block; }
        .header-user { color: #ccc; font-size: 13px; text-align: right; }
        .header-user strong { color: #fff; display: block; }

        .main { max-width: 760px; margin: 0 auto; padding: 40px 24px; }

        .welcome { text-align: center; margin-bottom: 40px; }
        .welcome h1 { font-size: 26px; font-weight: 900; margin-bottom: 8px; }
        .welcome p { color: #666; font-size: 15px; line-height: 1.6; max-width: 520px; margin: 0 auto; }

        .progress-bar-wrap { background: #e5e7eb; border-radius: 8px; height: 8px; margin: 24px 0 8px; }
        .progress-bar-fill { background: #CC0000; border-radius: 8px; height: 8px; transition: width 0.4s; }
        .progress-label { font-size: 12px; color: #888; text-align: right; margin-bottom: 32px; }

        .scenarios { display: flex; flex-direction: column; gap: 16px; margin-bottom: 40px; }
        .scenario-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid #e5e7eb;
            text-decoration: none;
            color: inherit;
        }
        .scenario-card.done { border-left-color: #16a34a; opacity: 0.75; }
        .scenario-card.active { border-left-color: #CC0000; }
        .scenario-card.locked { opacity: 0.45; pointer-events: none; }

        .s-avatar { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; }
        .s-info { flex: 1; }
        .s-label { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .s-preview { font-size: 13px; color: #888; }
        .s-platform { font-size: 10px; font-weight: 700; letter-spacing: 1px; color: #aaa; text-transform: uppercase; margin-top: 4px; }
        .s-status { font-size: 12px; font-weight: 700; flex-shrink: 0; }
        .s-status.done { color: #16a34a; }
        .s-status.active { color: #CC0000; }
        .s-status.locked { color: #ccc; }

        .btn-start {
            display: block;
            width: 100%;
            background: #CC0000;
            color: #fff;
            text-align: center;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 0.5px;
        }
        .btn-start:hover { background: #aa0000; }

        .info-box { background: #fff8e1; border: 1px solid #ffe082; border-radius: 8px; padding: 14px 18px; font-size: 13px; color: #795548; margin-bottom: 24px; }
    </style>
</head>
<body>
<div class="header">
    <div class="brand">
        <span style="font-size:22px;">🛡️</span>
        <div>
            <span class="brand-name">GUARDIÃO DIGITAL</span>
            <span class="brand-sub">by M2 Cloud & Security</span>
        </div>
    </div>
    <div class="header-user">
        <strong>{{ $collaborator->name ?? $collaborator->email }}</strong>
        {{ $collaborator->company->name }}
    </div>
</div>

<div class="main">
    <div class="welcome">
        <h1>Bem-vindo ao Treinamento 🛡️</h1>
        <p>Você vai passar por <strong>{{ $scenarios->count() }} cenários</strong> de situações reais de segurança. Em cada um, tome a decisão certa e aprenda a identificar ameaças.</p>
    </div>

    @php
        $completedCount = $answeredIds->count();
        $totalCount = $scenarios->count();
        $pct = $totalCount > 0 ? round($completedCount / $totalCount * 100) : 0;
    @endphp

    <div class="progress-bar-wrap">
        <div class="progress-bar-fill" style="width: {{ $pct }}%"></div>
    </div>
    <div class="progress-label">{{ $completedCount }} de {{ $totalCount }} cenários concluídos</div>

    @if($completedCount === 0)
    <div class="info-box">
        ⏱️ Duração estimada: <strong>10 a 15 minutos</strong>. Leia cada situação com atenção antes de responder.
    </div>
    @endif

    <div class="scenarios">
        @foreach($scenarios as $i => $scenario)
            @php
                $isDone = $answeredIds->contains($scenario->id);
                $isNext = $nextScenario?->id === $scenario->id;
                $isLocked = !$isDone && !$isNext;
                $cardClass = $isDone ? 'done' : ($isNext ? 'active' : 'locked');
                $href = $isNext ? route('training.show', $scenario->id) : '#';
                $statusLabel = $isDone ? '✔ Concluído' : ($isNext ? '→ Iniciar' : '🔒 Bloqueado');
                $platforms = ['wapp' => 'WhatsApp', 'teams' => 'Microsoft Teams', 'email' => 'E-mail'];
            @endphp
            <a href="{{ $href }}" class="scenario-card {{ $cardClass }}">
                <div class="s-avatar" style="background: {{ $scenario->bg_color }}20;">
                    {{ $scenario->avatar }}
                </div>
                <div class="s-info">
                    <div class="s-label">{{ $i + 1 }}. {{ $scenario->label }}</div>
                    <div class="s-preview">{{ $scenario->preview }}</div>
                    <div class="s-platform">{{ $platforms[$scenario->platform] ?? $scenario->platform }}</div>
                </div>
                <div class="s-status {{ $cardClass }}">{{ $statusLabel }}</div>
            </a>
        @endforeach
    </div>

    @if($nextScenario)
        <a href="{{ route('training.show', $nextScenario->id) }}" class="btn-start">
            {{ $completedCount === 0 ? 'Iniciar Treinamento →' : 'Continuar Treinamento →' }}
        </a>
    @endif
</div>
</body>
</html>
