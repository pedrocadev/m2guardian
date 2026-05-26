<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convite Guardião Digital</title>
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
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: #CC0000; color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 6px; font-size: 15px; font-weight: 700; letter-spacing: 0.5px; }
        .warning { background: #fff8e1; border: 1px solid #ffe082; border-radius: 6px; padding: 12px 16px; font-size: 13px; color: #795548; margin: 20px 0; }
        .url-fallback { word-break: break-all; color: #CC0000; font-size: 12px; margin-top: 12px; }
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
            Com o <strong>Guardião Digital</strong>, você poderá convidar sua equipe para completar treinamentos de segurança e acompanhar os resultados em tempo real pelo dashboard.
        </p>

        <div class="btn-wrap">
            <a href="{{ $magicLinkUrl }}" class="btn">Acessar meu painel →</a>
        </div>

        <div class="warning">
            ⏱️ <strong>Este link é de uso único e expira em 7 dias.</strong><br>
            Por segurança, não compartilhe este link com ninguém. Após o primeiro acesso, você poderá entrar novamente solicitando um novo link.
        </div>

        <p class="text" style="font-size:13px; color:#888;">
            Se o botão não funcionar, copie e cole o endereço abaixo no seu navegador:
        </p>
        <div class="url-fallback">{{ $magicLinkUrl }}</div>
    </div>

    <div class="footer">
        M2 Cloud &amp; Security · Guardião Digital<br>
        Tecnologia com propósito. Segurança com inteligência.
    </div>
</div>
</body>
</html>
