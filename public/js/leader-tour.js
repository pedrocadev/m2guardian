/* ─────────────────────────────────────────────────────────────────────────────
   M2 Guardian — Leader Tour (onboarding guiado)
   Vanilla JS, sem dependências. Persistência via localStorage.
   ───────────────────────────────────────────────────────────────────────────── */
(function () {
    'use strict';

    const STORAGE_KEY = 'm2guardian.leader-tour.completed.v1';
    const ARROW_OFFSET = 60; // deve casar com `.tour-popover::before { left }` no CSS

    const STEPS = [
        {
            target: null,
            mascot: 'training-welcome-greeting.png',
            badge: 'PASSO 1 / 7',
            title: 'Bem-vindo ao painel!',
            text: 'Em 30 segundos te mostro tudo o que dá pra fazer aqui. Pode pular a qualquer momento.',
            arrow: 'none',
            placement: 'center',
        },
        {
            target: '[data-tour="stats"]',
            mascot: 'training-welcome-explain.png',
            badge: 'PASSO 2 / 7',
            title: 'O resumo da equipe',
            text: 'Esses 4 números são o coração: <strong>quantos colaboradores</strong>, <strong>quantos concluíram</strong>, quantos ainda faltam e a <strong>taxa de conclusão</strong>.',
            arrow: 'top',
        },
        {
            target: '[data-tour="invite-btn"]',
            mascot: 'training-show-greeting.png',
            badge: 'PASSO 3 / 7',
            title: 'Convide sua equipe',
            text: 'Sua principal ação aqui: <strong>adicionar colaboradores</strong> ao treinamento. Cada um recebe um link único por e-mail.',
            arrow: 'top',
        },
        {
            target: '[data-tour="posture"]',
            mascot: 'training-show-correct.png',
            badge: 'PASSO 4 / 7',
            title: 'Postura da equipe',
            text: 'Veja em <strong>quais habilidades</strong> a equipe é forte e onde precisa evoluir. O termômetro mostra quanto falta pro próximo nível.',
            arrow: 'top',
        },
        {
            target: '[data-tour="problems"]',
            mascot: 'training-show-wrong.png',
            badge: 'PASSO 5 / 7',
            title: 'Onde a equipe mais erra',
            text: 'Os <strong>3 cenários problemáticos</strong> revelam padrões pra reforço dirigido. Use isso pra orientar conversas com o time.',
            arrow: 'top',
        },
        {
            target: '[data-tour="ver-postura"]',
            mascot: 'training-index-progress.png',
            badge: 'PASSO 6 / 7',
            title: 'Postura individual',
            text: 'Clique em <strong>"Ver postura"</strong> em qualquer colaborador concluído pra ver pontos fortes, pontos de evolução e desempenho por habilidade.',
            arrow: 'top',
        },
        {
            target: '[data-tour="export-pdf"]',
            mascot: 'training-index-done.png',
            badge: 'FIM 🏆',
            title: 'Pronto! Bom uso 🎯',
            text: 'Quer levar o relatório pra uma reunião? <strong>Exporte em PDF</strong> aqui. Você pode <strong>refazer este tour</strong> a qualquer momento no botão "Tour" do topo.',
            arrow: 'top',
        },
    ];

    function alreadyCompleted() {
        try { return localStorage.getItem(STORAGE_KEY) === 'true'; }
        catch (e) { return false; }
    }

    function markCompleted() {
        try { localStorage.setItem(STORAGE_KEY, 'true'); } catch (e) {}
    }

    let currentStep = 0;
    let spotlight, popover;
    const escHandler = (e) => { if (e.key === 'Escape') skip(); };

    function createElements() {
        spotlight = document.createElement('div');
        spotlight.className = 'tour-spotlight tour-spotlight-pulse';
        document.body.appendChild(spotlight);

        popover = document.createElement('div');
        popover.className = 'tour-popover';
        document.body.appendChild(popover);
    }

    function removeElements() {
        if (spotlight) { spotlight.remove(); spotlight = null; }
        if (popover)   { popover.remove();   popover   = null; }
        document.body.classList.remove('tour-active');
    }

    function positionSpotlight(target) {
        if (!target) {
            spotlight.style.display = 'none';
            return;
        }
        spotlight.style.display = 'block';
        const rect = target.getBoundingClientRect();
        const padding = 8;
        spotlight.style.top    = (rect.top    + window.scrollY - padding) + 'px';
        spotlight.style.left   = (rect.left   + window.scrollX - padding) + 'px';
        spotlight.style.width  = (rect.width  + padding * 2) + 'px';
        spotlight.style.height = (rect.height + padding * 2) + 'px';
    }

    function positionPopover(target, placement) {
        const popW = popover.offsetWidth;
        const popH = popover.offsetHeight;
        const margin = 20;

        if (placement === 'center' || !target) {
            popover.style.top  = (window.scrollY + (window.innerHeight - popH) / 2) + 'px';
            popover.style.left = ((window.innerWidth - popW) / 2) + 'px';
            return;
        }

        const rect = target.getBoundingClientRect();
        const targetTop    = rect.top + window.scrollY;
        const targetBottom = rect.bottom + window.scrollY;
        const targetCenterX = rect.left + rect.width / 2;

        const spaceBelow = window.innerHeight - rect.bottom;
        const placeBelow = spaceBelow > popH + margin || rect.top < popH + margin;

        let top, left;
        if (placeBelow) {
            top = targetBottom + margin;
            popover.setAttribute('data-arrow', 'top');
        } else {
            top = targetTop - popH - margin;
            popover.setAttribute('data-arrow', 'bottom');
        }
        left = targetCenterX - ARROW_OFFSET;
        left = Math.max(12, Math.min(left, window.innerWidth - popW - 12));

        popover.style.top  = top + 'px';
        popover.style.left = left + 'px';
    }

    function scrollToTarget(target) {
        if (!target) return;
        const rect = target.getBoundingClientRect();
        const inView = rect.top >= 0 && rect.bottom <= window.innerHeight;
        if (!inView) {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function renderStep(index) {
        const step = STEPS[index];
        if (!step) return;

        const target = step.target ? document.querySelector(step.target) : null;

        if (step.target && !target) {
            // Elemento não existe (talvez Pro-only escondido) — pula
            if (index < STEPS.length - 1) { next(); return; }
            else                          { end();  return; }
        }

        scrollToTarget(target);
        setTimeout(() => {
            positionSpotlight(target);

            const isFirst = index === 0;
            const isLast  = index === STEPS.length - 1;

            popover.innerHTML = `
                <div class="tour-popover-top">
                    <img src="/images/mascots/${step.mascot}" alt="" class="tour-mascot">
                    <div>
                        <span class="tour-badge">${step.badge}</span>
                        <div class="tour-title">${step.title}</div>
                    </div>
                </div>
                <div class="tour-popover-body">${step.text}</div>
                <div class="tour-popover-footer">
                    <div class="tour-progress">
                        ${STEPS.map((_, i) => `<div class="tour-dot ${i === index ? 'active' : ''}"></div>`).join('')}
                    </div>
                    <div class="tour-actions">
                        ${!isLast ? '<button type="button" class="tour-btn tour-btn-skip" data-action="skip">Pular</button>' : ''}
                        ${!isFirst ? '<button type="button" class="tour-btn tour-btn-secondary" data-action="prev">← Voltar</button>' : ''}
                        <button type="button" class="tour-btn tour-btn-primary" data-action="${isLast ? 'finish' : 'next'}">
                            ${isLast ? 'Concluir' : 'Próximo →'}
                        </button>
                    </div>
                </div>
            `;
            popover.setAttribute('data-arrow', step.arrow || 'top');

            requestAnimationFrame(() => positionPopover(target, step.placement));
        }, target ? 350 : 0);
    }

    function next() {
        if (currentStep < STEPS.length - 1) {
            currentStep++;
            renderStep(currentStep);
        }
    }

    function prev() {
        if (currentStep > 0) {
            currentStep--;
            renderStep(currentStep);
        }
    }

    function skip() {
        if (confirm('Deseja realmente pular o tour? Você pode refazê-lo a qualquer momento clicando em "Tour" no topo.')) {
            end();
        }
    }

    function end() {
        markCompleted();
        removeElements();
        document.removeEventListener('keydown', escHandler);
    }

    function start() {
        if (document.querySelector('.tour-popover')) return; // já rodando
        currentStep = 0;
        document.body.classList.add('tour-active');
        createElements();
        document.addEventListener('keydown', escHandler);

        popover.addEventListener('click', (e) => {
            const action = e.target.getAttribute('data-action');
            if (action === 'next')   next();
            else if (action === 'prev')   prev();
            else if (action === 'skip')   skip();
            else if (action === 'finish') end();
        });

        renderStep(0);
    }

    window.addEventListener('resize', () => {
        if (!popover) return;
        const step = STEPS[currentStep];
        if (!step) return;
        const target = step.target ? document.querySelector(step.target) : null;
        positionSpotlight(target);
        positionPopover(target, step.placement);
    });

    // Botão "Refazer tour"
    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-tour-replay]')) {
            e.preventDefault();
            start();
        }
    });

    // Auto-start na primeira visita
    if (!alreadyCompleted()) {
        // Pequeno delay pra dashboard renderizar completamente
        setTimeout(start, 600);
    }

    // Expõe global pra debug/teste manual
    window.LeaderTour = { start, end, reset: () => {
        try { localStorage.removeItem(STORAGE_KEY); } catch (e) {}
        console.log('Tour resetado. Próximo reload mostra de novo.');
    }};
})();
