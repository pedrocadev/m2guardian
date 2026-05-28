<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treinamento Concluído — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            background-image: url('/images/mascote/bg-circuito.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(244, 245, 247, 0.55);
            z-index: 0;
            pointer-events: none;
        }
        body > * { position: relative; z-index: 1; }

        .mascot-celebration {
            text-align: center;
            margin-bottom: -40px;
            animation: bounceIn 0.8s ease;
        }
        .mascot-celebration img {
            width: 220px;
            height: auto;
            filter: drop-shadow(0 12px 24px rgba(204, 0, 0, 0.25));
            animation: floatY 3s ease-in-out infinite;
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            60% { opacity: 1; transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .card {
            background: #fff;
            border-radius: 18px;
            padding: 48px 40px 36px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.14);
            text-align: center;
            animation: cardSlide 0.6s ease 0.2s backwards;
        }
        @keyframes cardSlide {
            from { opacity: 0; transform: translateY(28px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .badge {
            display: inline-block;
            background: #111;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            padding: 5px 12px;
            border-radius: 4px;
            margin-bottom: 22px;
        }

        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 6px solid var(--lvl-color, #eee);
        }
        .score-pct {
            font-size: 32px;
            font-weight: 900;
            line-height: 1;
            color: var(--lvl-color, #111);
        }
        .score-label { font-size: 11px; color: #999; margin-top: 4px; }

        /* ── Patente / nível ─────────────────────────── */
        .level-patent {
            background: linear-gradient(135deg, var(--lvl-color), var(--lvl-color-dark));
            color: #fff;
            border-radius: 14px;
            padding: 18px 22px 20px;
            margin: 0 auto 26px;
            max-width: 380px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 18px var(--lvl-shadow);
            animation: levelPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.4s backwards;
        }
        .level-patent::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.15) 0%, transparent 60%);
            animation: shineRotate 8s linear infinite;
        }
        .level-tag {
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 3px;
            opacity: 0.9;
            margin-bottom: 4px;
        }
        .level-icon {
            font-size: 38px;
            line-height: 1;
            margin-bottom: 6px;
            display: block;
            filter: drop-shadow(0 3px 8px rgba(0, 0, 0, 0.25));
        }
        .level-name {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
        }
        @keyframes levelPop {
            from { opacity: 0; transform: scale(0.6); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes shineRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .title {
            font-size: 22px;
            font-weight: 800;
            color: #111;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 26px;
        }

        .stats {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }
        .stat {
            flex: 1;
            background: #f9f9f9;
            border-radius: 10px;
            padding: 14px 10px;
        }
        .stat-value {
            font-size: 22px;
            font-weight: 900;
            color: #111;
        }
        .stat-label {
            font-size: 11px;
            color: #888;
            margin-top: 4px;
        }

        .message {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 16px;
            font-size: 13.5px;
            color: #444;
            line-height: 1.7;
            margin-bottom: 22px;
            border-left: 4px solid var(--lvl-color);
            text-align: left;
        }

        .footer-brand {
            font-size: 11px;
            color: #bbb;
            margin-top: 8px;
        }

        @media (max-width: 540px) {
            .card { padding: 36px 22px 26px; }
            .level-patent { padding: 14px 16px 16px; max-width: 100%; }
            .level-name { font-size: 17px; }
            .stats { gap: 6px; }
        }
    </style>
</head>
<body>

@php
    $score = $session->score ?? 0;
    $total = $session->total_questions ?? 1;
    $pct = $total > 0 ? round($score / $total * 100) : 0;
    $minutes = $session->duration_seconds ? floor($session->duration_seconds / 60) : '—';

    // Determina o nível (N1 a N5)
    if ($pct >= 100) {
        $levelKey = 'n5';
    } elseif ($pct >= 85) {
        $levelKey = 'n4';
    } elseif ($pct >= 70) {
        $levelKey = 'n3';
    } elseif ($pct >= 50) {
        $levelKey = 'n2';
    } else {
        $levelKey = 'n1';
    }

    $levels = [
        'n1' => [
            'tag'         => 'NÍVEL 1',
            'name'        => 'Em Alerta',
            'icon'        => '⚠️',
            'color'       => '#dc2626',
            'colorDark'   => '#991b1b',
            'shadow'      => 'rgba(220, 38, 38, 0.35)',
            'title'       => 'Treinamento concluído',
            'message'     => 'Você já iniciou sua jornada, mas ainda precisa fortalecer sua atenção diante de riscos digitais. Revise os cenários onde errou e fique atento nas próximas semanas — os atacantes exploram exatamente esses pontos.',
            'mascot'      => 'guardiao-explicando.png',
        ],
        'n2' => [
            'tag'         => 'NÍVEL 2',
            'name'        => 'Aprendiz Guardião',
            'icon'        => '🌱',
            'color'       => '#ea580c',
            'colorDark'   => '#9a3412',
            'shadow'      => 'rgba(234, 88, 12, 0.35)',
            'title'       => 'Bom começo! 👍',
            'message'     => 'Você reconhece alguns sinais de risco, mas ainda pode evoluir em situações de pressão. Continue praticando — quanto mais você treina, mais natural fica identificar as armadilhas.',
            'mascot'      => 'guardiao-correndo.png',
        ],
        'n3' => [
            'tag'         => 'NÍVEL 3',
            'name'        => 'Guardião Atento',
            'icon'        => '🛡️',
            'color'       => '#ca8a04',
            'colorDark'   => '#854d0e',
            'shadow'      => 'rgba(202, 138, 4, 0.35)',
            'title'       => 'Bom trabalho! 🛡️',
            'message'     => 'Você demonstra boa postura e toma decisões seguras na maior parte dos cenários. Mantenha esse padrão e fique atento aos detalhes — pequenos sinais fazem diferença.',
            'mascot'      => 'guardiao-vitoria.png',
        ],
        'n4' => [
            'tag'         => 'NÍVEL 4',
            'name'        => 'Guardião Estratégico',
            'icon'        => '⚔️',
            'color'       => '#16a34a',
            'colorDark'   => '#15803d',
            'shadow'      => 'rgba(22, 163, 74, 0.35)',
            'title'       => 'Excelente! ⚔️',
            'message'     => 'Você identifica riscos com clareza, valida informações e protege dados com consistência. Sua postura é referência para o time — continue assim.',
            'mascot'      => 'guardiao-guerreiro.png',
        ],
        'n5' => [
            'tag'         => 'NÍVEL 5',
            'name'        => 'Guardião Digital Certificado',
            'icon'        => '🏆',
            'color'       => '#CC0000',
            'colorDark'   => '#7f0000',
            'shadow'      => 'rgba(204, 0, 0, 0.45)',
            'title'       => 'Parabéns! Você é referência. 🏆',
            'message'     => 'Você concluiu todas as etapas com alto desempenho e demonstrou postura segura diante de diferentes ameaças. Compartilhe o que aprendeu — você é exemplo de cultura de segurança.',
            'mascot'      => 'guardiao-medalha.png',
        ],
    ];

    $L = $levels[$levelKey];
@endphp

<div class="mascot-celebration">
    <img src="/images/mascote/{{ $L['mascot'] }}" alt="Treinamento concluído!">
</div>

<div class="card" style="
    --lvl-color: {{ $L['color'] }};
    --lvl-color-dark: {{ $L['colorDark'] }};
    --lvl-shadow: {{ $L['shadow'] }};
">
    <div class="badge">GUARDIÃO DIGITAL</div>

    <div class="score-circle">
        <div class="score-pct">{{ $pct }}%</div>
        <div class="score-label">acertos</div>
    </div>

    <div class="level-patent">
        <div class="level-tag">{{ $L['tag'] }}</div>
        <span class="level-icon">{{ $L['icon'] }}</span>
        <div class="level-name">{{ $L['name'] }}</div>
    </div>

    <div class="title">{{ $L['title'] }}</div>
    <div class="subtitle">{{ $collaborator->name ?? $collaborator->email }}, você concluiu o treinamento.</div>

    <div class="stats">
        <div class="stat">
            <div class="stat-value">{{ $score }}/{{ $total }}</div>
            <div class="stat-label">Acertos</div>
        </div>
        <div class="stat">
            <div class="stat-value">{{ $session->total_scenarios ?? '—' }}</div>
            <div class="stat-label">Cenários</div>
        </div>
        <div class="stat">
            <div class="stat-value">{{ $minutes }}'</div>
            <div class="stat-label">Duração</div>
        </div>
    </div>

    <div class="message">{{ $L['message'] }}</div>

    <div class="footer-brand">M2 Cloud &amp; Security · Guardião Digital</div>
</div>
</body>
</html>
