<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trocar senha — Guardião Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            color: #111111;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 40px 44px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px -20px rgba(0,0,0,0.15), 0 8px 24px -8px rgba(0,0,0,0.08);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 8px;
        }
        .card-header-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #CC0000, #a30000);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .card-header-icon svg {
            width: 24px;
            height: 24px;
            color: #fff;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        h1 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #111;
        }

        .subtitle {
            font-size: 14px;
            color: #555;
            line-height: 1.55;
            margin-bottom: 24px;
        }

        .banner {
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .banner svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            stroke: currentColor;
            fill: none;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
            margin-top: 1px;
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .error ul { padding-left: 18px; margin: 4px 0 0; }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 8px;
            margin-top: 18px;
        }
        label:first-of-type { margin-top: 0; }

        input[type=password] {
            width: 100%;
            background: #f3f4f6;
            border: 1.5px solid #e5e7eb;
            color: #111;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: background 0.15s, border-color 0.15s, box-shadow 0.15s;
        }
        input[type=password]:focus {
            background: #ffffff;
            border-color: #CC0000;
            box-shadow: 0 0 0 3px rgba(204, 0, 0, 0.1);
        }

        .help {
            font-size: 12px;
            color: #888;
            margin-top: 6px;
        }

        button[type=submit] {
            width: 100%;
            background: #CC0000;
            color: #fff;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: background 0.15s, transform 0.05s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 24px;
        }
        button[type=submit] svg { width: 18px; height: 18px; }
        button[type=submit]:hover { background: #a30000; }
        button[type=submit]:active { transform: scale(0.99); }

        .footer-note {
            font-size: 12px;
            color: #999;
            text-align: center;
            margin-top: 20px;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <div class="card-header-icon">
            <svg viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <h1>Definir sua senha pessoal</h1>
    </div>

    <p class="subtitle">
        Olá, <strong>{{ $leader->name }}</strong>. Você está usando uma senha temporária gerada pelo administrador.
        Para continuar, escolha uma senha pessoal.
    </p>

    <div class="banner">
        <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <span>Esta troca é obrigatória e não pode ser adiada. A senha antiga deixará de funcionar assim que você salvar a nova.</span>
    </div>

    @if ($errors->any())
        <div class="error">
            <strong>Não foi possível salvar:</strong>
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('leader.password.update') }}">
        @csrf

        <label for="current_password">Senha atual (a temporária)</label>
        <input id="current_password" type="password" name="current_password" required autofocus autocomplete="current-password" placeholder="••••••••">

        <label for="password">Nova senha</label>
        <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="mínimo 8 caracteres">
        <div class="help">Escolha uma senha forte, com pelo menos 8 caracteres.</div>

        <label for="password_confirmation">Confirmar nova senha</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="repita a nova senha">

        <button type="submit">
            Salvar nova senha
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </button>
    </form>

    <p class="footer-note">
        Guarde sua senha em local seguro. A M2 nunca pede sua senha por e-mail ou telefone.
    </p>
</div>

</body>
</html>
