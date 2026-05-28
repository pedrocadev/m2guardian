<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Guardião Digital</title>
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
            background: rgba(255, 255, 255, 0.55);
            z-index: 0;
            pointer-events: none;
        }
        body > * { position: relative; z-index: 1; }

        /* Header slim com logo banner */
        .header {
            background:
                radial-gradient(ellipse at 20% 50%, #3a3a3a 0%, #1a1a1a 60%, #0a0a0a 100%);
            border-bottom: 3px solid #CC0000;
            padding: 8px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 80px;
            gap: 24px;
        }
        .brand-logo {
            display: flex;
            align-items: center;
            flex: 0 0 auto;
        }
        .brand-logo img {
            height: 130px;
            width: auto;
            display: block;
            mix-blend-mode: lighten;
            filter: drop-shadow(0 0 10px rgba(204, 0, 0, 0.25));
            margin: -25px 0; /* deixa a logo "transbordar" o header pra ficar maior sem aumentar a barra */
        }
        .header-user { color: #ddd; font-size: 13px; text-align: right; flex-shrink: 0; }
        .header-user strong { color: #fff; display: block; font-size: 14px; margin-bottom: 2px; }

        .main {
            max-width: 880px;
            margin: 0 auto;
            padding: 48px 24px 60px;
        }

        /* Hero — mascote olá + título lado a lado */
        .hero {
            display: flex;
            align-items: center;
            gap: 32px;
            margin-bottom: 36px;
            animation: slideIn 0.6s ease;
        }
        .hero-mascote {
            flex-shrink: 0;
        }
        .hero-mascote img {
            width: 220px;
            height: auto;
            filter: drop-shadow(0 12px 24px rgba(204, 0, 0, 0.2));
            animation: floatY 3.5s ease-in-out infinite;
        }
        .hero-text { flex: 1; min-width: 0; }
        .hero-text h1 {
            font-size: 34px;
            font-weight: 900;
            line-height: 1.15;
            margin-bottom: 10px;
            color: #111;
        }
        .hero-text h1 span { color: #CC0000; }
        .hero-text .subtitle {
            font-size: 15px;
            color: #666;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .content-card {
            background: #fff;
            border-radius: 16px;
            padding: 36px 40px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
            margin-bottom: 36px;
            animation: slideIn 0.7s ease;
        }
        .content-card p {
            font-size: 16px;
            line-height: 1.75;
            color: #333;
            margin-bottom: 16px;
        }
        .content-card p:last-of-type { margin-bottom: 0; }
        .content-card strong { color: #111; }
        .highlight { color: #CC0000; font-weight: 700; }

        .cta-wrap {
            text-align: center;
            margin-bottom: 24px;
            animation: slideIn 0.8s ease;
        }
        .btn-start {
            display: inline-block;
            background: #CC0000;
            color: #fff;
            padding: 18px 48px;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(204, 0, 0, 0.3);
            transition: all 0.2s ease;
            text-transform: uppercase;
        }
        .btn-start:hover {
            background: #aa0000;
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(204, 0, 0, 0.4);
        }
        .btn-start:active { transform: translateY(0); }
        .btn-start:disabled {
            background: #888;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ============================================
           Tela do mascote (estilo Duolingo) — full screen
           ============================================ */
        .duo-overlay {
            position: fixed;
            inset: 0;
            background-image: url('/images/mascote/bg-circuito.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 24px;
        }
        .duo-overlay::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.55);
            pointer-events: none;
        }
        .duo-overlay.show {
            display: flex;
            animation: fadeIn 0.4s ease;
        }
        .duo-stage {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 32px;
            max-width: 880px;
            width: 100%;
        }
        .duo-mascote {
            flex-shrink: 0;
            animation: duoEntry 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .duo-mascote img {
            width: 280px;
            height: auto;
            filter: drop-shadow(0 14px 30px rgba(0, 0, 0, 0.18));
            animation: floatY 3s ease-in-out infinite;
        }
        .duo-speech {
            background: #fff;
            border: 3px solid #CC0000;
            border-radius: 24px;
            padding: 28px 32px;
            font-size: 19px;
            color: #111;
            line-height: 1.55;
            max-width: 420px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            animation: bubblePop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s backwards;
        }
        .duo-speech::before {
            content: '';
            position: absolute;
            left: -16px;
            top: 50%;
            transform: translateY(-50%);
            border-style: solid;
            border-width: 14px 16px 14px 0;
            border-color: transparent #CC0000 transparent transparent;
        }
        .duo-speech::after {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            border-style: solid;
            border-width: 12px 14px 12px 0;
            border-color: transparent #fff transparent transparent;
        }
        .duo-speech strong { color: #CC0000; }
        .duo-speech p { margin-bottom: 10px; }
        .duo-speech p:last-child { margin-bottom: 0; }

        .duo-progress {
            position: absolute;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.92);
            padding: 10px 18px;
            border-radius: 30px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
            font-size: 13px;
            color: #666;
            font-weight: 600;
        }
        .duo-progress-bar {
            width: 160px;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }
        .duo-progress-fill {
            height: 100%;
            background: #CC0000;
            width: 0%;
            transition: width 1s linear;
            border-radius: 3px;
        }
        .duo-skip {
            background: none;
            border: none;
            color: #CC0000;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
            padding: 4px 10px;
            border-radius: 6px;
            transition: background 0.15s ease;
        }
        .duo-skip:hover { background: rgba(204, 0, 0, 0.08); }

        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes duoEntry {
            from { opacity: 0; transform: scale(0.6) translateX(-40px); }
            to { opacity: 1; transform: scale(1) translateX(0); }
        }
        @keyframes bubblePop {
            from { opacity: 0; transform: scale(0.7); }
            to { opacity: 1; transform: scale(1); }
        }

        @media (max-width: 720px) {
            .header { min-height: 70px; padding: 6px 16px; flex-direction: column; gap: 4px; }
            .brand-logo img { height: 90px; margin: -15px 0; }
            .header-user { text-align: center; }
            .hero { flex-direction: column; text-align: center; gap: 20px; }
            .hero-mascote img { width: 170px; }
            .hero-text h1 { font-size: 26px; }
            .content-card { padding: 24px 22px; }
            .content-card p { font-size: 15px; }
            .btn-start { padding: 16px 32px; font-size: 15px; }

            .duo-stage { flex-direction: column; gap: 18px; }
            .duo-mascote img { width: 200px; }
            .duo-speech { font-size: 16px; padding: 22px 24px; max-width: 100%; }
            .duo-speech::before, .duo-speech::after { display: none; }
        }
    </style>
</head>
<body>
<div class="header">
    <div class="brand-logo">
        <img src="/images/logo-guardiao.png" alt="Guardião Digital — by M2 Cloud & Security"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div style="display:none; align-items:center; gap:10px;">
            <span style="font-size:22px;">🛡️</span>
            <div>
                <span style="color:#fff; font-weight:900; font-size:15px; letter-spacing:1px;">GUARDIÃO DIGITAL</span>
                <span style="color:#888; font-size:10px; letter-spacing:0.5px; display:block;">by M2 Cloud &amp; Security</span>
            </div>
        </div>
    </div>
    <div class="header-user">
        <strong>{{ $collaborator->name ?? $collaborator->email }}</strong>
        {{ $collaborator->company->name }}
    </div>
</div>

<div class="main" id="welcome-main">
    <div class="hero">
        <div class="hero-mascote">
            <img src="/images/mascote/guardiao-ola.png" alt="Guardião Digital">
        </div>
        <div class="hero-text">
            <div class="subtitle">Sua jornada começa aqui</div>
            <h1>Bem-vindo ao <span>Guardião Digital</span></h1>
        </div>
    </div>

    <div class="content-card">
        <p>
            Nesta jornada, você vai enfrentar <strong>situações simuladas de segurança da informação</strong>
            em canais que fazem parte da rotina corporativa, como <span class="highlight">WhatsApp</span>,
            <span class="highlight">e-mail</span> e <span class="highlight">comunicadores internos</span>.
        </p>
        <p>
            A cada escolha, o Guardião indicará se sua decisão foi segura, explicará o motivo e mostrará
            qual seria a melhor conduta.
        </p>
        <p>
            Você está sendo preparado para se tornar um <strong>guardião digital certificado</strong>. Vamos lá?
        </p>
    </div>

    <div class="cta-wrap">
        <a href="{{ route('training.how-it-works') }}" id="start-btn" class="btn-start">Começar minha jornada →</a>
    </div>
</div>

{{-- Overlay estilo Duolingo (aparece após clique no botão) --}}
<div class="duo-overlay" id="duo-overlay">
    <div class="duo-stage">
        <div class="duo-mascote">
            <img src="/images/mascote/guardiao-explicando.png" alt="Guardião">
        </div>
        <div class="duo-speech">
            <p>Não se preocupe: aqui o <strong>erro também ensina</strong>.</p>
            <p>O importante é aprender antes que aconteça de verdade.</p>
        </div>
    </div>
    <div class="duo-progress">
        <span id="duo-counter">10</span>s
        <div class="duo-progress-bar">
            <div class="duo-progress-fill" id="duo-fill"></div>
        </div>
        <button type="button" id="duo-skip" class="duo-skip">Pular →</button>
    </div>
</div>

<script>
(function() {
    const DURATION = 10; // segundos
    const NEXT_URL = '{{ route("training.how-it-works") }}';
    const btn = document.getElementById('start-btn');
    const main = document.getElementById('welcome-main');
    const overlay = document.getElementById('duo-overlay');
    const counter = document.getElementById('duo-counter');
    const fill = document.getElementById('duo-fill');
    const skip = document.getElementById('duo-skip');

    let timer = null;
    let remaining = DURATION;

    function advance() {
        if (timer) clearInterval(timer);
        window.location.href = NEXT_URL;
    }

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        main.style.display = 'none';
        overlay.classList.add('show');

        requestAnimationFrame(() => {
            fill.style.transitionDuration = DURATION + 's';
            fill.style.width = '100%';
        });

        timer = setInterval(() => {
            remaining--;
            counter.textContent = remaining;
            if (remaining <= 0) {
                advance();
            }
        }, 1000);
    });

    skip.addEventListener('click', advance);
})();
</script>
</body>
</html>
