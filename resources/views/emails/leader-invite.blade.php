<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suas credenciais — Guardião Digital</title>
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: #f5f5f5; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #111111; padding: 28px 32px; text-align: center; border-bottom: 3px solid #CC0000; }
        .header-title { color: #ffffff; font-size: 20px; font-weight: 900; letter-spacing: 1px; margin: 8px 0 2px; }
        .header-sub { color: #999999; font-size: 11px; letter-spacing: 0.5px; }
        .body { padding: 36px 32px; }
        .greeting { font-size: 18px; font-weight: 700; color: #111111; margin-bottom: 12px; }
        .text { font-size: 15px; color: #444444; line-height: 1.6; margin-bottom: 16px; }
        .company-badge { display: inline-block; background: #f5f5f5; border-left: 3px solid #CC0000; padding: 8px 16px; border-radius: 4px; font-weight: 700; color: #111111; margin: 8px 0 24px; }
        .creds-box { background: #fafafa; border: 1px solid #e5e5e5; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .cred-row { margin-bottom: 14px; }
        .cred-row:last-child { margin-bottom: 0; }
        .cred-label { display: block; font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .cred-value { font-family: 'Courier New', monospace; background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 10px 12px; font-size: 14px; font-weight: 700; color: #111; word-break: break-all; }
        .cred-password { color: #CC0000; font-size: 16px; letter-spacing: 1px; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: #CC0000; color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 6px; font-size: 15px; font-weight: 700; letter-spacing: 0.5px; }
        .warning { background: #fff8e1; border: 1px solid #ffe082; border-radius: 6px; padding: 12px 16px; font-size: 13px; color: #795548; margin: 20px 0; }
        .footer { background: #f9f9f9; border-top: 1px solid #eeeeee; padding: 20px 32px; text-align: center; font-size: 12px; color: #999999; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div style="font-size: 32px;">🛡️</div>
        <div class="header-title">GUARDIÃO DIGITAL</div>
        <div class="header-sub">by M2 Cloud &amp; Security</div>
    </div>

    <div class="body">
        <div class="greeting">Olá, {{ $leader->name }}!</div>

        <p class="text">
            Você foi cadastrado como <strong>líder responsável</strong> pelo programa de conscientização em segurança da sua empresa:
        </p>

        <div class="company-badge">🏢 {{ $leader->company->name }}</div>

        <p class="text">
            Abaixo estão suas credenciais de acesso ao <strong>Painel do Líder</strong>:
        </p>

        <div class="creds-box">
            <div class="cred-row">
                <span class="cred-label">URL de Acesso</span>
                <div class="cred-value">{{ $loginUrl }}</div>
            </div>
            <div class="cred-row">
                <span class="cred-label">E-mail (usuário)</span>
                <div class="cred-value">{{ $leader->email }}</div>
            </div>
            <div class="cred-row">
                <span class="cred-label">Senha</span>
                <div class="cred-value cred-password">{{ $password }}</div>
            </div>
        </div>

        <div class="btn-wrap">
            <a href="{{ $loginUrl }}" class="btn">Acessar meu painel →</a>
        </div>

        <div class="warning">
            🔒 <strong>Esta é sua senha pessoal e intransferível.</strong><br>
            Não compartilhe com terceiros. Após 5 tentativas inválidas, o acesso será bloqueado por 15 minutos.
            Caso esqueça a senha, solicite uma nova ao suporte M2.
        </div>
    </div>

    <div class="footer">
        M2 Cloud &amp; Security · Guardião Digital<br>
        Tecnologia com propósito. Segurança com inteligência.
    </div>
</div>
</body>
</html>
