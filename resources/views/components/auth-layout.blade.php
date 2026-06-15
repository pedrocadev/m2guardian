@props([
    'title' => '',
    'titleHighlight' => '',
    'lead' => '',
    'features' => [],
    'mascot' => 'login-admin.png',
    'heroBackground' => null,
    'heroBackgroundPosition' => 'center',
    'brandLogo' => null,
    'showStats' => true,
    'showLegal' => true,
    'formTitle' => '',
    'formSubtitle' => '',
    'formMaxWidth' => '420px',
])

<style>
    /* ===== Layout split-screen compartilhado entre login admin e líder ===== */
    .m2-auth-layout {
        display: grid;
        grid-template-columns: 1fr;
        min-height: 100vh;
        font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
    }
    @media (min-width: 1024px) {
        .m2-auth-layout { grid-template-columns: 1.05fr 1fr; }
    }

    /* ----- Lado esquerdo (hero) ----- */
    .m2-hero {
        position: relative;
        display: none;
        flex-direction: column;
        justify-content: space-between;
        padding: clamp(28px, 4vw, 56px) clamp(28px, 4.5vw, 64px);
        gap: clamp(20px, 2.5vw, 36px);
        color: #f5f5f5;
        background:
            radial-gradient(1200px 600px at 20% 0%, rgba(204, 0, 0, 0.22) 0%, transparent 60%),
            radial-gradient(900px 500px at 80% 100%, rgba(204, 0, 0, 0.10) 0%, transparent 60%),
            linear-gradient(180deg, #0a0a0a 0%, #161616 100%);
        overflow: hidden;
    }
    @media (min-width: 1024px) { .m2-hero { display: flex; } }
    .m2-hero::before {
        content: '';
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
        background-size: 48px 48px;
        pointer-events: none;
        -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
        mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
    }
    .m2-hero--has-bg::before { display: none; }
    .m2-hero > * { position: relative; z-index: 1; }

    .m2-brand { display: flex; align-items: center; gap: 14px; }
    .m2-brand-icon {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, #CC0000 0%, #8a0000 100%);
        border-radius: 10px;
        display: grid; place-items: center;
        box-shadow: 0 4px 20px rgba(204, 0, 0, 0.35);
        flex-shrink: 0;
    }
    .m2-brand-icon svg { width: 24px; height: 24px; fill: #fff; }
    .m2-brand-logo {
        height: clamp(70px, 8vw, 110px);
        width: auto;
        flex-shrink: 0;
        filter: drop-shadow(0 6px 22px rgba(204, 0, 0, 0.45));
    }
    .m2-brand-text { line-height: 1.15; }
    .m2-brand-name { font-size: 16px; font-weight: 800; letter-spacing: 0.3px; color: #fff; }
    .m2-brand-sub { font-size: 11px; color: #888; letter-spacing: 0.4px; margin-top: 2px; }

    .m2-hero-content {
        display: flex; flex-direction: column;
        gap: clamp(16px, 2vw, 28px);
        max-width: min(440px, 100%);
        position: relative;
        z-index: 2;
    }
    .m2-hero-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 16px;
        border: 1px solid rgba(204, 0, 0, 0.5);
        border-radius: 999px;
        font-size: 13px; font-weight: 600;
        color: #ff5e5e;
        background: rgba(204, 0, 0, 0.08);
        width: fit-content;
    }
    .m2-hero-pill svg { width: 15px; height: 15px; }

    .m2-hero-title {
        font-size: clamp(30px, 4vw, 52px);
        font-weight: 800; line-height: 1.08;
        letter-spacing: -1.2px; color: #fff;
    }
    .m2-hero-title em {
        font-style: normal;
        background: linear-gradient(90deg, #ff4444, #ff7878);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .m2-hero-lead {
        font-size: clamp(13px, 1.1vw, 17px);
        color: #c4c4c4;
        line-height: 1.6; max-width: 480px;
    }
    .m2-hero-features { list-style: none; display: flex; flex-direction: column; gap: 14px; margin-top: 8px; padding: 0; }
    .m2-hero-features li {
        display: flex; align-items: center; gap: 14px;
        font-size: 16px; color: #dcdcdc;
    }
    .m2-check {
        width: 24px; height: 24px; flex-shrink: 0;
        background: rgba(204, 0, 0, 0.15);
        border: 1px solid rgba(204, 0, 0, 0.4);
        border-radius: 6px;
        display: grid; place-items: center;
    }
    .m2-check svg { width: 13px; height: 13px; stroke: #ff5e5e; fill: none; stroke-width: 3; }

    .m2-hero-mascot-wrap {
        position: absolute;
        right: clamp(10px, 2vw, 40px);
        bottom: clamp(40px, 6vw, 100px);
        pointer-events: none;
        z-index: 1;
    }
    .m2-hero-mascot {
        display: block;
        width: clamp(240px, 26vw, 440px);
        height: auto;
        opacity: 0.95;
        filter: drop-shadow(0 24px 50px rgba(0,0,0,0.55));
        position: relative;
        z-index: 2;
        animation: m2MascotFloat 4s ease-in-out infinite;
    }
    .m2-hero-mascot-wrap::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -6px;
        transform: translateX(-50%);
        width: 78%;
        height: 30px;
        background: radial-gradient(ellipse at center,
            rgba(204, 0, 0, 0.55) 0%,
            rgba(204, 0, 0, 0.25) 35%,
            rgba(204, 0, 0, 0.08) 60%,
            transparent 75%);
        filter: blur(10px);
        border-radius: 50%;
        z-index: 1;
        animation: m2MascotShadow 4s ease-in-out infinite;
    }
    @keyframes m2MascotFloat {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-14px); }
    }
    @keyframes m2MascotShadow {
        0%, 100% { transform: translateX(-50%) scaleX(1);    opacity: 0.95; }
        50%      { transform: translateX(-50%) scaleX(0.86); opacity: 0.7; }
    }
    @media (prefers-reduced-motion: reduce) {
        .m2-hero-mascot, .m2-hero-mascot-wrap::after { animation: none; }
    }
    @media (max-width: 1080px) {
        .m2-hero-mascot-wrap { display: none; }
    }

    .m2-hero-stats {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px;
        padding-top: 28px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }
    .m2-stat-num { font-size: 32px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
    .m2-stat-label { font-size: 12px; color: #999; line-height: 1.45; margin-top: 6px; max-width: 140px; }

    .m2-legal {
        display: none;
        position: absolute;
        bottom: 24px; left: 64px; right: 64px;
        font-size: 11px; color: #666;
    }
    @media (min-width: 1024px) { .m2-legal { display: block; } }

    /* ----- Lado direito (form wrapper) ----- */
    .m2-form-side {
        display: flex; align-items: center; justify-content: center;
        padding: clamp(24px, 4vw, 40px) clamp(16px, 3vw, 24px);
        background: #fafafa;
    }
    @media (min-width: 1024px) {
        .m2-form-side { padding: clamp(28px, 3.5vw, 40px) clamp(28px, 4.5vw, 64px); background: #ffffff; }
    }
    .m2-form-card { width: 100%; max-width: {{ $formMaxWidth }}; }

    .m2-brand-mobile {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 32px;
    }
    @media (min-width: 1024px) { .m2-brand-mobile { display: none; } }
    .m2-brand-mobile .m2-brand-name { color: #111; }
    .m2-brand-mobile .m2-brand-sub { color: #888; }

    .m2-form-title {
        font-size: clamp(20px, 1.8vw, 24px);
        font-weight: 800; color: #111;
        margin-bottom: 8px; letter-spacing: -0.3px;
    }
    .m2-form-subtitle {
        color: #666; font-size: clamp(13px, 1vw, 14px); line-height: 1.55;
        margin-bottom: 28px;
    }

    /* ===== Features em cards (alternativo ao checkmark) ===== */
    .m2-hero-features-cards {
        list-style: none;
        display: flex; flex-direction: column;
        gap: clamp(12px, 1.5vw, 18px);
        margin-top: 4px;
        padding: 0;
    }
    .m2-hero-features-cards li {
        display: flex;
        align-items: center;
        gap: clamp(12px, 1.4vw, 16px);
    }
    .m2-feature-card-icon {
        width: clamp(38px, 3.5vw, 46px);
        height: clamp(38px, 3.5vw, 46px);
        flex-shrink: 0;
        background: rgba(204, 0, 0, 0.12);
        border: 1px solid rgba(204, 0, 0, 0.45);
        border-radius: 10px;
        display: grid; place-items: center;
        color: #ff5e5e;
    }
    .m2-feature-card-icon svg {
        width: clamp(18px, 1.7vw, 22px);
        height: clamp(18px, 1.7vw, 22px);
        stroke: currentColor; fill: none;
        stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    }
    .m2-feature-card-content { display: flex; flex-direction: column; gap: 2px; }
    .m2-feature-card-title { font-size: clamp(13px, 1.15vw, 16px); font-weight: 700; color: #fff; line-height: 1.3; }
    .m2-feature-card-sub { font-size: clamp(12px, 1vw, 14px); color: #bbb; line-height: 1.4; }

    /* ===== Form header com ícone vermelho ao lado do título ===== */
    .m2-form-header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 28px;
    }
    .m2-form-header-icon {
        width: 46px; height: 46px;
        flex-shrink: 0;
        background: rgba(204, 0, 0, 0.10);
        border-radius: 10px;
        display: grid; place-items: center;
        color: #CC0000;
    }
    .m2-form-header-icon svg {
        width: 24px; height: 24px;
        stroke: currentColor; fill: none;
        stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    }
    .m2-form-header-text { flex: 1; }
    .m2-form-header-text .m2-form-title { margin-bottom: 6px; }
    .m2-form-header-text .m2-form-subtitle { margin-bottom: 0; }

    /* ===== Input com ícone interno ===== */
    .m2-input-wrapper { position: relative; }
    .m2-input-wrapper .m2-input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px; height: 18px;
        stroke: #888; fill: none;
        stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        pointer-events: none;
    }
    .m2-input-wrapper input { padding-left: 46px !important; }

    /* ===== Help card (rodapé do form) ===== */
    .m2-form-help-card {
        margin-top: 28px;
        padding: 16px 18px;
        background: #f3f3f3;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .m2-form-help-icon {
        width: 36px; height: 36px;
        flex-shrink: 0;
        background: rgba(204, 0, 0, 0.10);
        border-radius: 50%;
        display: grid; place-items: center;
        color: #CC0000;
    }
    .m2-form-help-icon svg {
        width: 18px; height: 18px;
        stroke: currentColor; fill: none;
        stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    }
    .m2-form-help-text { font-size: 13px; color: #555; line-height: 1.55; }
</style>

<div class="m2-auth-layout">

    {{-- ===== Hero (esquerda) ===== --}}
    <aside class="m2-hero @if($heroBackground) m2-hero--has-bg @endif" @if($heroBackground) style="background: url('{{ asset('images/backgrounds/' . $heroBackground) }}') {{ $heroBackgroundPosition }}/cover no-repeat, #0a0a0a;" @endif>
        <div class="m2-brand">
            @if($brandLogo)
                <img src="{{ asset('images/' . $brandLogo) }}" alt="Guardião Digital" class="m2-brand-logo">
            @else
                <div class="m2-brand-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5l-8-3z"/>
                    </svg>
                </div>
                <div class="m2-brand-text">
                    <div class="m2-brand-name">GUARDIÃO DIGITAL</div>
                    <div class="m2-brand-sub">Continuous Human Risk Management</div>
                </div>
            @endif
        </div>

        <div class="m2-hero-content">
            @isset($pill)
                {{ $pill }}
            @else
                <div class="m2-hero-pill">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    Security Awareness Platform
                </div>
            @endisset

            @isset($heroTitle)
                {{ $heroTitle }}
            @else
                <h1 class="m2-hero-title">
                    {{ $title }}<br>
                    <em>{{ $titleHighlight }}</em>
                </h1>
            @endisset

            <p class="m2-hero-lead">{{ $lead }}</p>

            @isset($heroFeatures)
                {{ $heroFeatures }}
            @else
                <ul class="m2-hero-features">
                    @foreach ($features as $feature)
                        <li>
                            <span class="m2-check">
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </span>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            @endisset
        </div>

        <div class="m2-hero-mascot-wrap">
            <img src="{{ asset('images/mascots/' . $mascot) }}" alt="Mascote Guardião" class="m2-hero-mascot">
        </div>

        @if($showStats)
            <div class="m2-hero-stats">
                <div>
                    <div class="m2-stat-num">91%</div>
                    <div class="m2-stat-label">dos ataques começam por e-mail</div>
                </div>
                <div>
                    <div class="m2-stat-num">74%</div>
                    <div class="m2-stat-label">das brechas envolvem fator humano</div>
                </div>
                <div>
                    <div class="m2-stat-num">-60%</div>
                    <div class="m2-stat-label">cliques após treinamento contínuo</div>
                </div>
            </div>
        @endif

        @if($showLegal)
            <div class="m2-legal">© {{ date('Y') }} M2 Cloud &amp; Security · Guardião Digital</div>
        @endif
    </aside>

    {{-- ===== Form (direita) ===== --}}
    <main class="m2-form-side">
        <div class="m2-form-card">
            <div class="m2-brand-mobile">
                <div class="m2-brand-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5l-8-3z"/>
                    </svg>
                </div>
                <div class="m2-brand-text">
                    <div class="m2-brand-name">GUARDIÃO DIGITAL</div>
                    <div class="m2-brand-sub">by M2 Cloud &amp; Security</div>
                </div>
            </div>

            @if ($formTitle)
                <h2 class="m2-form-title">{{ $formTitle }}</h2>
            @endif
            @if ($formSubtitle)
                <p class="m2-form-subtitle">{{ $formSubtitle }}</p>
            @endif

            {{ $slot }}
        </div>
    </main>
</div>
