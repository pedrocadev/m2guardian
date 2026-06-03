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
        }

        /* ===== Estilos específicos do form do líder ===== */
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
            font-size: 12px;
            font-weight: 600;
            color: #444;
            display: block;
            margin-bottom: 6px;
            margin-top: 16px;
        }
        .leader-form label:first-of-type { margin-top: 0; }

        .leader-form input[type=email],
        .leader-form input[type=password] {
            width: 100%;
            background: #ffffff;
            border: 1.5px solid #e5e5e5;
            color: #111;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .leader-form input:focus {
            border-color: #CC0000;
            box-shadow: 0 0 0 3px rgba(204, 0, 0, 0.1);
        }

        .leader-remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 18px 0 24px;
        }
        .leader-remember-row input { width: 16px; height: 16px; accent-color: #CC0000; cursor: pointer; }
        .leader-remember-row label {
            font-size: 13px;
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
            padding: 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: background 0.15s, transform 0.05s;
        }
        .leader-form button[type=submit]:hover { background: #a30000; }
        .leader-form button[type=submit]:active { transform: scale(0.99); }

        .leader-footer-note {
            text-align: center;
            margin-top: 24px;
            padding-top: 22px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #888;
            line-height: 1.6;
        }
        .leader-footer-note strong { color: #555; }
    </style>
</head>
<body>

<x-auth-layout
    title="Eleve a maturidade"
    title-highlight="da sua equipe."
    lead="Acompanhe campanhas de phishing, métricas por colaborador e a evolução da consciência de segurança da sua empresa em tempo real."
    :features="[
        'Cenários realistas em WhatsApp, Teams e E-mail',
        'Métricas granulares por colaborador',
        'Evidências para LGPD e ISO 27001',
    ]"
    mascot="login-leader.png"
    form-title="Entrar como Líder"
    form-subtitle="Acesse o painel da sua empresa com as credenciais fornecidas pela equipe M2."
>
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
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="voce@suaempresa.com.br">

        <label for="password">Senha</label>
        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">

        <div class="leader-remember-row">
            <input type="checkbox" name="remember" id="remember" value="1">
            <label for="remember">Manter conectado neste dispositivo</label>
        </div>

        <button type="submit">Acessar painel</button>
    </form>

    <div class="leader-footer-note">
        Não recebeu acesso? Fale com seu <strong>contato M2</strong> para receber suas credenciais.
    </div>
</x-auth-layout>

</body>
</html>
