<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Próxima missão — Guardião Digital</title>
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
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.6);
            z-index: 0;
            pointer-events: none;
        }
        body > * { position: relative; z-index: 1; }

        /* Header slim */
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

        .main {
            max-width: 760px;
            margin: 0 auto;
            padding: 56px 24px 40px;
            text-align: center;
        }

        /* Confete sutil no fundo (só apareceu de uma missão anterior) */
        @if($previousScenario)
        .confetti-wrap {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }
        .confetti {
            position: absolute;
            width: 8px;
            height: 14px;
            opacity: 0;
            animation: confettiFall 3.5s ease-out forwards;
        }
        @keyframes confettiFall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
        @endif

        /* Mascote com bounce de entrada */
        .mascote-celebra {
            display: inline-block;
            margin-bottom: 22px;
            animation: bounceIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .mascote-celebra img {
            width: 200px;
            height: auto;
            filter: drop-shadow(0 14px 28px rgba(204, 0, 0, 0.22));
            animation: floatY 3.5s ease-in-out infinite 0.8s;
        }

        /* Texto "missão concluída" pequeno em cima */
        @if($previousScenario)
        .completed-tag {
            display: inline-block;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: #fff;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 20px;
            margin-bottom: 16px;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.3);
            animation: popIn 0.5s ease;
        }
        .completed-tag::before { content: '✓ '; }
        @endif

        /* Card central da transição */
        .transition-card {
            background: #fff;
            border-radius: 22px;
            padding: 36px 40px 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
            animation: cardSlide 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s backwards;
        }
        .transition-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, #CC0000, #ff3344, #CC0000);
            background-size: 200% 100%;
            animation: shimmerBar 2.5s linear infinite;
        }

        .next-tag {
            font-size: 12px;
            font-weight: 800;
            color: #888;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }
        .next-tag .arrow {
            color: #CC0000;
            margin: 0 6px;
            animation: arrowMove 1.5s ease-in-out infinite;
            display: inline-block;
        }

        .platform-icon {
            font-size: 64px;
            margin-bottom: 14px;
            animation: iconPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.4s backwards;
            line-height: 1;
        }

        .transition-title {
            font-size: 30px;
            font-weight: 900;
            color: #111;
            line-height: 1.2;
            margin-bottom: 10px;
        }
        .transition-title span { color: #CC0000; }

        .transition-subtitle {
            font-size: 15px;
            color: #666;
            line-height: 1.6;
            max-width: 480px;
            margin: 0 auto 22px;
        }

        .mission-counter {
            display: inline-block;
            background: rgba(204, 0, 0, 0.08);
            border: 2px solid rgba(204, 0, 0, 0.15);
            border-radius: 14px;
            padding: 8px 18px;
            color: #CC0000;
            font-weight: 800;
            font-size: 13px;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        /* CTA + auto-advance */
        .cta-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            flex-wrap: wrap;
        }
        .btn-go {
            background: linear-gradient(135deg, #CC0000, #aa0000);
            color: #fff;
            padding: 16px 36px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 800;
            text-decoration: none;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(204, 0, 0, 0.32);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-go:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(204, 0, 0, 0.4);
        }
        .btn-go::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2.5s infinite;
        }

        .auto-advance {
            font-size: 12px;
            color: #999;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .auto-progress {
            width: 90px;
            height: 5px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }
        .auto-fill {
            height: 100%;
            background: #CC0000;
            width: 0%;
            border-radius: 3px;
        }

        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3) translateY(40px); }
            60% { transform: scale(1.1); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes cardSlide {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes iconPop {
            from { opacity: 0; transform: scale(0.4) rotate(-20deg); }
            to { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(0.6); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes arrowMove {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(4px); }
        }
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        @keyframes shimmerBar {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        @media (max-width: 720px) {
            .header { min-height: 70px; padding: 6px 16px; flex-direction: column; gap: 4px; }
            .brand-logo img { height: 90px; margin: -15px 0; }
            .header-user { text-align: center; }

            .main { padding: 36px 18px; }
            .transition-card { padding: 26px 22px; }
            .transition-title { font-size: 24px; }
            .platform-icon { font-size: 50px; }
            .mascote-celebra img { width: 150px; }
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

@if($previousScenario)
    <div class="confetti-wrap" id="confetti"></div>
@endif

<div class="main">
    @php
        $platforms = [
            'wapp'  => ['name' => 'WhatsApp',           'icon' => '📱', 'verb' => 'Atenção redobrada com mensagens diretas.'],
            'teams' => ['name' => 'Microsoft Teams',    'icon' => '💼', 'verb' => 'Comunicação corporativa também pode esconder armadilhas.'],
            'email' => ['name' => 'E-mail corporativo', 'icon' => '📧', 'verb' => 'A caixa de entrada é um dos alvos favoritos dos atacantes.'],
        ];
        $platform = $platforms[$scenario->platform] ?? ['name' => $scenario->platform, 'icon' => '⚡', 'verb' => 'Próximo desafio na área.'];

        $firstName = explode(' ', $collaborator->name ?? 'colaborador')[0];

        // Mascote alterna por plataforma pra dar variedade
        $mascotes = [
            'wapp'  => 'guardiao-defendendo.png',
            'teams' => 'guardiao-guerreiro.png',
            'email' => 'guardiao-explicando.png',
        ];
        $mascote = $mascotes[$scenario->platform] ?? 'guardiao-correndo.png';
    @endphp

    <div class="mascote-celebra">
        <img src="/images/mascote/{{ $mascote }}" alt="Guardião Digital">
    </div>

    @if($previousScenario)
        <div class="completed-tag">Missão anterior concluída</div>
    @endif

    <div class="transition-card">
        <div class="next-tag">
            @if($previousScenario)
                Próxima parada <span class="arrow">→</span>
            @else
                Sua primeira missão <span class="arrow">→</span>
            @endif
        </div>

        <div class="platform-icon">{{ $platform['icon'] }}</div>

        <h1 class="transition-title">
            Agora é hora do <span>{{ $platform['name'] }}</span>
        </h1>

        <p class="transition-subtitle">
            {{ $platform['verb'] }} Respire fundo, leia com calma e tome a decisão mais segura.
        </p>

        <div class="mission-counter">
            ⚡ Missão {{ $position }} de {{ $total }}
        </div>
    </div>

    <div class="cta-row">
        <a href="{{ route('training.show', $scenario->id) }}" id="goBtn" class="btn-go">
            Bora encarar →
        </a>
        <div class="auto-advance">
            <span>auto em <strong id="countdown">5</strong>s</span>
            <div class="auto-progress"><div class="auto-fill" id="autoFill"></div></div>
        </div>
    </div>
</div>

<script>
(function() {
    const DURATION = 5; // segundos
    const TARGET_URL = '{{ route("training.show", $scenario->id) }}';
    const countdown = document.getElementById('countdown');
    const fill = document.getElementById('autoFill');

    requestAnimationFrame(() => {
        fill.style.transition = 'width ' + DURATION + 's linear';
        fill.style.width = '100%';
    });

    let remaining = DURATION;
    const timer = setInterval(() => {
        remaining--;
        if (countdown) countdown.textContent = Math.max(remaining, 0);
        if (remaining <= 0) {
            clearInterval(timer);
            window.location.href = TARGET_URL;
        }
    }, 1000);
})();

@if($previousScenario)
// Confete simples (10 peças caindo) quando vem de uma missão concluída
(function() {
    const wrap = document.getElementById('confetti');
    if (!wrap) return;
    const colors = ['#CC0000', '#16a34a', '#fbbf24', '#3b82f6', '#a855f7'];
    for (let i = 0; i < 22; i++) {
        const c = document.createElement('div');
        c.className = 'confetti';
        c.style.left = Math.random() * 100 + '%';
        c.style.background = colors[Math.floor(Math.random() * colors.length)];
        c.style.animationDelay = Math.random() * 0.6 + 's';
        c.style.animationDuration = (2.5 + Math.random() * 1.5) + 's';
        wrap.appendChild(c);
    }
})();
@endif
</script>
</body>
</html>
