<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar 2FA — M2 Guardião Admin</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #0f0f0f; color: #eee; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1a1a1a; border: 1px solid #2a2a2a; border-top: 3px solid #CC0000; border-radius: 12px; padding: 40px; max-width: 460px; width: 100%; }
        h1 { font-size: 20px; font-weight: 800; margin-bottom: 6px; }
        p { color: #888; font-size: 13px; margin-bottom: 24px; line-height: 1.6; }
        .steps { counter-reset: step; margin-bottom: 24px; }
        .step { display: flex; gap: 12px; margin-bottom: 16px; font-size: 13px; color: #ccc; }
        .step-num { background: #CC0000; color: #fff; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; margin-top: 1px; }
        .qr-box { background: #fff; padding: 16px; border-radius: 8px; display: flex; justify-content: center; margin-bottom: 20px; }
        .qr-box img { width: 200px; height: 200px; }
        input[type=text] { width: 100%; background: #111; border: 1px solid #333; color: #eee; padding: 12px 14px; border-radius: 8px; font-size: 16px; letter-spacing: 4px; text-align: center; outline: none; margin-bottom: 12px; }
        input:focus { border-color: #CC0000; }
        button { width: 100%; background: #CC0000; color: #fff; border: none; padding: 14px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; letter-spacing: 0.5px; }
        button:hover { background: #aa0000; }
        .error { background: #3b0000; border: 1px solid #7f1d1d; color: #fca5a5; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 12px; }
        .secret-box { background: #111; border: 1px solid #333; border-radius: 6px; padding: 10px 14px; font-family: monospace; font-size: 13px; color: #aaa; margin-bottom: 20px; word-break: break-all; text-align: center; }
    </style>
</head>
<body>
<div class="card">
    <h1>Autenticação em Dois Fatores</h1>
    <p>Configure o 2FA para proteger seu acesso ao painel administrativo.</p>

    <div class="steps">
        <div class="step">
            <div class="step-num">1</div>
            <span>Instale um app autenticador (Google Authenticator, Authy ou Microsoft Authenticator)</span>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <span>Escaneie o QR code abaixo ou insira a chave manualmente</span>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <span>Digite o código de 6 dígitos gerado pelo app para confirmar</span>
        </div>
    </div>

    <div class="qr-box">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code 2FA">
    </div>

    <p style="font-size:11px; color:#666; margin-bottom:8px;">Chave manual (se não conseguir escanear):</p>
    <div class="secret-box">{{ $admin->two_factor_secret }}</div>

    @if($errors->any())
    <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.two-factor.confirm') }}">
        @csrf
        <input type="text" name="code" placeholder="000000" maxlength="6" autocomplete="one-time-code" autofocus>
        <button type="submit">Confirmar e Ativar 2FA</button>
    </form>
</div>
</body>
</html>
