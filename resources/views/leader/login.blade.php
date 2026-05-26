<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso do Líder — Guardião Digital</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #0f0f0f; color: #eee; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #1a1a1a; border: 1px solid #2a2a2a; border-top: 3px solid #CC0000; border-radius: 12px; padding: 40px; max-width: 420px; width: 100%; box-shadow: 0 10px 40px rgba(0,0,0,.4); }
        .logo { display: flex; align-items: center; gap: 12px; margin-bottom: 28px; }
        .logo-icon { font-size: 28px; }
        .logo-text { font-weight: 900; font-size: 15px; letter-spacing: 1px; }
        .logo-sub { font-size: 10px; color: #666; letter-spacing: 0.5px; }
        h1 { font-size: 20px; font-weight: 800; margin-bottom: 6px; }
        .subtitle { color: #888; font-size: 13px; margin-bottom: 28px; line-height: 1.5; }
        label { font-size: 11px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: 0.6px; display: block; margin-bottom: 6px; margin-top: 14px; }
        input[type=email], input[type=password] { width: 100%; background: #0a0a0a; border: 1px solid #333; color: #eee; padding: 13px 14px; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.15s; }
        input:focus { border-color: #CC0000; }
        .remember-row { display: flex; align-items: center; gap: 8px; margin: 16px 0 22px; font-size: 12px; color: #aaa; }
        .remember-row input { width: auto; }
        button { width: 100%; background: #CC0000; color: #fff; border: none; padding: 14px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; letter-spacing: 0.5px; transition: background 0.15s; }
        button:hover { background: #aa0000; }
        .error { background: #3b0000; border: 1px solid #7f1d1d; color: #fca5a5; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 14px; }
        .footer-note { text-align: center; margin-top: 24px; font-size: 11px; color: #555; line-height: 1.6; }
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

    <h1>Painel do Líder</h1>
    <div class="subtitle">Insira suas credenciais fornecidas pela equipe M2 para acessar o painel da sua empresa.</div>

    @if($errors->any())
    <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('leader.login') }}">
        @csrf
        <label>E-mail Corporativo</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">

        <label>Senha</label>
        <input type="password" name="password" required autocomplete="current-password">

        <div class="remember-row">
            <input type="checkbox" name="remember" id="remember" value="1">
            <label for="remember" style="margin:0; text-transform:none; letter-spacing:0; font-weight:normal; color:#aaa; font-size:12px;">Manter conectado neste dispositivo</label>
        </div>

        <button type="submit">Acessar Painel →</button>
    </form>

    <div class="footer-note">
        Esqueceu sua senha? Entre em contato com seu gestor M2 para receber novas credenciais.
    </div>
</div>
</body>
</html>
