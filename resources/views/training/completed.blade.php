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
            padding: 24px;
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
            background: rgba(244,245,247,0.55);
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
            filter: drop-shadow(0 12px 24px rgba(204,0,0,0.25));
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

        .card { background: #fff; border-radius: 16px; padding: 48px 40px 40px; max-width: 480px; width: 100%; box-shadow: 0 4px 24px rgba(0,0,0,0.12); text-align: center; }

        .badge { display: inline-block; background: #111; color: #fff; font-size: 10px; font-weight: 700; letter-spacing: 1.5px; padding: 5px 12px; border-radius: 4px; margin-bottom: 24px; }

        .score-circle {
            width: 120px; height: 120px; border-radius: 50%;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            border: 6px solid #eee;
        }
        .score-circle.great { border-color: #16a34a; }
        .score-circle.ok { border-color: #d97706; }
        .score-circle.bad { border-color: #dc2626; }
        .score-pct { font-size: 32px; font-weight: 900; line-height: 1; }
        .score-pct.great { color: #16a34a; }
        .score-pct.ok { color: #d97706; }
        .score-pct.bad { color: #dc2626; }
        .score-label { font-size: 11px; color: #999; margin-top: 4px; }

        .title { font-size: 22px; font-weight: 800; color: #111; margin-bottom: 8px; }
        .subtitle { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 28px; }

        .stats { display: flex; gap: 12px; margin-bottom: 28px; }
        .stat { flex: 1; background: #f9f9f9; border-radius: 10px; padding: 14px 10px; }
        .stat-value { font-size: 22px; font-weight: 900; color: #111; }
        .stat-label { font-size: 11px; color: #888; margin-top: 4px; }

        .message { background: #f9f9f9; border-radius: 10px; padding: 16px; font-size: 13px; color: #555; line-height: 1.7; margin-bottom: 28px; border-left: 3px solid #CC0000; text-align: left; }

        .footer-brand { font-size: 11px; color: #bbb; margin-top: 8px; }
    </style>
</head>
<body>
<div class="mascot-celebration">
    <img src="/images/mascote/guardiao-medalha.png" alt="Treinamento concluído!">
</div>

@php
    $score = $session->score ?? 0;
    $total = $session->total_questions ?? 1;
    $pct = $total > 0 ? round($score / $total * 100) : 0;
    $level = $pct >= 80 ? 'great' : ($pct >= 50 ? 'ok' : 'bad');

    $messages = [
        'great' => 'Excelente desempenho! Você demonstrou consciência sólida em segurança digital. Continue assim — seu comportamento protege você e toda a sua equipe.',
        'ok'    => 'Bom começo! Você acertou a maioria dos cenários. Revise os pontos onde errou — cada situação real pode ser decisiva para a segurança da sua empresa.',
        'bad'   => 'Atenção! Você encontrou dificuldades em alguns cenários importantes. Esses são exatamente os tipos de situação que atacantes exploram. Fique atento nas próximas semanas.',
    ];

    $titles = [
        'great' => 'Parabéns! 🏆',
        'ok'    => 'Bom trabalho! 👍',
        'bad'   => 'Treinamento concluído ⚠️',
    ];

    $minutes = $session->duration_seconds ? floor($session->duration_seconds / 60) : '—';
@endphp
<div class="card">
    <div class="badge">GUARDIÃO DIGITAL</div>

    <div class="score-circle {{ $level }}">
        <div class="score-pct {{ $level }}">{{ $pct }}%</div>
        <div class="score-label">acertos</div>
    </div>

    <div class="title">{{ $titles[$level] }}</div>
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

    <div class="message">{{ $messages[$level] }}</div>

    <div class="footer-brand">M2 Cloud & Security · Guardião Digital</div>
</div>
</body>
</html>
