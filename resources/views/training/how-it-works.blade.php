<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Como a jornada funciona — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            color: #111;
            min-height: 100vh;
            background-image: url('/images/mascote/bg-circuito.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.55);
            z-index: 0;
            pointer-events: none;
        }
        body > * { position: relative; z-index: 1; }

        /* Header slim com logo banner */
        .header {
            background: radial-gradient(ellipse at 20% 50%, #3a3a3a 0%, #1a1a1a 60%, #0a0a0a 100%);
            border-bottom: 3px solid #CC0000;
            padding: 8px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 80px;
            gap: 24px;
        }
        .brand-logo { display: flex; align-items: center; flex: 0 0 auto; }
        .brand-logo img {
            height: 130px;
            width: auto;
            display: block;
            mix-blend-mode: lighten;
            filter: drop-shadow(0 0 10px rgba(204, 0, 0, 0.25));
            margin: -25px 0;
        }
        .brand-fallback { display: none; align-items: center; gap: 10px; }
        .brand-name { color: #fff; font-weight: 900; font-size: 15px; letter-spacing: 1px; }
        .brand-sub { color: #888; font-size: 10px; letter-spacing: 0.5px; display: block; }
        .header-user { color: #ddd; font-size: 13px; text-align: right; flex-shrink: 0; }
        .header-user strong { color: #fff; display: block; font-size: 14px; margin-bottom: 2px; }

        .main {
            max-width: 920px;
            margin: 0 auto;
            padding: 48px 24px 60px;
        }

        /* Hero */
        .page-hero {
            text-align: center;
            margin-bottom: 36px;
            animation: slideIn 0.6s ease;
        }
        .page-hero .subtitle {
            font-size: 14px;
            color: #666;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .page-hero h1 {
            font-size: 34px;
            font-weight: 900;
            line-height: 1.15;
            color: #111;
        }
        .page-hero h1 span { color: #CC0000; }

        /* Texto introdutório */
        .content-card {
            background: #fff;
            border-radius: 16px;
            padding: 32px 38px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
            margin-bottom: 36px;
            animation: slideIn 0.7s ease;
        }
        .content-card p {
            font-size: 16px;
            line-height: 1.75;
            color: #333;
            margin-bottom: 14px;
        }
        .content-card p:last-of-type { margin-bottom: 0; }
        .content-card strong { color: #111; }
        .highlight { color: #CC0000; font-weight: 700; }

        /* Bloco visual com os passos */
        .steps-title {
            text-align: center;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 22px;
            animation: slideIn 0.75s ease;
        }
        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
            margin-bottom: 40px;
            animation: slideIn 0.8s ease;
        }
        .step {
            background: #fff;
            border-radius: 14px;
            padding: 20px 16px 18px;
            text-align: center;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
            border-top: 3px solid #CC0000;
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .step:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
        }
        .step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #CC0000;
            color: #fff;
            border-radius: 50%;
            font-size: 14px;
            font-weight: 900;
            margin-bottom: 12px;
            box-shadow: 0 3px 8px rgba(204, 0, 0, 0.35);
        }
        .step-icon { font-size: 32px; margin-bottom: 8px; line-height: 1; }
        .step-label {
            font-size: 13.5px;
            font-weight: 700;
            color: #111;
            line-height: 1.35;
        }
        .step.final {
            background: linear-gradient(135deg, #CC0000 0%, #aa0000 100%);
            border-top-color: #fff;
            color: #fff;
        }
        .step.final .step-num { background: #fff; color: #CC0000; }
        .step.final .step-label { color: #fff; }

        /* CTA */
        .cta-wrap {
            text-align: center;
            animation: slideIn 0.85s ease;
        }
        .btn-ready {
            display: inline-block;
            background: #CC0000;
            color: #fff;
            padding: 18px 56px;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(204, 0, 0, 0.3);
            transition: all 0.2s ease;
            text-transform: uppercase;
        }
        .btn-ready:hover {
            background: #aa0000;
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(204, 0, 0, 0.4);
        }
        .btn-ready:active { transform: translateY(0); }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 720px) {
            .header { min-height: 70px; padding: 6px 16px; flex-direction: column; gap: 4px; }
            .brand-logo img { height: 90px; margin: -15px 0; }
            .header-user { text-align: center; }
            .page-hero h1 { font-size: 26px; }
            .content-card { padding: 22px 22px; }
            .content-card p { font-size: 15px; }
            .steps { grid-template-columns: repeat(2, 1fr); }
            .btn-ready { padding: 16px 36px; font-size: 15px; }
        }
        @media (max-width: 420px) {
            .steps { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="header">
    <div class="brand-logo">
        <img src="/images/logo-guardiao.png" alt="Guardião Digital — by M2 Cloud & Security"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="brand-fallback">
            <span style="font-size:22px;">🛡️</span>
            <div>
                <span class="brand-name">GUARDIÃO DIGITAL</span>
                <span class="brand-sub">by M2 Cloud &amp; Security</span>
            </div>
        </div>
    </div>
    <div class="header-user">
        <strong>{{ $collaborator->name ?? $collaborator->email }}</strong>
        {{ $collaborator->company->name }}
    </div>
</div>

<div class="main">
    <div class="page-hero">
        <div class="subtitle">Entenda a dinâmica</div>
        <h1>Como a <span>jornada</span> funciona</h1>
    </div>

    <div class="content-card">
        <p>
            Você passará por diferentes <strong>cenários simulados</strong>. Em cada um deles, deverá
            escolher a <span class="highlight">atitude mais segura</span>.
        </p>
        <p>
            Algumas mensagens parecerão legítimas. Outras terão <strong>sinais de risco escondidos
            nos detalhes</strong>. O seu papel é <span class="highlight">observar, decidir e evoluir</span>.
        </p>
    </div>

    <div class="steps-title">O caminho do guardião</div>

    <div class="steps">
        <div class="step">
            <div class="step-num">1</div>
            <div class="step-icon">📖</div>
            <div class="step-label">Leia o cenário</div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-icon">👆</div>
            <div class="step-label">Escolha sua resposta</div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-icon">🛡️</div>
            <div class="step-label">Receba o feedback do Guardião</div>
        </div>
        <div class="step">
            <div class="step-num">4</div>
            <div class="step-icon">➡️</div>
            <div class="step-label">Avance para a próxima missão</div>
        </div>
        <div class="step final">
            <div class="step-num">5</div>
            <div class="step-icon">🏆</div>
            <div class="step-label">Descubra seu Nível Guardião</div>
        </div>
    </div>

    <div class="cta-wrap">
        <form action="{{ route('training.start') }}" method="POST">
            @csrf
            <button type="submit" class="btn-ready">Estou pronto →</button>
        </form>
    </div>
</div>
</body>
</html>
