<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação 2FA — M2 Guardião Admin</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #0f0f0f; color: #eee; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1a1a1a; border: 1px solid #2a2a2a; border-top: 3px solid #CC0000; border-radius: 12px; padding: 40px; max-width: 400px; width: 100%; }
        .logo { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
        .logo-icon { font-size: 24px; }
        .logo-text { font-weight: 900; font-size: 14px; letter-spacing: 1px; }
        .logo-sub { font-size: 10px; color: #666; }
        h1 { font-size: 18px; font-weight: 800; margin-bottom: 6px; }
        p { color: #888; font-size: 13px; margin-bottom: 24px; }
        label { font-size: 11px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px; }
        input[type=text] { width: 100%; background: #111; border: 1px solid #333; color: #eee; padding: 14px; border-radius: 8px; font-size: 22px; letter-spacing: 8px; text-align: center; outline: none; margin-bottom: 16px; }
        input:focus { border-color: #CC0000; }
        button { width: 100%; background: #CC0000; color: #fff; border: none; padding: 14px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; }
        button:hover { background: #aa0000; }
        .error { background: #3b0000; border: 1px solid #7f1d1d; color: #fca5a5; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 14px; }
        .recovery-link { text-align: center; margin-top: 16px; font-size: 12px; color: #666; }
        .recovery-link a { color: #888; text-decoration: underline; cursor: pointer; }
        #recovery-form { display: none; margin-top: 16px; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <div class="logo-icon">🛡️</div>
        <div>
            <div class="logo-text">GUARDIÃO DIGITAL</div>
            <div class="logo-sub">by M2 Cloud & Security</div>
        </div>
    </div>

    <h1>Verificação em Dois Fatores</h1>
    <p>Insira o código de 6 dígitos do seu app autenticador.</p>

    @if($errors->any())
    <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.two-factor.verify') }}">
        @csrf
        <label>Código do Autenticador</label>
        <input type="text" name="code" placeholder="000000" maxlength="8" autocomplete="one-time-code" autofocus>
        <button type="submit">Verificar</button>
    </form>

    <div class="recovery-link">
        <a onclick="document.getElementById('recovery-form').style.display='block'; this.parentElement.style.display='none';">
            Usar código de recuperação
        </a>
    </div>

    <div id="recovery-form">
        <label style="margin-top:0;">Código de Recuperação</label>
        <form method="POST" action="{{ route('admin.two-factor.verify') }}">
            @csrf
            <input type="text" name="code" placeholder="XXXXXXXXXX" maxlength="10" style="letter-spacing:2px;font-size:14px;">
            <button type="submit">Verificar com Código de Recuperação</button>
        </form>
    </div>
</div>
</body>
</html>
