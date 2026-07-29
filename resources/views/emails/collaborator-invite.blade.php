<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Você recebeu uma missão no Guardião Digital</title>
    <style>
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background: #f5f5f5; color: #222; }
        .wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
        .banner { display: block; width: 100%; height: auto; }
        .body { padding: 32px 36px 28px; }
        .greeting { font-size: 20px; font-weight: 700; color: #111111; margin: 0 0 20px; }
        .text { font-size: 15px; color: #444444; line-height: 1.65; margin: 0 0 16px; }
        .highlight { color: #111; font-weight: 600; }
        .meta-line { font-size: 14px; color: #555; margin: 24px 0 8px; font-weight: 600; }
        .btn-wrap { text-align: center; margin: 28px 0 20px; }
        .btn { display: inline-block; background: #CC0000; color: #ffffff !important; text-decoration: none; padding: 15px 40px; border-radius: 6px; font-size: 15px; font-weight: 700; letter-spacing: 0.3px; }
        .lock-line { background: #f9fafb; border-left: 3px solid #CC0000; padding: 10px 14px; font-size: 13px; color: #444; border-radius: 4px; margin: 20px 0 10px; }
        .deadline { font-size: 13px; color: #666; margin: 6px 0 24px; }
        .legit { background: #f5f7fa; border-radius: 8px; padding: 18px 20px; margin: 28px 0 8px; }
        .legit-title { font-size: 14px; font-weight: 700; color: #111; margin: 0 0 10px; }
        .legit-text { font-size: 13px; color: #555; line-height: 1.6; margin: 0 0 10px; }
        .footer { border-top: 1px solid #eeeeee; padding: 20px 36px 28px; text-align: center; font-size: 12px; color: #999999; letter-spacing: 0.5px; }
        .footer strong { color: #CC0000; }
    </style>
</head>
<body>
<div class="wrapper">
    <img src="{{ asset('images/Email/banner.mailconvite.png') }}" alt="Guardião Digital — M2 Cloud & Security" class="banner">

    <div class="body">
        <p class="greeting">Olá{{ $collaborator->name ? ', ' . $collaborator->name : '' }}!</p>

        <p class="text">
            A <span class="highlight">{{ $leader->company->name }}</span> convidou você para participar do Guardião Digital, uma experiência prática de conscientização em segurança desenvolvida pela M2 Cloud &amp; Security.
        </p>

        <p class="text">
            Em uma jornada rápida, você enfrentará situações inspiradas no dia a dia e tomará decisões sobre mensagens, links, pedidos urgentes e outras abordagens digitais.
        </p>

        <p class="text">
            Você aprenderá a reconhecer golpes, tentativas de phishing e manipulações antes que elas se transformem em um problema para você ou para a empresa.
        </p>

        <p class="meta-line">Tempo estimado: de 10 a 15 minutos.</p>

        <div class="btn-wrap">
            <a href="{{ $magicLinkUrl }}" class="btn">Começar minha jornada →</a>
        </div>

        <div class="lock-line">
            🔐 Este link é pessoal e está vinculado ao seu e-mail. Não o compartilhe.
        </div>

        <p class="deadline">
            Conclua sua jornada até <strong>{{ $deadline }}</strong>.
        </p>

        <div class="legit">
            <p class="legit-title">Como saber que este convite é legítimo?</p>
            <p class="legit-text">
                Esta ação foi autorizada pela <strong>{{ $leader->company->name }}</strong> e enviada pela M2 Cloud &amp; Security. O botão acima direciona exclusivamente ao ambiente oficial do Guardião Digital.
            </p>
            <p class="legit-text">
                Em caso de dúvida, entre em contato com o responsável pela ação na {{ $leader->company->name }} ou com o suporte da M2.
            </p>
        </div>
    </div>

    <div class="footer">
        <strong>M2 Guardião Digital</strong>
    </div>
</div>
</body>
</html>
