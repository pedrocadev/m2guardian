<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treinamento — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            color: #111;
            min-height: 100vh;
            background-image: url('/images/mascote/bg-circuito.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.55);
            z-index: 0;
            pointer-events: none;
        }
        body > * { position: relative; z-index: 1; }

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

        /* Mascote — boas-vindas */
        .mascote-hero {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            margin-bottom: 16px;
            animation: slideIn 0.6s ease;
        }
        .mascote-hero img {
            width: 180px;
            height: auto;
            filter: drop-shadow(0 10px 20px rgba(204,0,0,0.18));
            animation: floatY 3.5s ease-in-out infinite;
        }
        .mascote-hero .speech {
            background: #fff;
            border: 2px solid #CC0000;
            border-radius: 16px;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #111;
            position: relative;
            max-width: 280px;
            line-height: 1.5;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .mascote-hero .speech::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 50%;
            transform: translateY(-50%);
            border-style: solid;
            border-width: 8px 10px 8px 0;
            border-color: transparent #CC0000 transparent transparent;
        }
        .mascote-hero .speech::after {
            content: '';
            position: absolute;
            left: -7px;
            top: 50%;
            transform: translateY(-50%);
            border-style: solid;
            border-width: 7px 9px 7px 0;
            border-color: transparent #fff transparent transparent;
        }
        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 600px) {
            .mascote-hero { flex-direction: column; gap: 12px; }
            .mascote-hero img { width: 140px; }
            .mascote-hero .speech::before, .mascote-hero .speech::after { display: none; }
        }
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
    @php
        $firstName = explode(' ', $collaborator->name ?? 'colaborador')[0];
        $completed = $answeredIds->count();
        $totalScenarios = $scenarios->count();
        $allDone = $completed >= $totalScenarios && $totalScenarios > 0;
    @endphp

    @if($completed === 0)
        {{-- INÍCIO: Mascote correndo + saudação de boas-vindas --}}
        <div class="mascote-hero">
            <img src="/images/mascote/guardiao-correndo.png" alt="Guardião Digital">
            <div class="speech">
                Olá <strong>{{ $firstName }}</strong>! Vamos juntos enfrentar os ataques digitais? 🛡️
            </div>
        </div>

        <div class="welcome">
            <h1>Bem-vindo ao Treinamento</h1>
            <p>Você vai passar por <strong>{{ $totalScenarios }} cenários</strong> de situações reais de segurança. Em cada um, tome a decisão certa e aprenda a identificar ameaças.</p>
        </div>

    @elseif(!$allDone)
        {{-- EM ANDAMENTO: Mascote vitória + incentivo --}}
        <div class="mascote-hero">
            <img src="/images/mascote/guardiao-vitoria.png" alt="Continue!">
            <div class="speech">
                Continue assim, <strong>{{ $firstName }}</strong>! Você já completou <strong>{{ $completed }} de {{ $totalScenarios }}</strong>. Faltam só mais alguns! 💪
            </div>
        </div>

        <div class="welcome">
            <h1>Continue seu Treinamento</h1>
            <p>Você está indo bem — faltam <strong>{{ $totalScenarios - $completed }} cenário(s)</strong> para concluir. Cada um pode te ensinar algo novo sobre proteção digital.</p>
        </div>

    @else
        {{-- CONCLUÍDO (fallback raro — geralmente redireciona para /concluido) --}}
        <div class="mascote-hero">
            <img src="/images/mascote/guardiao-medalha.png" alt="Parabéns!">
            <div class="speech">
                Parabéns, <strong>{{ $firstName }}</strong>! Você finalizou todos os <strong>{{ $totalScenarios }} cenários</strong> do treinamento! 🎉
            </div>
        </div>

        <div class="welcome">
            <h1>Treinamento Concluído!</h1>
            <p>Você completou todos os cenários. Seus resultados já foram registrados e seu líder foi notificado.</p>
        </div>
    @endif

    @php
        $pct = $totalScenarios > 0 ? round($completed / $totalScenarios * 100) : 0;
    @endphp

    @unless($allDone)
        <div class="progress-bar-wrap">
            <div class="progress-bar-fill" style="width: {{ $pct }}%"></div>
        </div>
        <div class="progress-label">{{ $completed }} de {{ $totalScenarios }} cenários concluídos</div>
    @endunless

    @if($completed === 0)
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
            {{ $completed === 0 ? 'Iniciar Treinamento →' : 'Continuar Treinamento →' }}
        </a>
    @endif
</div>
</body>
</html>
