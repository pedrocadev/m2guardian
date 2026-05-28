<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $scenario->label }} — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            color: #111;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            background: rgba(244,245,247,0.78);
            z-index: 0;
            pointer-events: none;
        }
        body > * { position: relative; z-index: 1; }

        .header { background: #111; border-bottom: 3px solid #CC0000; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 56px; flex-shrink: 0; }
        .brand-name { color: #fff; font-weight: 900; font-size: 14px; letter-spacing: 1px; }
        .progress-info { color: #888; font-size: 13px; }
        .progress-info strong { color: #fff; }

        .scenario-bar { background: #fff; border-bottom: 1px solid #eee; padding: 12px 24px; display: flex; align-items: center; gap: 14px; flex-shrink: 0; }
        .s-avatar { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .s-info-label { font-size: 15px; font-weight: 700; }
        .s-info-sub { font-size: 12px; color: #888; }

        .question-counter { margin-left: auto; background: #f5f5f5; border-radius: 16px; padding: 5px 12px; font-size: 12px; font-weight: 700; color: #666; }
        .question-counter strong { color: #CC0000; }

        .chat-area { flex: 1; overflow-y: auto; padding: 24px; max-width: 680px; width: 100%; margin: 0 auto; }

        .msg { display: flex; margin-bottom: 12px; animation: fadeIn 0.3s ease; }
        .msg.them { justify-content: flex-start; }
        .msg.me { justify-content: flex-end; }
        .bubble { max-width: 72%; padding: 12px 16px; border-radius: 16px; font-size: 14px; line-height: 1.6; white-space: pre-wrap; }
        .bubble.them { background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.08); border-bottom-left-radius: 4px; color: #222; }
        .bubble.me { background: #CC0000; color: #fff; border-bottom-right-radius: 4px; }

        .question-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-top: 8px; margin-bottom: 8px; animation: fadeIn 0.3s ease; }
        .question-prompt { font-size: 15px; font-weight: 700; color: #111; margin-bottom: 16px; }

        .options { display: flex; flex-direction: column; gap: 10px; }
        .option-btn {
            background: #f9f9f9;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 14px;
            color: #333;
            cursor: pointer;
            text-align: left;
            transition: border-color 0.15s, background 0.15s;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .option-btn:hover:not(:disabled) { border-color: #CC0000; background: #fff5f5; }
        .option-btn:disabled { cursor: default; }
        .option-btn.selected-correct { border-color: #16a34a; background: #f0fdf4; color: #15803d; }
        .option-btn.selected-wrong { border-color: #dc2626; background: #fef2f2; color: #dc2626; }
        .option-btn.other-correct { border-color: #16a34a; background: #f0fdf4; opacity: 0.6; }
        .option-key { font-weight: 900; font-size: 13px; min-width: 20px; margin-top: 1px; }

        .feedback-box { margin-top: 16px; padding: 14px 16px; border-radius: 10px; font-size: 13px; line-height: 1.6; display: none; animation: fadeIn 0.3s ease; }
        .feedback-box.correct { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
        .feedback-box.wrong { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }

        .continue-btn {
            display: none;
            margin-top: 16px;
            width: 100%;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.5px;
            transition: background 0.15s;
            animation: fadeIn 0.3s ease;
        }
        .continue-btn:hover { background: #CC0000; }
        .continue-btn.next-scenario { background: #CC0000; }
        .continue-btn.next-scenario:hover { background: #aa0000; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        .typing { display: flex; align-items: center; gap: 4px; padding: 12px 16px; background: #fff; border-radius: 16px; border-bottom-left-radius: 4px; width: fit-content; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .dot { width: 7px; height: 7px; background: #bbb; border-radius: 50%; animation: bounce 1.2s infinite; }
        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce { 0%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-6px); } }

        .bottom-spacer { height: 40px; }

        /* Mascote fixo no canto durante o cenario */
        .mascote-fixo {
            position: fixed;
            bottom: 0;
            right: 16px;
            width: 130px;
            height: auto;
            opacity: 0.85;
            pointer-events: none;
            z-index: 5;
            filter: drop-shadow(0 6px 14px rgba(0,0,0,0.18));
            animation: floatYsmall 4s ease-in-out infinite;
        }
        @keyframes floatYsmall {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        @media (max-width: 900px) { .mascote-fixo { display: none; } }

        /* Mascote no feedback (aparece entre as opcoes e a caixa de feedback) */
        .feedback-mascot-wrap {
            display: none;
            justify-content: center;
            margin: 18px auto 4px;
            animation: popIn 0.45s ease;
        }
        .feedback-mascot-wrap img {
            width: 110px;
            height: auto;
            filter: drop-shadow(0 6px 12px rgba(0,0,0,0.18));
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(0.6); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Mascote "ola" no inicio do cenario */
        .mascote-intro {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #fff;
            border-radius: 14px;
            padding: 14px 18px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 14px;
            border-left: 4px solid #CC0000;
            animation: slideInRight 0.5s ease;
        }
        .mascote-intro img {
            width: 60px;
            height: auto;
            flex-shrink: 0;
        }
        .mascote-intro .intro-text {
            font-size: 13px;
            color: #444;
            line-height: 1.5;
        }
        .mascote-intro .intro-text strong { color: #CC0000; }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="brand-name">🛡️ GUARDIÃO DIGITAL</div>
    <div class="progress-info">Cenário <strong>{{ $position }}</strong> de <strong>{{ $total }}</strong></div>
</div>

<div class="scenario-bar">
    <div class="s-avatar" style="background: {{ $scenario->bg_color }}20;">{{ $scenario->avatar }}</div>
    <div>
        <div class="s-info-label">{{ $scenario->label }}</div>
        <div class="s-info-sub">{{ $scenario->preview }}</div>
    </div>
    <div class="question-counter">Pergunta <strong id="qCurrent">1</strong> de <strong id="qTotal">1</strong></div>
</div>

<div class="chat-area" id="chatArea">
    <div class="mascote-intro">
        <img src="/images/mascote/guardiao-ola.png" alt="Guardião">
        <div class="intro-text">
            <strong>Atenção!</strong> Este é um cenário simulado. Leia as mensagens com calma e tome a decisão mais segura possível. Estou aqui para te ajudar! 🛡️
        </div>
    </div>
</div>

<img src="/images/mascote/guardiao-parado.png" alt="" class="mascote-fixo">

<div class="bottom-spacer"></div>

<script>
const messages       = @json($scenario->content['messages']);
const scenarioId     = {{ $scenario->id }};
const csrfToken      = document.querySelector('meta[name="csrf-token"]').content;
const answerUrl      = '{{ route("training.answer") }}';
const nextScenarioFallback = '{{ route("training.index") }}';

// ─── Agrupa mensagens em "chunks", cada um termina com uma pergunta ──────
const chunks = [];
let buffer = [];
for (const msg of messages) {
    if (msg.type === 'question') {
        chunks.push({ texts: buffer, question: msg });
        buffer = [];
    } else if (msg.type === 'text') {
        buffer.push(msg);
    }
}
// Mensagens de texto após a última pergunta (epílogo opcional)
const epilogue = buffer;

document.getElementById('qTotal').textContent = chunks.length;

const chatArea = document.getElementById('chatArea');
let questionStartTime = null;

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

// Detecta qual elemento rola: chat-area (overflow interno) ou o window (página inteira)
function getScrollContext() {
    // Se o chat-area tem scroll interno usável, usa ele
    if (chatArea.scrollHeight > chatArea.clientHeight + 4) {
        return {
            getStart: () => chatArea.scrollTop,
            getTarget: () => chatArea.scrollHeight - chatArea.clientHeight,
            setScroll: (v) => { chatArea.scrollTop = v; },
        };
    }
    // Senão, rola o window
    return {
        getStart: () => window.scrollY || window.pageYOffset || document.documentElement.scrollTop,
        getTarget: () => document.documentElement.scrollHeight - window.innerHeight,
        setScroll: (v) => { window.scrollTo(0, v); },
    };
}

// Scroll suave e lento até o fim (ease-in-out)
function smoothScrollToBottom(duration = 700) {
    return new Promise((resolve) => {
        const ctx = getScrollContext();
        const start = ctx.getStart();
        const target = ctx.getTarget();
        const distance = target - start;
        if (Math.abs(distance) < 4) { resolve(); return; }

        const startTime = performance.now();
        function step(now) {
            const elapsed = now - startTime;
            const t = Math.min(elapsed / duration, 1);
            const eased = 0.5 - 0.5 * Math.cos(Math.PI * t);
            ctx.setScroll(start + distance * eased);
            if (t < 1) requestAnimationFrame(step);
            else resolve();
        }
        requestAnimationFrame(step);
    });
}

function quickScrollToBottom() {
    const ctx = getScrollContext();
    ctx.setScroll(ctx.getTarget());
}

function addBubble(from, body) {
    const wrap = document.createElement('div');
    wrap.className = 'msg ' + from;
    const safe = String(body).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    wrap.innerHTML = `<div class="bubble ${from}">${safe}</div>`;
    chatArea.appendChild(wrap);
    smoothScrollToBottom(450);
}

function showTyping() {
    const wrap = document.createElement('div');
    wrap.className = 'msg them';
    wrap.id = 'typing';
    wrap.innerHTML = `<div class="typing"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>`;
    chatArea.appendChild(wrap);
    smoothScrollToBottom(400);
}

function removeTyping() {
    const t = document.getElementById('typing');
    if (t) t.remove();
}

async function renderTexts(texts) {
    for (let i = 0; i < texts.length; i++) {
        const msg = texts[i];
        if (msg.from === 'them') {
            showTyping();
            await sleep(900);
            removeTyping();
        } else {
            await sleep(400);
        }
        addBubble(msg.from === 'them' ? 'them' : 'me', msg.body);
        await sleep(300);
    }
}

async function renderQuestion(q, questionIndex, isLastChunk) {
    await sleep(400);

    const card = document.createElement('div');
    card.className = 'question-card';

    const optionsHtml = q.options.map(opt => `
        <button type="button" class="option-btn" data-key="${opt.key}">
            <span class="option-key">${opt.key.toUpperCase()}</span>
            <span>${opt.text.replace(/</g, '&lt;')}</span>
        </button>
    `).join('');

    card.innerHTML = `
        <div class="question-prompt">💬 ${q.prompt.replace(/</g, '&lt;')}</div>
        <div class="options">${optionsHtml}</div>
        <div class="feedback-mascot-wrap"><img src="" alt=""></div>
        <div class="feedback-box"></div>
        <button class="continue-btn" type="button">Continuar →</button>
    `;

    chatArea.appendChild(card);
    // Scroll lento até a nova pergunta — dá tempo do usuário perceber que tem mais
    smoothScrollToBottom(1200);
    questionStartTime = Date.now();

    return new Promise((resolve) => {
        card.querySelectorAll('.option-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (card.dataset.answered) return;
                card.dataset.answered = '1';

                const chosenKey = this.dataset.key;
                const elapsed = questionStartTime ? Date.now() - questionStartTime : 0;

                // Desabilita todas as opções e mostra estado visual
                card.querySelectorAll('.option-btn').forEach(b => b.disabled = true);

                // POST AJAX
                let data;
                try {
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    formData.append('scenario_id', scenarioId);
                    formData.append('question_index', questionIndex);
                    formData.append('chosen_option_key', chosenKey);
                    formData.append('response_time_ms', elapsed);

                    const res = await fetch(answerUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    data = await res.json();
                } catch (err) {
                    alert('Erro ao enviar resposta: ' + err.message);
                    card.querySelectorAll('.option-btn').forEach(b => b.disabled = false);
                    delete card.dataset.answered;
                    return;
                }

                // Marca a opção escolhida e revela a correta
                card.querySelectorAll('.option-btn').forEach(b => {
                    if (b.dataset.key === chosenKey) {
                        b.classList.add(data.is_correct ? 'selected-correct' : 'selected-wrong');
                    } else if (!data.is_correct) {
                        // Quando errou, destacar a correta seria revelar — só faz se o backend mandar
                        // (no momento o backend não retorna qual era a correta, então deixa só selecionado)
                    }
                });

                // Mostra mascote do feedback (vitoria ou explicando)
                const mascotWrap = card.querySelector('.feedback-mascot-wrap');
                const mascotImg = mascotWrap.querySelector('img');
                mascotImg.src = data.is_correct
                    ? '/images/mascote/guardiao-vitoria.png'
                    : '/images/mascote/guardiao-explicando.png';
                mascotImg.alt = data.is_correct ? 'Acertou!' : 'Vamos aprender';
                mascotWrap.style.display = 'flex';

                // Mostra feedback
                const fbox = card.querySelector('.feedback-box');
                fbox.className = 'feedback-box ' + (data.is_correct ? 'correct' : 'wrong');
                fbox.textContent = data.feedback;
                fbox.style.display = 'block';

                // Botão de continuar
                const contBtn = card.querySelector('.continue-btn');
                if (data.scenario_complete) {
                    if (data.training_complete) {
                        contBtn.textContent = '🎉 Ver Meu Resultado →';
                    } else {
                        contBtn.textContent = 'Próximo Cenário →';
                    }
                    contBtn.classList.add('next-scenario');
                    contBtn.addEventListener('click', () => {
                        window.location.href = data.next_url || nextScenarioFallback;
                    });
                } else {
                    contBtn.textContent = 'Continuar →';
                    contBtn.addEventListener('click', () => {
                        contBtn.style.display = 'none';
                        resolve();
                    });
                }
                contBtn.style.display = 'block';
                // Scroll lento até o feedback + botão continuar
                smoothScrollToBottom(800);
            });
        });
    });
}

async function run() {
    for (let i = 0; i < chunks.length; i++) {
        document.getElementById('qCurrent').textContent = (i + 1);
        await renderTexts(chunks[i].texts);
        await renderQuestion(chunks[i].question, i, i === chunks.length - 1);
    }
    // Epílogo (se houver) — texto pós-última pergunta
    if (epilogue.length > 0) {
        await renderTexts(epilogue);
    }
}

run();
</script>
</body>
</html>
