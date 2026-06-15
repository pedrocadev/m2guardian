<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso do Líder — Guardião Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #ffffff;
            color: #111111;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        img { max-width: 100%; height: auto; }

        /* ===== Form header (override pra ficar maior — fluido) ===== */
        .m2-form-header { gap: clamp(12px, 1.4vw, 18px); margin-bottom: clamp(24px, 3vw, 36px); }
        .m2-form-header-icon { width: clamp(44px, 4vw, 54px); height: clamp(44px, 4vw, 54px); border-radius: 14px; }
        .m2-form-header-icon svg { width: clamp(22px, 2vw, 28px); height: clamp(22px, 2vw, 28px); }
        .m2-form-title { font-size: clamp(22px, 2.2vw, 28px); letter-spacing: -0.4px; }
        .m2-form-subtitle { font-size: clamp(13px, 1.1vw, 15px); line-height: 1.6; }

        /* ===== Form do líder ===== */
        .leader-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .leader-error svg { width: 16px; height: 16px; flex-shrink: 0; }

        .leader-form label {
            font-size: clamp(13px, 1.05vw, 14px);
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 10px;
            margin-top: clamp(16px, 2vw, 22px);
        }
        .leader-form label:first-of-type { margin-top: 0; }

        .leader-form input[type=email],
        .leader-form input[type=password] {
            width: 100%;
            background: #f3f4f6;
            border: 1.5px solid #e5e7eb;
            color: #111;
            padding: clamp(14px, 1.5vw, 18px) clamp(16px, 1.8vw, 20px);
            border-radius: 999px;
            font-size: clamp(14px, 1.1vw, 15px);
            font-family: inherit;
            outline: none;
            transition: background 0.15s, border-color 0.15s, box-shadow 0.15s;
        }
        .leader-form input:focus {
            background: #ffffff;
            border-color: #CC0000;
            box-shadow: 0 0 0 3px rgba(204, 0, 0, 0.1);
        }

        .leader-remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: clamp(18px, 2vw, 24px) 0 clamp(20px, 2.5vw, 28px);
        }
        .leader-remember-row input { width: 18px; height: 18px; accent-color: #CC0000; cursor: pointer; flex-shrink: 0; }
        .leader-remember-row label {
            font-size: clamp(13px, 1.05vw, 14px);
            color: #555;
            font-weight: 500;
            margin: 0;
            cursor: pointer;
        }

        .leader-form button[type=submit] {
            width: 100%;
            background: #CC0000;
            color: #fff;
            border: none;
            padding: clamp(16px, 1.8vw, 20px);
            border-radius: 999px;
            font-size: clamp(14px, 1.1vw, 15px);
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: background 0.15s, transform 0.05s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: clamp(10px, 1.2vw, 14px);
        }
        .leader-form button[type=submit] svg { width: clamp(16px, 1.6vw, 20px); height: clamp(16px, 1.6vw, 20px); }
        .leader-form button[type=submit]:hover { background: #a30000; }
        .leader-form button[type=submit]:active { transform: scale(0.99); }

        /* Input com ícone interno (override fluido) */
        .m2-input-wrapper input { padding-left: clamp(46px, 4vw, 54px) !important; }
        .m2-input-wrapper .m2-input-icon { left: clamp(16px, 1.6vw, 20px); width: clamp(18px, 1.7vw, 20px); height: clamp(18px, 1.7vw, 20px); }

        /* Help card fluido */
        .m2-form-help-card { padding: clamp(16px, 1.8vw, 20px) clamp(18px, 2vw, 22px); gap: clamp(12px, 1.4vw, 16px); margin-top: clamp(20px, 2.5vw, 28px); }
        .m2-form-help-icon { width: clamp(36px, 3.5vw, 42px); height: clamp(36px, 3.5vw, 42px); }
        .m2-form-help-icon svg { width: clamp(18px, 1.7vw, 20px); height: clamp(18px, 1.7vw, 20px); }
        .m2-form-help-text { font-size: clamp(13px, 1.05vw, 14px); }
    </style>
</head>
<body>

<x-auth-layout
    lead="Acompanhe suas campanhas simuladas, identifique áreas vulneráveis e transforme comportamentos em indicadores claros para segurança, LGPD e governança."
    mascot="login-leader.png"
    hero-background="login-leader.jpg"
    hero-background-position="left center"
    brand-logo="backgrounds/Logo_guardiao.png"
    :show-stats="false"
    :show-legal="false"
    form-max-width="560px"
>
    <x-slot name="pill">
        <div class="m2-hero-pill">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/>
                <line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Painel de postura digital
        </div>
    </x-slot>

    <x-slot name="heroTitle">
        <h1 class="m2-hero-title">
            Enxergue o<br>
            <em>risco humano</em><br>
            antes do incidente
        </h1>
    </x-slot>

    <x-slot name="heroFeatures">
        <ul class="m2-hero-features-cards">
            <li>
                <span class="m2-feature-card-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </span>
                <div class="m2-feature-card-content">
                    <div class="m2-feature-card-title">Simulações realistas em</div>
                    <div class="m2-feature-card-sub">WhatsApp, E-mail e Comunicador</div>
                </div>
            </li>
            <li>
                <span class="m2-feature-card-icon">
                    <svg viewBox="0 0 24 24">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                </span>
                <div class="m2-feature-card-content">
                    <div class="m2-feature-card-title">Índice de Postura Digital</div>
                    <div class="m2-feature-card-sub">por área, perfil e campanha</div>
                </div>
            </li>
            <li>
                <span class="m2-feature-card-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="9" y1="13" x2="15" y2="13"/>
                        <line x1="9" y1="17" x2="13" y2="17"/>
                    </svg>
                </span>
                <div class="m2-feature-card-content">
                    <div class="m2-feature-card-title">Evidências para LGPD, ISO 27001</div>
                    <div class="m2-feature-card-sub">e programas de segurança</div>
                </div>
            </li>
        </ul>
    </x-slot>

    {{-- ===== FORM SIDE ===== --}}
    <div class="m2-form-header">
        <div class="m2-form-header-icon">
            <svg viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div class="m2-form-header-text">
            <h2 class="m2-form-title">Acesse o Painel Guardião</h2>
            <p class="m2-form-subtitle">Consulte os indicadores da sua empresa, acompanhe campanhas e visualize a evolução da postura digital das equipes.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="leader-error">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('leader.login') }}" class="leader-form">
        @csrf

        <label for="email">E-mail corporativo</label>
        <div class="m2-input-wrapper">
            <svg class="m2-input-icon" viewBox="0 0 24 24">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="voce@suaempresa.com.br">
        </div>

        <label for="password">Senha</label>
        <div class="m2-input-wrapper">
            <svg class="m2-input-icon" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
        </div>

        <div class="leader-remember-row">
            <input type="checkbox" name="remember" id="remember" value="1">
            <label for="remember">Manter-me conectado neste dispositivo</label>
        </div>

        <button type="submit">
            Entrar no painel
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
            </svg>
        </button>
    </form>

    <div class="m2-form-help-card">
        <div class="m2-form-help-icon">
            <svg viewBox="0 0 24 24">
                <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
                <path d="M21 19a2 2 0 0 1-2 2h-1v-6h3z"/>
                <path d="M3 19a2 2 0 0 0 2 2h1v-6H3z"/>
            </svg>
        </div>
        <div class="m2-form-help-text">
            Ainda não recebeu suas credenciais?<br>
            Fale com o gestor do projeto na sua empresa ou com o time da M2.
        </div>
    </div>
</x-auth-layout>

</body>
</html>
