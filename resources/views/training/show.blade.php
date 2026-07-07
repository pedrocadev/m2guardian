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
            background-image: url('/images/backgrounds/training-show.jpg');
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
        .progress-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
            color: #888;
            font-size: 12px;
            min-width: 140px;
        }
        .progress-info strong { color: #fff; font-weight: 800; }
        .progress-track {
            width: 140px;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.4s ease;
        }
        .progress-fill.red    { background: linear-gradient(90deg, #dc2626, #ef4444); }
        .progress-fill.yellow { background: linear-gradient(90deg, #d97706, #f59e0b); }
        .progress-fill.green  { background: linear-gradient(90deg, #16a34a, #22c55e); }

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

        .answered-tag {
            display: inline-block;
            background: #f3f4f6;
            color: #6b7280;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 6px;
            margin-bottom: 12px;
            border: 1px solid #e5e7eb;
        }

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
        .option-btn.faded { opacity: 0.35; transition: opacity 0.3s ease; cursor: not-allowed; }
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

        /* Elementos que sempre existem mas só aparecem em modos específicos */
        .msg-time, .msg-check, .s-info-online, .wapp-header-icons { display: none; }
        .s-info { display: flex; flex-direction: column; gap: 2px; }

        /* Regras compartilhadas entre todos os modos de plataforma (wapp / teams / email) */
        .platform-wapp .chat-main,
        .platform-teams .chat-main,
        .platform-email .chat-main {
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 0;
        }
        .platform-wapp .chat-main .chat-area,
        .platform-teams .chat-main .chat-area,
        .platform-email .chat-main .chat-area {
            max-width: 100%;
            margin: 0;
        }
        .platform-wapp .mascote-fixo,
        .platform-teams .mascote-fixo,
        .platform-email .mascote-fixo {
            display: none;
        }

        /* ═══════════════════════════════════════════════════════════
           MODO WHATSAPP — ativa via body.platform-wapp
           ═══════════════════════════════════════════════════════════ */
        body.platform-wapp {
            font-family: 'Segoe UI', 'Helvetica Neue', -apple-system, sans-serif;
        }
        body.platform-wapp::before {
            background: rgba(236, 229, 221, 0.85); /* tinta bege WhatsApp */
        }

        /* Header do "contato" (scenario-bar) — verde WhatsApp */
        .platform-wapp .scenario-bar {
            background: #075E54;
            border-bottom: none;
            padding: 10px 16px;
            color: #fff;
        }
        .platform-wapp .s-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15) !important;
            color: #fff;
            font-size: 22px;
            flex-shrink: 0;
        }
        .platform-wapp .s-info-label {
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0;
        }
        .platform-wapp .s-info-sub { display: none; }
        .platform-wapp .s-info-online {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #bde5c9;
        }
        .platform-wapp .online-dot {
            width: 8px; height: 8px;
            background: #25D366;
            border-radius: 50%;
        }
        .platform-wapp .wapp-header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: auto;
            color: #fff;
        }
        .platform-wapp .wapp-header-icons svg {
            width: 20px; height: 20px;
        }
        .platform-wapp .question-counter {
            background: rgba(255,255,255,0.12);
            color: #eafbf0;
            margin-left: 16px;
        }
        .platform-wapp .question-counter strong { color: #fff; }

        /* Wallpaper do WhatsApp — mantém a coluna estreita do chat */
        .platform-wapp .chat-area {
            background-color: #ECE5DD;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'><g fill='%23000' fill-opacity='0.04'><circle cx='20' cy='20' r='3'/><path d='M60 40 L64 48 L72 48 L66 54 L68 62 L60 58 L52 62 L54 54 L48 48 L56 48 Z'/><circle cx='100' cy='30' r='2'/><path d='M30 80 q4 -8 8 0 t8 0'/><circle cx='90' cy='95' r='2.5'/><rect x='15' y='95' width='10' height='6' rx='1'/></g></svg>");
            max-width: 560px;
            padding: 16px 12px;
        }

        /* Bolhas com tail (rabinho) — regra única compartilhada por bolhas/pergunta/typing/feedback */
        .platform-wapp .msg { margin-bottom: 4px; }
        .platform-wapp .bubble {
            position: relative;
            padding: 6px 8px 6px 10px;
            border-radius: 8px;
            font-size: 14.5px;
            line-height: 1.35;
            max-width: 68%;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        }
        .platform-wapp .bubble.them,
        .platform-wapp .bubble.me,
        .platform-wapp .question-prompt,
        .platform-wapp .typing,
        .platform-wapp .feedback-box {
            position: relative;
        }
        .platform-wapp .bubble.them::before,
        .platform-wapp .question-prompt::before,
        .platform-wapp .typing::before,
        .platform-wapp .feedback-box::before,
        .platform-wapp .bubble.me::before {
            content: '';
            position: absolute;
            top: 0;
            width: 8px; height: 13px;
            background: inherit;
        }
        /* Tail à esquerda (mensagens recebidas + sistema) */
        .platform-wapp .bubble.them::before,
        .platform-wapp .question-prompt::before,
        .platform-wapp .typing::before,
        .platform-wapp .feedback-box::before {
            left: -8px;
            clip-path: polygon(100% 0, 100% 100%, 0 0);
        }
        /* Tail à direita (mensagens enviadas + primeira opção da quick-reply) */
        .platform-wapp .bubble.me::before,
        .platform-wapp .option-btn:first-child::before {
            right: -8px;
            clip-path: polygon(0 0, 0 100%, 100% 0);
        }
        .platform-wapp .option-btn:first-child::before {
            content: '';
            position: absolute;
            top: 0;
            width: 8px; height: 13px;
            background: inherit;
        }
        .platform-wapp .bubble.them {
            background: #fff;
            color: #111;
            border-top-left-radius: 0;
        }
        .platform-wapp .bubble.me {
            background: #DCF8C6;
            color: #111;
            border-top-right-radius: 0;
        }

        /* Timestamp + check dentro da bolha */
        .platform-wapp .msg-time {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10.5px;
            color: #667781;
            float: right;
            margin: 6px 0 -2px 8px;
            white-space: nowrap;
            font-family: inherit;
        }
        .platform-wapp .msg-check {
            display: inline;
            color: #4FC3F7;
            font-weight: 700;
            letter-spacing: -2px;
        }

        /* Typing indicator estilo WhatsApp (tail já vem via seletor agrupado acima) */
        .platform-wapp .typing {
            background: #fff;
            border-top-left-radius: 0;
            border-radius: 8px;
        }

        /* Question card → transparente, pergunta vira bolha them, opções viram bolhas me */
        .platform-wapp .question-card {
            background: transparent;
            box-shadow: none;
            padding: 0;
            margin: 4px 0;
        }
        .platform-wapp .question-prompt {
            background: #fff;
            color: #111;
            padding: 8px 12px;
            border-radius: 8px;
            border-top-left-radius: 0;
            font-size: 14.5px;
            font-weight: 500;
            margin-bottom: 10px;
            max-width: 68%;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        }
        .platform-wapp .answered-tag {
            display: block;
            background: rgba(255,255,255,0.85);
            color: #666;
            width: fit-content;
            margin-bottom: 8px;
        }

        /* Opções → bolhas verdes alinhadas à direita (quick-reply) */
        .platform-wapp .options {
            align-items: flex-end;
            gap: 4px;
        }
        .platform-wapp .option-btn {
            background: #DCF8C6;
            color: #111;
            border: 1px solid transparent;
            border-radius: 8px;
            border-top-right-radius: 0;
            padding: 8px 12px;
            font-size: 14.5px;
            max-width: 68%;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
            position: relative;
            justify-content: flex-start;
            display: flex;
            transition: transform 0.12s ease, background 0.15s;
        }
        .platform-wapp .option-btn:hover:not(:disabled) {
            background: #c8f0b1;
            transform: translateX(-2px);
            border-color: transparent;
        }
        .platform-wapp .option-btn.selected-correct {
            background: #25D366;
            color: #fff;
            border-color: transparent;
        }
        .platform-wapp .option-btn.selected-wrong {
            background: #dc2626;
            color: #fff;
            border-color: transparent;
        }
        .platform-wapp .option-key {
            display: none;
        }

        /* Feedback vira 'resposta do sistema' — bolha branca com remetente */
        .platform-wapp .feedback-mascot-wrap {
            display: none !important;
        }
        .platform-wapp .feedback-box {
            background: #fff;
            border: none;
            color: #111;
            border-radius: 8px;
            border-top-left-radius: 0;
            padding: 8px 12px;
            margin-top: 10px;
            font-size: 14px;
            max-width: 80%;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        }
        .platform-wapp .feedback-box::after {
            content: 'Guardião Digital';
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #075E54;
            margin-bottom: 4px;
            order: -1;
        }
        .platform-wapp .feedback-box.correct { color: #111; border-left: 3px solid #25D366; padding-left: 10px; }
        .platform-wapp .feedback-box.wrong { color: #111; border-left: 3px solid #dc2626; padding-left: 10px; }

        /* Botão continuar → verde WhatsApp */
        .platform-wapp .continue-btn {
            background: #25D366;
            border-radius: 24px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(37,211,102,0.3);
        }
        .platform-wapp .continue-btn:hover { background: #128C7E; }
        .platform-wapp .continue-btn.next-scenario { background: #128C7E; }
        .platform-wapp .continue-btn.next-scenario:hover { background: #075E54; }

        /* Intro do mascote — visual mais discreto no modo WhatsApp */
        .platform-wapp .mascote-intro {
            background: rgba(255,255,255,0.9);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        @media (max-width: 700px) {
            .platform-wapp .wapp-header-icons { display: none; }
            .platform-wapp .question-counter { display: none; }
        }

        /* ═══════════════════════════════════════════════════════════
           LAYOUT 2-COLUNAS DO WHATSAPP WEB
           ═══════════════════════════════════════════════════════════ */
        .chat-wrapper { display: contents; }

        .platform-wapp .chat-wrapper {
            display: grid;
            grid-template-columns: minmax(320px, 380px) 1fr;
            flex: 1;
            min-height: 0;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px 20px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        /* .chat-main + .chat-area do modo wapp: usam regras compartilhadas acima */

        /* ─── SIDEBAR ─────────────────────────────────────────────── */
        .wapp-sidebar {
            background: #fff;
            border-right: 1px solid #e6e6e6;
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        .wapp-sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            background: #f0f2f5;
            flex-shrink: 0;
        }
        .wapp-user-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            overflow: hidden;
        }
        .wapp-user-avatar img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        .wapp-sidebar-icons {
            display: flex;
            align-items: center;
            gap: 22px;
            color: #54656f;
        }
        .wapp-sidebar-icons svg {
            width: 22px; height: 22px;
            cursor: pointer;
        }

        .wapp-search {
            padding: 8px 12px;
            background: #f6f6f6;
            border-bottom: 1px solid #e6e6e6;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #888;
            font-size: 14px;
        }
        .wapp-search svg { width: 18px; height: 18px; flex-shrink: 0; }
        .wapp-search span { flex: 1; }

        .wapp-chat-list {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }
        .wapp-chat-list::-webkit-scrollbar { width: 6px; }
        .wapp-chat-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }

        .wapp-chat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f2f5;
            transition: background 0.15s, opacity 0.15s;
            text-decoration: none;
            color: inherit;
        }

        /* Cenário concluído — clicável (modo revisão) */
        .wapp-chat-item.completed { cursor: pointer; }
        .wapp-chat-item.completed:hover { background: #f5f6f6; }

        /* Cenário em progresso ou próximo pendente — clicável (continuar) */
        .wapp-chat-item.available { cursor: pointer; }
        .wapp-chat-item.available:hover { background: #f5f6f6; }

        /* Cenário atual — destacado, não clicável */
        .wapp-chat-item.active {
            background: #f0f2f5;
            cursor: default;
        }

        /* Cenário pendente — não clicável, esmaecido */
        .wapp-chat-item.locked {
            cursor: not-allowed;
            opacity: 0.55;
        }
        .wapp-chat-item.locked:hover { background: transparent; }

        .wapp-chat-checkmark {
            color: #4FC3F7;
            font-weight: 700;
            letter-spacing: -2px;
            margin-right: 4px;
        }
        .wapp-chat-lock {
            font-size: 11px;
            opacity: 0.5;
            flex-shrink: 0;
        }

        .wapp-chat-avatar {
            width: 48px; height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .wapp-chat-body {
            flex: 1;
            min-width: 0;
            border-bottom: 1px solid transparent;
        }
        .wapp-chat-line1 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        .wapp-chat-name {
            font-size: 14.5px;
            font-weight: 500;
            color: #111b21;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .wapp-chat-time {
            font-size: 11.5px;
            color: #667781;
            flex-shrink: 0;
        }
        .wapp-chat-line2 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }
        .wapp-chat-preview {
            font-size: 13px;
            color: #667781;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }
        .wapp-chat-badge {
            background: #25D366;
            color: #fff;
            font-size: 20px;
            line-height: 1;
            padding: 0;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .wapp-chat-item.active .wapp-chat-time { color: #667781; }

        /* .mascote-fixo escondido em todos os modos: regra compartilhada acima */

        /* Ajusta o body pra flexbox horizontal ocupar altura toda */
        body.platform-wapp {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* Responsivo: sidebar some em telas menores */
        @media (max-width: 900px) {
            .platform-wapp .chat-wrapper,
            .platform-teams .chat-wrapper {
                grid-template-columns: 1fr;
            }
            .wapp-sidebar { display: none; }
        }

        /* ═══════════════════════════════════════════════════════════
           MODO MICROSOFT TEAMS — ativa via body.platform-teams
           ═══════════════════════════════════════════════════════════ */
        body.platform-teams {
            font-family: 'Segoe UI', 'Helvetica Neue', -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        body.platform-teams::before {
            background: rgba(245, 245, 245, 0.9);
        }

        /* Wrapper 2-colunas */
        .platform-teams .chat-wrapper {
            display: grid;
            grid-template-columns: minmax(300px, 340px) 1fr;
            flex: 1;
            min-height: 0;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px 20px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .platform-teams .chat-main { background: #fff; }

        /* Sidebar (chat list) — visual Teams */
        .platform-teams .wapp-sidebar {
            background: #f5f5f5;
            border-right: 1px solid #e1dfdd;
        }
        .platform-teams .wapp-sidebar-header {
            background: #f5f5f5;
            border-bottom: 1px solid #e1dfdd;
            padding: 16px 20px;
        }
        .teams-sidebar-title {
            font-size: 18px;
            font-weight: 700;
            color: #201f1e;
        }
        .platform-teams .wapp-sidebar-icons {
            color: #616161;
            gap: 18px;
        }
        .platform-teams .wapp-search {
            background: #fff;
            border: 1px solid #e1dfdd;
            border-radius: 4px;
            margin: 12px;
            padding: 8px 12px;
        }

        /* Chat items estilo Teams */
        .platform-teams .wapp-chat-item {
            padding: 10px 16px 10px 14px;
            border-bottom: none;
            border-left: 3px solid transparent;
        }
        .platform-teams .wapp-chat-item.active {
            background: #ebebeb;
            border-left-color: #6264A7;
        }
        .platform-teams .wapp-chat-item.completed:hover,
        .platform-teams .wapp-chat-item.available:hover {
            background: #ececec;
        }
        .platform-teams .wapp-chat-avatar {
            border-radius: 50%;
            width: 44px; height: 44px;
        }
        .platform-teams .wapp-chat-name {
            color: #201f1e;
            font-weight: 600;
        }
        .platform-teams .wapp-chat-time { color: #616161; }
        .platform-teams .wapp-chat-preview { color: #616161; }
        .platform-teams .wapp-chat-checkmark {
            color: #6264A7;
        }
        .platform-teams .wapp-chat-badge {
            background: #6264A7;
        }

        /* Header do chat (scenario-bar) — visual Teams */
        .platform-teams .scenario-bar {
            background: #fff;
            border-bottom: 1px solid #e1dfdd;
            padding: 12px 20px;
            color: #201f1e;
        }
        .platform-teams .s-avatar {
            border-radius: 50%;
            width: 42px; height: 42px;
            flex-shrink: 0;
        }
        .platform-teams .s-info-label {
            color: #201f1e;
            font-size: 15px;
            font-weight: 600;
        }
        .platform-teams .s-info-sub { display: none; }
        .platform-teams .s-info-online {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #616161;
        }
        .platform-teams .online-dot {
            width: 8px; height: 8px;
            background: #6bb700;
            border-radius: 50%;
        }
        .platform-teams .wapp-header-icons {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-left: auto;
            color: #616161;
        }
        .platform-teams .wapp-header-icons svg {
            width: 20px; height: 20px;
        }
        .platform-teams .question-counter {
            background: #f0f0f0;
            color: #616161;
            margin-left: 16px;
        }
        .platform-teams .question-counter strong { color: #6264A7; }

        /* Chat area — fundo branco (Teams não usa wallpaper) */
        .platform-teams .chat-area {
            background: #fff;
            padding: 20px 24px;
        }

        /* Bolhas do Teams — cantos arredondados, sem tail */
        .platform-teams .msg { margin-bottom: 8px; }
        .platform-teams .bubble {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
            max-width: 68%;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        }
        .platform-teams .bubble.them {
            background: #f5f5f5;
            color: #201f1e;
        }
        .platform-teams .bubble.me {
            background: #ebebfa;
            color: #201f1e;
        }
        /* Remove tails do Teams (herdou do bloco genérico) */
        .platform-teams .bubble.them::before,
        .platform-teams .bubble.me::before,
        .platform-teams .question-prompt::before,
        .platform-teams .typing::before,
        .platform-teams .feedback-box::before,
        .platform-teams .option-btn:first-child::before {
            display: none;
        }

        /* Timestamp + check — mais discretos no Teams */
        .platform-teams .msg-time {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #616161;
            float: right;
            margin: 6px 0 -2px 8px;
            font-family: inherit;
        }
        .platform-teams .msg-check { display: none; }

        /* Typing indicator estilo Teams */
        .platform-teams .typing {
            background: #f5f5f5;
            border-radius: 8px;
        }

        /* Pergunta (bolha do "sistema/contato") */
        .platform-teams .question-card {
            background: transparent;
            box-shadow: none;
            padding: 0;
        }
        .platform-teams .question-prompt {
            background: #f5f5f5;
            color: #201f1e;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
            max-width: 68%;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        }

        /* Opções quick-reply — bolhas roxas claras à direita */
        .platform-teams .options {
            align-items: flex-end;
            gap: 6px;
        }
        .platform-teams .option-btn {
            background: #ebebfa;
            color: #201f1e;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            max-width: 75%;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
            justify-content: flex-start;
            display: flex;
            transition: background 0.15s, transform 0.12s;
        }
        .platform-teams .option-btn:hover:not(:disabled) {
            background: #d9d9f7;
            transform: translateX(-2px);
            border-color: transparent;
        }
        .platform-teams .option-btn.selected-correct {
            background: #6264A7;
            color: #fff;
        }
        .platform-teams .option-btn.selected-wrong {
            background: #c50f1f;
            color: #fff;
        }
        .platform-teams .option-key { display: none; }

        /* Feedback como "resposta do sistema" */
        .platform-teams .feedback-mascot-wrap { display: none !important; }
        .platform-teams .feedback-box {
            background: #f5f5f5;
            color: #201f1e;
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 10px;
            font-size: 14px;
            max-width: 80%;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        }
        .platform-teams .feedback-box::after {
            content: 'Guardião Digital';
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #6264A7;
            margin-bottom: 4px;
            order: -1;
        }
        .platform-teams .feedback-box.correct { border-left: 3px solid #6bb700; padding-left: 12px; }
        .platform-teams .feedback-box.wrong   { border-left: 3px solid #c50f1f; padding-left: 12px; }

        /* Botão continuar — visual Teams */
        .platform-teams .continue-btn {
            background: #6264A7;
            border-radius: 4px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(98,100,167,0.3);
        }
        .platform-teams .continue-btn:hover { background: #464775; }
        .platform-teams .continue-btn.next-scenario { background: #464775; }
        .platform-teams .continue-btn.next-scenario:hover { background: #33344A; }

        /* Intro do mascote */
        .platform-teams .mascote-intro {
            background: #f5f5f5;
            border-radius: 8px;
            border-left: 3px solid #6264A7;
        }


        @media (max-width: 700px) {
            .platform-teams .wapp-header-icons { display: none; }
            .platform-teams .question-counter { display: none; }
        }

        /* ═══════════════════════════════════════════════════════════
           MODO E-MAIL — layout Outlook Web (caixa de entrada + envelope)
           ═══════════════════════════════════════════════════════════ */
        body.platform-email {
            font-family: 'Segoe UI', 'Helvetica Neue', -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        body.platform-email::before {
            background: rgba(240, 240, 240, 0.9);
        }

        .platform-email .chat-wrapper {
            display: grid;
            grid-template-columns: minmax(300px, 360px) 1fr;
            flex: 1;
            min-height: 0;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px 20px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .platform-email .chat-main { background: #faf9f8; }
        .platform-email .chat-main .chat-area {
            background: #faf9f8;
            padding: 24px;
        }

        /* Sidebar — caixa de entrada */
        .platform-email .wapp-sidebar {
            background: #fff;
            border-right: 1px solid #e1dfdd;
        }
        .platform-email .wapp-sidebar-header {
            background: #0078d4;
            padding: 14px 20px;
            color: #fff;
        }
        .email-sidebar-title {
            font-size: 17px;
            font-weight: 700;
            color: #fff;
        }
        .platform-email .wapp-sidebar-icons {
            color: #fff;
            gap: 16px;
        }
        .platform-email .wapp-search {
            background: #f3f2f1;
            border: 1px solid #e1dfdd;
            border-radius: 4px;
            margin: 10px;
            padding: 8px 12px;
        }

        /* Itens do inbox */
        .platform-email .wapp-chat-item {
            padding: 12px 16px;
            border-bottom: 1px solid #edebe9;
            border-left: 4px solid transparent;
        }
        .platform-email .wapp-chat-item.active {
            background: #f3f2f1;
            border-left-color: #0078d4;
        }
        .platform-email .wapp-chat-item.completed:hover,
        .platform-email .wapp-chat-item.available:hover {
            background: #f8f8f8;
        }
        .platform-email .wapp-chat-avatar {
            border-radius: 50%;
            width: 40px; height: 40px;
        }
        .platform-email .wapp-chat-name {
            color: #201f1e;
            font-weight: 600;
        }
        .platform-email .wapp-chat-time { color: #605e5c; font-size: 11px; }
        .platform-email .wapp-chat-preview { color: #605e5c; }
        .platform-email .wapp-chat-checkmark { color: #0078d4; }
        .platform-email .wapp-chat-badge { background: #0078d4; }

        /* Header do e-mail aberto (scenario-bar) */
        .platform-email .scenario-bar {
            background: #fff;
            border-bottom: 1px solid #e1dfdd;
            padding: 10px 20px;
            color: #201f1e;
        }
        .platform-email .s-avatar { display: none; }
        .platform-email .s-info-label {
            color: #201f1e;
            font-size: 15px;
            font-weight: 600;
        }
        .platform-email .s-info-sub {
            display: block;
            color: #605e5c;
            font-size: 12px;
        }
        .platform-email .s-info-online { display: none; }
        /* Ícones de câmera/chamada não fazem sentido em e-mail — herdam display: none do base */
        .platform-email .question-counter {
            background: #f0f0f0;
            color: #605e5c;
            margin-left: 16px;
        }
        .platform-email .question-counter strong { color: #0078d4; }

        /* Envelope do e-mail — card branco central */
        .email-envelope {
            background: #fff;
            border: 1px solid #e1dfdd;
            border-radius: 6px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            margin: 0 auto 20px;
            max-width: 780px;
            overflow: hidden;
            animation: fadeIn 0.4s ease;
        }
        .email-subject-bar {
            padding: 18px 24px 12px;
            font-size: 20px;
            font-weight: 600;
            color: #201f1e;
            line-height: 1.3;
        }
        .email-meta-bar {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 24px;
            border-top: 1px solid #edebe9;
            border-bottom: 1px solid #edebe9;
            background: #faf9f8;
        }
        .email-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .email-meta-info { flex: 1; min-width: 0; }
        .email-meta-line1 {
            font-size: 14px;
            display: flex;
            gap: 8px;
            align-items: baseline;
            flex-wrap: wrap;
        }
        .email-from-name { font-weight: 700; color: #201f1e; }
        .email-from-address { color: #605e5c; font-size: 13px; }
        .email-meta-line2 {
            font-size: 12px;
            color: #605e5c;
            margin-top: 3px;
        }
        .email-meta-line2 strong { color: #201f1e; }
        .email-actions-top {
            display: flex;
            gap: 16px;
            color: #605e5c;
        }
        .email-actions-top svg {
            width: 20px; height: 20px;
            cursor: pointer;
        }
        .email-body {
            padding: 24px;
            font-size: 15px;
            line-height: 1.7;
            color: #201f1e;
            background: #fff;
        }
        .email-paragraph {
            margin: 0 0 14px;
            white-space: pre-wrap;
        }
        .email-paragraph:last-child { margin-bottom: 0; }
        .email-actions-bottom {
            display: flex;
            gap: 10px;
            padding: 12px 24px;
            border-top: 1px solid #edebe9;
            background: #faf9f8;
        }
        .email-btn {
            background: #fff;
            border: 1px solid #e1dfdd;
            border-radius: 4px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #201f1e;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.15s;
        }
        .email-btn:hover { background: #f3f2f1; }
        .email-btn span { color: #0078d4; margin-right: 4px; }

        /* Esconde bolhas .msg no modo email (mensagens já viraram parágrafos) */
        .platform-email .msg { display: none; }
        .platform-email .typing { display: none !important; }

        /* Pergunta + opções — card destacado abaixo do e-mail */
        .platform-email .question-card {
            background: #fff;
            border: 1px solid #e1dfdd;
            border-radius: 6px;
            padding: 20px 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            margin: 20px auto 8px;
            max-width: 780px;
        }
        .platform-email .question-prompt {
            background: transparent;
            padding: 0;
            font-size: 16px;
            font-weight: 700;
            color: #201f1e;
            margin-bottom: 16px;
            max-width: 100%;
            box-shadow: none;
        }
        .platform-email .options {
            gap: 10px;
            align-items: stretch;
        }
        .platform-email .option-btn {
            background: #fff;
            color: #201f1e;
            border: 1.5px solid #e1dfdd;
            border-radius: 4px;
            padding: 12px 16px;
            font-size: 14px;
            max-width: 100%;
            box-shadow: none;
            justify-content: flex-start;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transition: border-color 0.15s, background 0.15s;
        }
        .platform-email .option-btn:hover:not(:disabled) {
            border-color: #0078d4;
            background: #f3f9fd;
        }
        .platform-email .option-btn.selected-correct {
            background: #dff6dd;
            border-color: #107c10;
            color: #107c10;
        }
        .platform-email .option-btn.selected-wrong {
            background: #fde7e9;
            border-color: #c50f1f;
            color: #c50f1f;
        }
        .platform-email .option-key {
            display: inline-block;
            font-weight: 700;
            color: #0078d4;
            font-size: 13px;
            min-width: 20px;
        }

        /* Feedback como card do "Guardião" */
        .platform-email .feedback-mascot-wrap { display: none !important; }
        .platform-email .feedback-box {
            background: #f3f9fd;
            border: 1px solid #b3d7ee;
            border-radius: 6px;
            color: #201f1e;
            padding: 14px 18px;
            margin-top: 16px;
            font-size: 14px;
        }
        .platform-email .feedback-box::after {
            content: '🛡️ Guardião Digital';
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #0078d4;
            margin-bottom: 6px;
            order: -1;
        }
        .platform-email .feedback-box.correct { background: #dff6dd; border-color: #a8dea8; }
        .platform-email .feedback-box.wrong   { background: #fde7e9; border-color: #f0b8bd; }

        .platform-email .continue-btn {
            background: #0078d4;
            border-radius: 4px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0,120,212,0.3);
        }
        .platform-email .continue-btn:hover { background: #106ebe; }
        .platform-email .continue-btn.next-scenario { background: #106ebe; }
        .platform-email .continue-btn.next-scenario:hover { background: #005a9e; }

        .platform-email .mascote-intro {
            background: #fff8e6;
            border-left: 3px solid #d99b00;
            border-radius: 4px;
            max-width: 780px;
            margin: 0 auto 20px;
        }

        @media (max-width: 700px) {
            .platform-email .question-counter { display: none; }
            .platform-email .email-actions-top { display: none; }
        }
    </style>
</head>
<body class="platform-{{ $scenario->platform }}">

<div class="header">
    <div class="brand-name">🛡️ GUARDIÃO DIGITAL</div>
    @php
        $progressPercent = $total > 0 ? round(($position / $total) * 100) : 0;
        $progressColor = $progressPercent < 33 ? 'red' : ($progressPercent < 66 ? 'yellow' : 'green');
    @endphp
    <div class="progress-info">
        <span>Cenário <strong>{{ $position }}</strong> de <strong>{{ $total }}</strong></span>
        <div class="progress-track">
            <div class="progress-fill {{ $progressColor }}" style="width: {{ $progressPercent }}%;"></div>
        </div>
    </div>
</div>

<div class="chat-wrapper">

@if(in_array($scenario->platform, ['wapp', 'teams', 'email']))
<aside class="wapp-sidebar">
    <div class="wapp-sidebar-header">
        @if($scenario->platform === 'email')
            <div class="email-sidebar-title">Caixa de entrada</div>
        @elseif($scenario->platform === 'teams')
            <div class="teams-sidebar-title">Chat</div>
        @else
            <div class="wapp-user-avatar">
                <img src="/images/mascots/training-show-sidebar.png" alt="Você" onerror="this.style.display='none';this.parentElement.innerHTML='🛡️';">
            </div>
        @endif
        <div class="wapp-sidebar-icons">
            @if($scenario->platform === 'email')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" title="Novo e-mail"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" title="Filtrar"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            @elseif($scenario->platform === 'teams')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" title="Filtrar"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" title="Novo chat"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" title="Comunidades"><circle cx="12" cy="7" r="3"/><circle cx="6" cy="16" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" title="Status"><circle cx="12" cy="12" r="9" stroke-dasharray="4 2"/></svg>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" title="Nova conversa"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" title="Menu"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
            @endif
        </div>
    </div>
    <div class="wapp-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.5" y2="16.5"/></svg>
        <span>{{ $scenario->platform === 'email' ? 'Pesquisar e-mails' : ($scenario->platform === 'teams' ? 'Pesquisar' : 'Pesquisar ou começar uma nova conversa') }}</span>
    </div>
    <div class="wapp-chat-list">
        @foreach($scenarios->where('platform', $scenario->platform) as $idx => $s)
            @php
                $isActive     = $s->id === $scenario->id;
                $isCompleted  = $completedScenarioIds->contains($s->id);
                $isReachable  = $reachableScenarioIds->contains($s->id);
                $isClickable  = $isReachable && !$isActive;
                $stateClass   = $isActive
                                    ? 'active'
                                    : ($isCompleted ? 'completed' : ($isReachable ? 'available' : 'locked'));
                $tooltip      = $isActive
                                    ? 'Cenário atual'
                                    : ($isCompleted ? 'Revisar cenário concluído' : ($isReachable ? 'Continuar cenário' : 'Bloqueado — conclua o cenário atual primeiro'));
                $preview      = \Illuminate\Support\Str::limit($s->preview ?: 'Cenário simulado', 42);
                $fakeTime     = sprintf('%02d:%02d', 9 + ($idx % 10), ($idx * 7) % 60);
            @endphp
            @if($isClickable)
                <a href="{{ route('training.show', $s->id) }}" class="wapp-chat-item {{ $stateClass }}" title="{{ $tooltip }}">
            @else
                <div class="wapp-chat-item {{ $stateClass }}" title="{{ $tooltip }}">
            @endif
                <div class="wapp-chat-avatar" style="background: {{ $s->bg_color }}20;">{{ $s->avatar }}</div>
                <div class="wapp-chat-body">
                    <div class="wapp-chat-line1">
                        <span class="wapp-chat-name">{{ $s->label }}</span>
                        <span class="wapp-chat-time">{{ $fakeTime }}</span>
                    </div>
                    <div class="wapp-chat-line2">
                        <span class="wapp-chat-preview">
                            @if($isCompleted)
                                <span class="wapp-chat-checkmark">✓✓</span>
                            @endif
                            {{ $preview }}
                        </span>
                        @if($isActive)
                            <span class="wapp-chat-badge">•</span>
                        @elseif(!$isReachable)
                            <span class="wapp-chat-lock" aria-hidden="true">🔒</span>
                        @endif
                    </div>
                </div>
            @if($isClickable)
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>
</aside>
@endif

<div class="chat-main">

<div class="scenario-bar">
    <div class="s-avatar" style="background: {{ $scenario->bg_color }}20;">{{ $scenario->avatar }}</div>
    <div class="s-info">
        <div class="s-info-label">{{ $scenario->label }}</div>
        <div class="s-info-sub">{{ $scenario->preview }}</div>
        <div class="s-info-online"><span class="online-dot"></span>online</div>
    </div>
    <div class="wapp-header-icons">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
    </div>
    <div class="question-counter">Pergunta <strong id="qCurrent">1</strong> de <strong id="qTotal">1</strong></div>
</div>

<div class="chat-area" id="chatArea">
    <div class="mascote-intro">
        <img src="/images/mascots/training-show-greeting.png" alt="Guardião">
        <div class="intro-text">
            <strong>Atenção!</strong> Este é um cenário simulado. Leia as mensagens com calma e tome a decisão mais segura possível. Estou aqui para te ajudar! 🛡️
        </div>
    </div>

    @if($scenario->platform === 'email')
        @php
            $emailFromName = data_get($scenario->content, 'email_from_name') ?: $scenario->label;
            $emailSubject  = data_get($scenario->content, 'email_subject') ?: ($scenario->preview ?: 'Assunto');
            $emailFromAddr = data_get($scenario->content, 'email_from_address');
            if (empty($emailFromAddr)) {
                $labelSafe     = $scenario->label ?: 'contato';
                $emailFromAddr = strtolower(str_replace(' ', '.', \Illuminate\Support\Str::ascii($labelSafe))) . '@' . \Illuminate\Support\Str::slug($labelSafe, '') . '.com';
            }
            $fakeDate = 'Hoje, ' . now()->format('H:i');
        @endphp
        <div class="email-envelope">
            <div class="email-subject-bar">{{ $emailSubject }}</div>
            <div class="email-meta-bar">
                <div class="email-avatar" style="background: {{ $scenario->bg_color }}30;">{{ $scenario->avatar }}</div>
                <div class="email-meta-info">
                    <div class="email-meta-line1">
                        <span class="email-from-name">{{ $emailFromName }}</span>
                        <span class="email-from-address">&lt;{{ $emailFromAddr }}&gt;</span>
                    </div>
                    <div class="email-meta-line2">Para: <strong>você</strong> • {{ $fakeDate }}</div>
                </div>
                <div class="email-actions-top" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 17 20 12 15 7"/><path d="M4 18v-2a4 4 0 0 1 4-4h12"/></svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
                </div>
            </div>
            <div class="email-body" id="emailBody">
                {{-- Corpo do e-mail é populado pelo JS (mensagens do cenário viram parágrafos) --}}
            </div>
            <div class="email-actions-bottom">
                <button type="button" class="email-btn"><span>↩</span> Responder</button>
                <button type="button" class="email-btn"><span>↪</span> Encaminhar</button>
            </div>
        </div>
    @endif
</div>

<div class="bottom-spacer"></div>

</div> {{-- /chat-main --}}
</div> {{-- /chat-wrapper --}}

<img src="/images/mascots/training-show-sidebar.png" alt="Mascote Guardião" class="mascote-fixo">

<script>
const messages       = @json($scenario->content['messages']);
const previousAnswers = @json($previousAnswers ?? (object)[]);
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

function currentTime() {
    const d = new Date();
    return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
}

const isEmailMode = document.body.classList.contains('platform-email');
const emailBodyEl = document.getElementById('emailBody');

function addBubble(from, body) {
    const safe = String(body).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

    // Modo email: só mensagens 'them' viram parágrafos do envelope; 'me' seria descartado
    // (cenários de email não têm 'me' hoje — mas silencia caso apareça em vez de criar DOM invisível)
    if (isEmailMode) {
        if (from === 'them' && emailBodyEl) {
            const p = document.createElement('p');
            p.className = 'email-paragraph';
            p.innerHTML = safe;
            emailBodyEl.appendChild(p);
            smoothScrollToBottom(300);
        }
        return;
    }

    const wrap = document.createElement('div');
    wrap.className = 'msg ' + from;
    const check = from === 'me' ? '<span class="msg-check">✓✓</span>' : '';
    wrap.innerHTML = `<div class="bubble ${from}">${safe}<span class="msg-time">${currentTime()}${check}</span></div>`;
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

async function renderTexts(texts, instant = false) {
    for (let i = 0; i < texts.length; i++) {
        const msg = texts[i];
        if (instant) {
            // Modo replay: aparece de uma vez, sem typing/delays
            addBubble(msg.from === 'them' ? 'them' : 'me', msg.body);
            continue;
        }
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
    const previous = previousAnswers[questionIndex];
    const isReplay = !!previous;

    await sleep(isReplay ? 100 : 400);

    const card = document.createElement('div');
    card.className = 'question-card';

    const optionsHtml = q.options.map(opt => `
        <button type="button" class="option-btn" data-key="${opt.key}">
            <span class="option-key">${opt.key.toUpperCase()}</span>
            <span>${opt.text.replace(/</g, '&lt;')}</span>
        </button>
    `).join('');

    card.innerHTML = `
        ${isReplay ? '<div class="answered-tag">✓ Já respondida — sem permissão de alterar</div>' : ''}
        <div class="question-prompt">💬 ${q.prompt.replace(/</g, '&lt;')}</div>
        <div class="options">${optionsHtml}</div>
        <div class="feedback-mascot-wrap"><img src="" alt=""></div>
        <div class="feedback-box"></div>
        <button class="continue-btn" type="button">Continuar →</button>
    `;

    chatArea.appendChild(card);
    smoothScrollToBottom(isReplay ? 400 : 1200);

    // ── REPLAY de pergunta já respondida ─────────────────────────────────
    if (isReplay) {
        const chosenKey = previous.key;
        const isCorrect = previous.is_correct;
        const chosen = (q.options || []).find(o => o.key === chosenKey);
        const feedbackText = chosen?.feedback ?? '';

        card.querySelectorAll('.option-btn').forEach(b => {
            b.disabled = true;
            if (b.dataset.key === chosenKey) {
                b.classList.add(isCorrect ? 'selected-correct' : 'selected-wrong');
            } else {
                b.classList.add('faded');
            }
        });

        const mascotWrap = card.querySelector('.feedback-mascot-wrap');
        const mascotImg = mascotWrap.querySelector('img');
        mascotImg.src = isCorrect
            ? '/images/mascots/training-show-correct.png'
            : '/images/mascots/training-show-wrong.png';
        mascotImg.alt = isCorrect ? 'Acertou!' : 'Vamos aprender';
        mascotWrap.style.display = 'flex';

        const fbox = card.querySelector('.feedback-box');
        fbox.className = 'feedback-box ' + (isCorrect ? 'correct' : 'wrong');
        fbox.textContent = feedbackText;
        fbox.style.display = 'block';

        // Não mostra "Continue" — só dá um pequeno respiro pro user ver
        // e segue pra próxima pergunta automaticamente
        await sleep(700);
        return;
    }

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

                // Marca a opção escolhida + esmaece as outras
                card.querySelectorAll('.option-btn').forEach(b => {
                    if (b.dataset.key === chosenKey) {
                        b.classList.add(data.is_correct ? 'selected-correct' : 'selected-wrong');
                    } else {
                        b.classList.add('faded');
                    }
                });

                // Mostra mascote do feedback (vitoria ou explicando)
                const mascotWrap = card.querySelector('.feedback-mascot-wrap');
                const mascotImg = mascotWrap.querySelector('img');
                mascotImg.src = data.is_correct
                    ? '/images/mascots/training-show-correct.png'
                    : '/images/mascots/training-show-wrong.png';
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
        const isPreAnswered = !!previousAnswers[i];
        document.getElementById('qCurrent').textContent = (i + 1);
        // Modo email: e-mail aparece instantâneo (sem typing/delays)
        await renderTexts(chunks[i].texts, isPreAnswered || isEmailMode);
        await renderQuestion(chunks[i].question, i, i === chunks.length - 1);
    }
    if (epilogue.length > 0) {
        await renderTexts(epilogue, isEmailMode);
    }
}

run();
</script>
</body>
</html>
