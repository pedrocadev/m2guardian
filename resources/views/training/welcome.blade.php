<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo à Jornada Guardião — Guardião Digital</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { min-height: 100%; }
        body {
            font-family: 'Segoe UI', 'Inter', Arial, sans-serif;
            color: #e8edf2;   /* cor gelo principal (em vez de branco forte) */
            background: #0a0a0a;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Background com imagem ──────────────────────── */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: url('/images/backgrounds/training-welcome.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
        }

        /* ── Header ─────────────────────────────────────── */
        .brand-header {
            display: flex;
            justify-content: center;
            padding: 32px 24px 24px;
            position: relative;
            z-index: 2;
        }
        .brand-header img {
            height: clamp(72px, 8vw, 120px);
            width: auto;
            display: block;
            filter: drop-shadow(0 6px 22px rgba(204, 0, 0, 0.45));
        }

        /* ── Container principal ───────────────────────── */
        .main {
            max-width: 1400px;
            margin: 16px auto 0;
            padding: 0 24px 64px;
            position: relative;
            z-index: 1;
        }

        .stage {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 680px;
        }

        /* Mascote à esquerda */
        .mascot {
            position: absolute;
            left: -60px;
            bottom: 0;
            width: 340px;
            height: auto;
            filter: drop-shadow(0 30px 50px rgba(0, 0, 0, 0.6)) drop-shadow(0 0 30px rgba(204, 0, 0, 0.2));
            pointer-events: none;
            z-index: 3;
            animation: mascotFloat 4s ease-in-out infinite;
        }
        @keyframes mascotFloat {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-12px); }
        }

        /* Trophy à direita */
        .trophy {
            position: absolute;
            right: -70px;
            bottom: 40px;
            width: 130px; height: 130px;
            border: 2px solid rgba(255,255,255,0.35);
            border-radius: 50%;
            display: grid; place-items: center;
            background: radial-gradient(circle at center, rgba(204,0,0,0.15), transparent 70%);
            box-shadow: 0 0 40px rgba(204,0,0,0.25), inset 0 0 20px rgba(255,255,255,0.05);
            z-index: 3;
            animation: trophyShine 3s ease-in-out infinite;
        }
        .trophy svg { width: 64px; height: 64px; }
        @keyframes trophyShine {
            0%, 100% { box-shadow: 0 0 40px rgba(204,0,0,0.25), inset 0 0 20px rgba(255,255,255,0.05); }
            50%      { box-shadow: 0 0 60px rgba(255,90,90,0.45), inset 0 0 30px rgba(255,255,255,0.1); }
        }

        /* Card principal */
        .card {
            position: relative;
            background:
                radial-gradient(800px 400px at 50% 0%, rgba(204,0,0,0.10), transparent 60%),
                linear-gradient(180deg, rgba(20, 8, 12, 0.85), rgba(10, 5, 8, 0.92));
            backdrop-filter: blur(2px);
            border: 1px solid rgba(204, 0, 0, 0.25);
            border-radius: 28px;
            padding: 70px 90px 60px;
            width: 100%;
            max-width: 1100px;
            box-shadow:
                0 30px 80px rgba(0,0,0,0.6),
                0 0 60px rgba(204,0,0,0.12),
                inset 0 1px 0 rgba(255,255,255,0.05);
            z-index: 2;
        }
        .card-shield-badge {
            position: absolute;
            top: -22px; left: 50%;
            transform: translateX(-50%);
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #1a0000, #330000);
            border: 2px solid #CC0000;
            border-radius: 50%;
            display: grid; place-items: center;
            box-shadow: 0 0 20px rgba(204,0,0,0.6);
        }
        .card-shield-badge svg { width: 22px; height: 22px; fill: #CC0000; }

        .card h1 {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.12;
            text-align: center;
            margin-bottom: 26px;
            letter-spacing: -0.8px;
            color: #e8edf2;
        }
        .card h1 .highlight {
            display: block;
            background: linear-gradient(135deg, #ff4444 0%, #CC0000 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
        }

        .card .lead {
            font-size: 17px;
            color: #d4d8dd;
            text-align: center;
            line-height: 1.65;
            max-width: 720px;
            margin: 0 auto 26px;
        }

        .card .highlight-msg {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 700;
            color: #ff6666;
            text-align: center;
            margin-bottom: 32px;
        }
        .highlight-msg svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* Botão CTA */
        .btn-cta {
            display: block;
            width: 100%;
            max-width: 440px;
            margin: 0 auto 48px;
            background: linear-gradient(135deg, #CC0000 0%, #aa0000 100%);
            color: #fff;
            border: none;
            padding: 18px 36px;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(204,0,0,0.45), inset 0 1px 0 rgba(255,255,255,0.2);
            transition: all 0.2s ease;
            font-family: inherit;
            letter-spacing: 0.3px;
        }
        .btn-cta:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 12px 32px rgba(204,0,0,0.55), inset 0 1px 0 rgba(255,255,255,0.25);
        }
        .btn-cta:active { transform: translateY(0) scale(0.99); }

        /* Grid de features */
        .features {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }
        .feature {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(204, 0, 0, 0.25);
            border-radius: 14px;
            padding: 18px 18px 20px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: default;
            position: relative;
            overflow: hidden;
        }
        .feature::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(120px 80px at 50% 0%, rgba(204,0,0,0.25), transparent 70%);
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        .feature:hover {
            transform: translateY(-8px);
            border-color: rgba(204, 0, 0, 0.65);
            box-shadow: 0 16px 30px rgba(0,0,0,0.4), 0 0 20px rgba(204,0,0,0.2);
        }
        .feature:hover::before { opacity: 1; }

        .feature-icon {
            width: 42px; height: 42px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(204,0,0,0.18), rgba(255,90,90,0.12));
            border: 1px solid rgba(204,0,0,0.45);
            display: grid; place-items: center;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            color: #ff7878;
        }
        .feature-icon svg { width: 22px; height: 22px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .feature:hover .feature-icon {
            transform: scale(1.15) rotate(-6deg);
            background: linear-gradient(135deg, rgba(204,0,0,0.35), rgba(255,90,90,0.22));
            color: #ffaaaa;
        }

        .feature-title {
            font-size: 16px;
            font-weight: 500;
            color: #e8edf2;
            margin-bottom: 6px;
            line-height: 1.3;
        }
        .feature-desc {
            font-size: 14px;
            color: #999;
            line-height: 1.5;
        }

        /* ── Responsivo ─────────────────────────────────── */
        @media (max-width: 1100px) {
            .mascot { left: -30px; width: 240px; }
            .trophy { right: -30px; width: 90px; height: 90px; }
            .trophy svg { width: 44px; height: 44px; }
            .card { padding: 50px 48px 40px; }
        }
        @media (max-width: 900px) {
            .mascot, .trophy { display: none; }
            .stage { min-height: auto; }
            .card { padding: 44px 30px 36px; max-width: 100%; }
            .card h1 { font-size: 28px; }
            .features { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 500px) {
            .features { grid-template-columns: 1fr; }
            .card h1 { font-size: 24px; }
        }
    </style>
</head>
<body>

<header class="brand-header">
    <img src="{{ asset('images/backgrounds/Logo_guardiao.png') }}" alt="Guardião Digital">
</header>

<main class="main">
    <div class="stage">
        <img src="{{ asset('images/mascots/training-welcome-guardian.png') }}" alt="Mascote Guardião" class="mascot">

        <div class="card">
            <div class="card-shield-badge">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5l-8-3z"/>
                </svg>
            </div>

            <h1>
                Bem vindo à
                <span class="highlight">Jornada Guardião</span>
            </h1>

            <p class="lead">
                Você enfrentará situações parecidas com as do dia a dia corporativo:
                mensagens urgentes, anexos de e-mail, links compartilhados e solicitações
                diversas. Observe, escolha e evolua.
            </p>

            <div class="highlight-msg">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Você não está sendo testado. Está sendo preparado!
            </div>

            <form method="POST" action="{{ route('training.start') }}">
                @csrf
                <button type="submit" class="btn-cta">Começar minha jornada</button>
            </form>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    </div>
                    <div class="feature-title">13 Desafios rápidos</div>
                    <div class="feature-desc">Simulações curtas e práticas</div>
                </div>

                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div class="feature-title">3 Canais simulados</div>
                    <div class="feature-desc">WhatsApp, E-mail e comunicador</div>
                </div>

                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24">
                            <!-- Alvo (3 círculos concêntricos) -->
                            <circle cx="12" cy="12" r="9"/>
                            <circle cx="12" cy="12" r="5.5"/>
                            <circle cx="12" cy="12" r="2"/>
                        </svg>
                    </div>
                    <div class="feature-title">Feedback Imediato</div>
                    <div class="feature-desc">Aprenda na hora e evolua sempre</div>
                </div>

                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M7 14 L 4 4 L 9 4 L 12 10"/>
                            <path d="M17 14 L 20 4 L 15 4 L 12 10"/>
                            <circle cx="12" cy="17" r="5.5"/>
                            <polygon points="12 14 12.9 16 15 16.1 13.2 17.4 13.9 19.5 12 18.2 10.1 19.5 10.8 17.4 9 16.1 11.1 16" fill="currentColor" stroke="none"/>
                        </svg>
                    </div>
                    <div class="feature-title">Certificado ao final</div>
                    <div class="feature-desc">Conquiste e compartilhe suas habilidades</div>
                </div>
            </div>
        </div>

        <div class="trophy">
            <svg viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/>
                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
                <path d="M4 22h16"/>
                <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/>
                <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/>
                <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
            </svg>
        </div>
    </div>
</main>

</body>
</html>
