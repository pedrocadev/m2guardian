<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $scenario->label }} — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f5f7; color: #111; min-height: 100vh; display: flex; flex-direction: column; }

        .header { background: #111; border-bottom: 3px solid #CC0000; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 56px; flex-shrink: 0; }
        .brand-name { color: #fff; font-weight: 900; font-size: 14px; letter-spacing: 1px; }
        .progress-info { color: #888; font-size: 13px; }
        .progress-info strong { color: #fff; }

        .scenario-bar { background: #fff; border-bottom: 1px solid #eee; padding: 12px 24px; display: flex; align-items: center; gap: 14px; flex-shrink: 0; }
        .s-avatar { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .s-info-label { font-size: 15px; font-weight: 700; }
        .s-info-sub { font-size: 12px; color: #888; }

        .chat-area { flex: 1; overflow-y: auto; padding: 24px; max-width: 680px; width: 100%; margin: 0 auto; }

        .msg { display: flex; margin-bottom: 12px; animation: fadeIn 0.3s ease; }
        .msg.them { justify-content: flex-start; }
        .msg.me { justify-content: flex-end; }
        .bubble { max-width: 72%; padding: 12px 16px; border-radius: 16px; font-size: 14px; line-height: 1.6; }
        .bubble.them { background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.08); border-bottom-left-radius: 4px; color: #222; }
        .bubble.me { background: #CC0000; color: #fff; border-bottom-right-radius: 4px; }

        .question-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-top: 8px; animation: fadeIn 0.3s ease; }
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
        .option-btn:hover { border-color: #CC0000; background: #fff5f5; }
        .option-btn.selected-correct { border-color: #16a34a; background: #f0fdf4; color: #15803d; }
        .option-btn.selected-wrong { border-color: #dc2626; background: #fef2f2; color: #dc2626; }
        .option-btn.other-correct { border-color: #16a34a; background: #f0fdf4; opacity: 0.6; }
        .option-key { font-weight: 900; font-size: 13px; min-width: 20px; margin-top: 1px; }

        .feedback-box { margin-top: 16px; padding: 14px 16px; border-radius: 10px; font-size: 13px; line-height: 1.6; display: none; }
        .feedback-box.correct { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
        .feedback-box.wrong { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }

        .next-btn {
            display: none;
            width: 100%;
            margin-top: 16px;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.5px;
        }
        .next-btn:hover { background: #CC0000; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        .typing { display: flex; align-items: center; gap: 4px; padding: 12px 16px; background: #fff; border-radius: 16px; border-bottom-left-radius: 4px; width: fit-content; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .dot { width: 7px; height: 7px; background: #bbb; border-radius: 50%; animation: bounce 1.2s infinite; }
        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce { 0%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-6px); } }

        .bottom-spacer { height: 40px; }
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
</div>

<div class="chat-area" id="chatArea">
    <!-- mensagens inseridas via JS -->
</div>

<form id="answerForm" method="POST" action="{{ route('training.answer') }}" style="display:none;">
    @csrf
    <input type="hidden" name="scenario_id" value="{{ $scenario->id }}">
    <input type="hidden" name="question_index" value="0">
    <input type="hidden" name="chosen_option_key" id="chosenKey">
    <input type="hidden" name="response_time_ms" id="responseTime">
</form>

<script>
const messages = @json($scenario->content['messages']);
const chatArea = document.getElementById('chatArea');
let startTime = null;

function addBubble(from, body, delay) {
    return new Promise(resolve => {
        setTimeout(() => {
            const wrap = document.createElement('div');
            wrap.className = 'msg ' + from;
            wrap.innerHTML = `<div class="bubble ${from}">${body}</div>`;
            chatArea.appendChild(wrap);
            chatArea.scrollTop = chatArea.scrollHeight;
            resolve();
        }, delay);
    });
}

function showTyping() {
    const wrap = document.createElement('div');
    wrap.className = 'msg them';
    wrap.id = 'typing';
    wrap.innerHTML = `<div class="typing"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>`;
    chatArea.appendChild(wrap);
    chatArea.scrollTop = chatArea.scrollHeight;
}

function removeTyping() {
    const t = document.getElementById('typing');
    if (t) t.remove();
}

async function renderMessages() {
    let delay = 0;

    for (let i = 0; i < messages.length; i++) {
        const msg = messages[i];

        if (msg.type === 'text') {
            const d = delay;
            delay += 800;

            setTimeout(() => {
                removeTyping();
                if (msg.from === 'them' && i + 1 < messages.length) showTyping();
                addBubble(msg.from === 'them' ? 'them' : 'me', msg.body, 0);
            }, d);

        } else if (msg.type === 'question') {
            setTimeout(() => {
                removeTyping();
                renderQuestion(msg);
                startTime = Date.now();
            }, delay + 400);
        }
    }
}

function renderQuestion(q) {
    const card = document.createElement('div');
    card.className = 'question-card';

    const keys = ['a', 'b', 'c', 'd'];
    let optionsHtml = q.options.map(opt => `
        <button type="button" class="option-btn" data-key="${opt.key}" data-correct="${opt.correct}" data-feedback="${encodeURIComponent(opt.feedback || '')}">
            <span class="option-key">${opt.key.toUpperCase()}</span>
            <span>${opt.text}</span>
        </button>
    `).join('');

    const feedbackId = 'feedback-' + Math.random().toString(36).slice(2);
    const nextLabel = {{ $position }} < {{ $total }} ? 'Próximo cenário →' : 'Ver meu resultado →';

    card.innerHTML = `
        <div class="question-prompt">💬 ${q.prompt}</div>
        <div class="options">${optionsHtml}</div>
        <div class="feedback-box" id="${feedbackId}"></div>
        <button class="next-btn" id="nextBtn" type="button">${nextLabel}</button>
    `;

    chatArea.appendChild(card);
    chatArea.scrollTop = chatArea.scrollHeight;

    card.querySelectorAll('.option-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (card.dataset.answered) return;
            card.dataset.answered = '1';

            const chosenKey = this.dataset.key;
            const isCorrect = this.dataset.correct === 'true';
            const feedback = decodeURIComponent(this.dataset.feedback);
            const elapsed = startTime ? Date.now() - startTime : 0;

            // Visual feedback
            card.querySelectorAll('.option-btn').forEach(b => {
                b.disabled = true;
                if (b.dataset.key === chosenKey) {
                    b.classList.add(isCorrect ? 'selected-correct' : 'selected-wrong');
                } else if (b.dataset.correct === 'true') {
                    b.classList.add('other-correct');
                }
            });

            const fbox = document.getElementById(feedbackId);
            fbox.className = 'feedback-box ' + (isCorrect ? 'correct' : 'wrong');
            fbox.innerHTML = (isCorrect ? '✅ ' : '❌ ') + feedback;
            fbox.style.display = 'block';

            document.getElementById('chosenKey').value = chosenKey;
            document.getElementById('responseTime').value = elapsed;

            const nextBtn = document.getElementById('nextBtn');
            nextBtn.style.display = 'block';
            nextBtn.addEventListener('click', () => {
                document.getElementById('answerForm').submit();
            });

            chatArea.scrollTop = chatArea.scrollHeight;
        });
    });
}

renderMessages();
</script>
<div class="bottom-spacer"></div>
</body>
</html>
