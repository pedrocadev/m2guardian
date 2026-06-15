<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardião Digital — Sua jornada começa aqui</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body {
            font-family: Arial, sans-serif;
            background: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            position: fixed;
            inset: 0;
        }

        /* Header fino com logo + botão pular */
        .intro-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 22px;
            background: linear-gradient(180deg, rgba(0,0,0,0.85), rgba(0,0,0,0));
            position: absolute;
            top: 0; left: 0; right: 0;
            z-index: 10;
        }
        .intro-brand { display: flex; align-items: center; gap: 10px; }
        .intro-brand-icon { font-size: 22px; }
        .intro-brand-text {
            font-weight: 800;
            font-size: 13px;
            letter-spacing: 1px;
            color: #fff;
        }

        /* Botão pular — desabilitado até 50% */
        .skip-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #aaa;
            font-size: 12.5px;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: not-allowed;
            transition: all 0.2s;
            user-select: none;
            text-decoration: none;
        }
        .skip-btn.unlocked {
            background: linear-gradient(135deg, #CC0000, #aa0000);
            border-color: transparent;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(204, 0, 0, 0.4);
            animation: skip-pulse 0.5s ease-out;
        }
        .skip-btn.unlocked:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(204, 0, 0, 0.5);
        }
        @keyframes skip-pulse {
            0%   { transform: scale(0.92); opacity: 0.6; }
            60%  { transform: scale(1.06); }
            100% { transform: scale(1); opacity: 1; }
        }
        .skip-countdown {
            font-variant-numeric: tabular-nums;
            opacity: 0.7;
        }

        /* Player de vídeo — ocupa a tela inteira (cover preenche, contain manteria barras pretas) */
        .intro-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: #000;
            z-index: 0;
        }

        /* Barra de progresso no rodapé */
        .progress-bar {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 10;
        }
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #CC0000, #ff5555);
            width: 0%;
            transition: width 0.1s linear;
            box-shadow: 0 0 8px rgba(204, 0, 0, 0.6);
        }

        /* Fallback se vídeo falhar */
        .video-error {
            display: none;
            position: absolute;
            inset: 0;
            background: #0a0a0a;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 12px;
            text-align: center;
            padding: 24px;
        }
        .video-error h2 { font-size: 18px; font-weight: 700; }
        .video-error p { font-size: 13px; color: #888; }
        .video-error a {
            background: #CC0000;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            margin-top: 12px;
        }
    </style>
</head>
<body>

<div class="intro-header">
    <div class="intro-brand">
        <span class="intro-brand-icon">🛡️</span>
        <span class="intro-brand-text">GUARDIÃO DIGITAL</span>
    </div>
    <a href="{{ route('training.welcome') }}" id="skip-btn" class="skip-btn" aria-disabled="true">
        <span id="skip-label">Pular em <span class="skip-countdown" id="skip-counter">3</span>s</span>
    </a>
</div>

<video id="intro-video" class="intro-video" autoplay muted playsinline preload="auto">
    <source src="{{ asset('videos/intro.mp4') }}" type="video/mp4">
</video>

<div class="progress-bar">
    <div id="progress-fill" class="progress-bar-fill"></div>
</div>

<div id="video-error" class="video-error">
    <h2>⚠️ Vídeo indisponível</h2>
    <p>Não foi possível carregar o vídeo de introdução.</p>
    <a href="{{ route('training.welcome') }}">Continuar para o treinamento →</a>
</div>

<script>
(function () {
    const video      = document.getElementById('intro-video');
    const skipBtn    = document.getElementById('skip-btn');
    const skipLabel  = document.getElementById('skip-label');
    const skipCount  = document.getElementById('skip-counter');
    const progress   = document.getElementById('progress-fill');
    const errorBox   = document.getElementById('video-error');
    const welcomeUrl = "{{ route('training.welcome') }}";

    let unlocked = false;

    // Skip desabilitado por padrão; só libera após 50% do vídeo
    skipBtn.addEventListener('click', (e) => {
        if (!unlocked) e.preventDefault();
    });

    function unlock() {
        if (unlocked) return;
        unlocked = true;
        skipBtn.classList.add('unlocked');
        skipBtn.removeAttribute('aria-disabled');
        skipLabel.innerHTML = 'Pular intro →';
    }

    video.addEventListener('timeupdate', () => {
        if (!video.duration) return;

        const pct = (video.currentTime / video.duration) * 100;
        progress.style.width = pct + '%';

        if (!unlocked) {
            if (pct >= 50) {
                unlock();
            } else {
                // Contador regressivo até atingir 50%
                const remainingPct = 50 - pct;
                const remainingSec = Math.ceil((remainingPct / 100) * video.duration);
                skipCount.textContent = Math.max(0, remainingSec);
            }
        }
    });

    video.addEventListener('ended', () => {
        // Vídeo terminou → redireciona automaticamente
        window.location.href = welcomeUrl;
    });

    video.addEventListener('error', () => {
        // Falha ao carregar — mostra mensagem com link manual e redireciona em 2s
        errorBox.style.display = 'flex';
        setTimeout(() => { window.location.href = welcomeUrl; }, 2000);
    });

    // Failsafe: se o vídeo não começar em 3s (autoplay bloqueado por política do browser),
    // libera o skip imediatamente para o usuário ter como prosseguir
    setTimeout(() => {
        if (video.paused && video.currentTime === 0) {
            unlock();
        }
    }, 3000);
})();
</script>

</body>
</html>
