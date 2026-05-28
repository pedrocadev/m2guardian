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
            background: rgba(255, 255, 255, 0.55);
            z-index: 0;
            pointer-events: none;
        }
        body > * { position: relative; z-index: 1; }

        /* Header */
        .header {
            background: radial-gradient(ellipse at 20% 50%, #3a3a3a 0%, #1a1a1a 60%, #0a0a0a 100%);
            border-bottom: 3px solid #CC0000;
            padding: 8px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 80px;
            gap: 24px;
        }
        .brand-logo { display: flex; align-items: center; flex: 0 0 auto; }
        .brand-logo img {
            height: 130px;
            width: auto;
            display: block;
            mix-blend-mode: lighten;
            filter: drop-shadow(0 0 10px rgba(204, 0, 0, 0.25));
            margin: -25px 0;
        }
        .brand-fallback { display: none; align-items: center; gap: 10px; }
        .brand-name { color: #fff; font-weight: 900; font-size: 15px; letter-spacing: 1px; }
        .brand-sub { color: #888; font-size: 10px; letter-spacing: 0.5px; display: block; }
        .header-user { color: #ddd; font-size: 13px; text-align: right; flex-shrink: 0; }
        .header-user strong { color: #fff; display: block; font-size: 14px; margin-bottom: 2px; }

        .main { max-width: 720px; margin: 0 auto; padding: 36px 24px 60px; }

        /* Mascote hero */
        .mascote-hero {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 22px;
            margin-bottom: 24px;
            animation: slideIn 0.6s ease;
        }
        .mascote-hero img {
            width: 150px;
            height: auto;
            filter: drop-shadow(0 10px 20px rgba(204, 0, 0, 0.18));
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
            max-width: 300px;
            line-height: 1.5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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

        /* Progresso — dots gamificados */
        .progress-block {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 14px;
            padding: 16px 22px;
            margin-bottom: 28px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            animation: slideIn 0.7s ease;
        }
        .progress-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 13px;
        }
        .progress-top .left {
            font-weight: 800;
            color: #111;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .progress-top .right {
            color: #888;
            font-weight: 600;
        }
        .progress-top .right strong { color: #CC0000; }

        .progress-dots {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .dot {
            flex: 1;
            min-width: 24px;
            height: 10px;
            border-radius: 5px;
            background: #e5e7eb;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .dot.done {
            background: linear-gradient(90deg, #16a34a, #22c55e);
            box-shadow: 0 0 8px rgba(34, 197, 94, 0.4);
        }
        .dot.current {
            background: linear-gradient(90deg, #CC0000, #ff3344);
            box-shadow: 0 0 12px rgba(204, 0, 0, 0.5);
            animation: pulse 1.5s ease-in-out infinite;
        }

        /* MISSION POPUP CARD — o cenário "ativo" */
        .mission-popup {
            background: #fff;
            border-radius: 22px;
            padding: 0;
            box-shadow: 0 20px 50px rgba(204, 0, 0, 0.18), 0 6px 16px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            animation: missionPopIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 2px solid #CC0000;
        }

        .mission-banner {
            background: linear-gradient(135deg, #CC0000 0%, #8a0000 100%);
            color: #fff;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .mission-banner .badge {
            background: #fff;
            color: #CC0000;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 900;
        }

        .mission-body {
            padding: 28px 28px 26px;
            display: flex;
            gap: 22px;
            align-items: flex-start;
        }
        .mission-avatar {
            width: 78px;
            height: 78px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            flex-shrink: 0;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .mission-avatar::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 22px;
            border: 2px dashed rgba(204, 0, 0, 0.3);
            animation: rotateBorder 12s linear infinite;
        }

        .mission-info { flex: 1; min-width: 0; }
        .mission-platform {
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #CC0000;
            text-transform: uppercase;
            margin-bottom: 6px;
            display: inline-block;
            background: rgba(204, 0, 0, 0.08);
            padding: 3px 10px;
            border-radius: 10px;
        }
        .mission-title {
            font-size: 22px;
            font-weight: 900;
            color: #111;
            line-height: 1.2;
            margin-bottom: 8px;
        }
        .mission-preview {
            font-size: 14px;
            color: #555;
            line-height: 1.55;
        }

        .mission-footer {
            padding: 18px 28px 26px;
            border-top: 1px dashed #f0f0f0;
        }
        .btn-accept {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #CC0000 0%, #aa0000 100%);
            color: #fff;
            text-align: center;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 800;
            text-decoration: none;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 6px 16px rgba(204, 0, 0, 0.3);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-accept:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(204, 0, 0, 0.4);
        }
        .btn-accept:active { transform: translateY(0); }

        .btn-accept::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            animation: shimmer 2.5s infinite;
        }

        /* Estado concluído (fallback raro) */
        .all-done {
            text-align: center;
            background: #fff;
            border-radius: 18px;
            padding: 40px 28px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        }
        .all-done h2 { font-size: 26px; font-weight: 900; margin-bottom: 12px; }
        .all-done p { color: #666; font-size: 15px; line-height: 1.6; }

        /* Animações */
        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes missionPopIn {
            0% { opacity: 0; transform: scale(0.7) translateY(40px); }
            60% { transform: scale(1.04) translateY(-4px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 12px rgba(204, 0, 0, 0.5); }
            50% { box-shadow: 0 0 22px rgba(204, 0, 0, 0.85); }
        }
        @keyframes rotateBorder {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        @media (max-width: 720px) {
            .header { min-height: 70px; padding: 6px 16px; flex-direction: column; gap: 4px; }
            .brand-logo img { height: 90px; margin: -15px 0; }
            .header-user { text-align: center; }

            .mascote-hero { flex-direction: column; gap: 12px; }
            .mascote-hero img { width: 130px; }
            .mascote-hero .speech::before, .mascote-hero .speech::after { display: none; }

            .mission-body { flex-direction: column; align-items: center; text-align: center; gap: 16px; padding: 24px 20px 20px; }
            .mission-title { font-size: 19px; }
            .mission-banner { font-size: 11px; padding: 12px 18px; }
        }
    </style>
</head>
<body>
<div class="header">
    <div class="brand-logo">
        <img src="/images/logo-guardiao.png" alt="Guardião Digital — by M2 Cloud & Security"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="brand-fallback">
            <span style="font-size:22px;">🛡️</span>
            <div>
                <span class="brand-name">GUARDIÃO DIGITAL</span>
                <span class="brand-sub">by M2 Cloud &amp; Security</span>
            </div>
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
        $nextPosition = $nextScenario
            ? $scenarios->search(fn($s) => $s->id === $nextScenario->id) + 1
            : null;
        $platforms = ['wapp' => 'WhatsApp', 'teams' => 'Microsoft Teams', 'email' => 'E-mail'];

        // Mensagem do mascote contextual
        if ($completed === 0) {
            $mascote = 'guardiao-correndo.png';
            $speech = "Pronto, <strong>{$firstName}</strong>? Sua primeira missão chegou! 🛡️";
        } elseif (!$allDone) {
            $mascote = 'guardiao-vitoria.png';
            $remaining = $totalScenarios - $completed;
            $speech = "Mandou bem, <strong>{$firstName}</strong>! Próxima missão na área. Faltam <strong>{$remaining}</strong>. 💪";
        } else {
            $mascote = 'guardiao-medalha.png';
            $speech = "Parabéns, <strong>{$firstName}</strong>! Você concluiu todas as missões! 🏆";
        }
    @endphp

    <div class="mascote-hero">
        <img src="/images/mascote/{{ $mascote }}" alt="Guardião Digital">
        <div class="speech">{!! $speech !!}</div>
    </div>

    @unless($allDone)
        <div class="progress-block">
            <div class="progress-top">
                <span class="left">Progresso</span>
                <span class="right"><strong>{{ $completed }}</strong> / {{ $totalScenarios }} missões</span>
            </div>
            <div class="progress-dots">
                @for($i = 1; $i <= $totalScenarios; $i++)
                    @php
                        $dotClass = $i <= $completed ? 'done' : ($i === $completed + 1 ? 'current' : '');
                    @endphp
                    <div class="dot {{ $dotClass }}"></div>
                @endfor
            </div>
        </div>
    @endunless

    @if($nextScenario)
        <div class="mission-popup">
            <div class="mission-banner">
                <span>⚡ Missão {{ $nextPosition }} de {{ $totalScenarios }}</span>
                <span class="badge">{{ $platforms[$nextScenario->platform] ?? $nextScenario->platform }}</span>
            </div>

            <div class="mission-body">
                <div class="mission-avatar" style="background: {{ $nextScenario->bg_color }}25;">
                    {{ $nextScenario->avatar }}
                </div>
                <div class="mission-info">
                    <span class="mission-platform">Cenário simulado</span>
                    <h2 class="mission-title">{{ $nextScenario->label }}</h2>
                    <p class="mission-preview">{{ $nextScenario->preview }}</p>
                </div>
            </div>

            <div class="mission-footer">
                <a href="{{ route('training.show', $nextScenario->id) }}" class="btn-accept">
                    Aceitar missão →
                </a>
            </div>
        </div>
    @else
        <div class="all-done">
            <h2>🏆 Treinamento concluído!</h2>
            <p>Você completou todas as missões. Seu resultado já foi registrado.</p>
        </div>
    @endif
</div>
</body>
</html>
