@props([
    'title' => '',
    'titleHighlight' => '',
    'lead' => '',
    'features' => [],
    'mascot' => 'login-admin.png',
    'formTitle' => '',
    'formSubtitle' => '',
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
        padding: 56px 64px;
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
    .m2-brand-text { line-height: 1.15; }
    .m2-brand-name { font-size: 16px; font-weight: 800; letter-spacing: 0.3px; color: #fff; }
    .m2-brand-sub { font-size: 11px; color: #888; letter-spacing: 0.4px; margin-top: 2px; }

    .m2-hero-content {
        display: flex; flex-direction: column; gap: 28px;
        max-width: 440px;
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
        font-size: 52px; font-weight: 800; line-height: 1.08;
        letter-spacing: -1.2px; color: #fff;
    }
    .m2-hero-title em {
        font-style: normal;
        background: linear-gradient(90deg, #ff4444, #ff7878);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .m2-hero-lead {
        font-size: 17px; color: #c4c4c4;
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

    .m2-hero-mascot {
        position: absolute;
        right: 30px;
        bottom: 140px;
        width: 230px;
        opacity: 0.92;
        pointer-events: none;
        filter: drop-shadow(0 20px 40px rgba(0,0,0,0.5));
        z-index: 1;
    }
    @media (max-width: 1440px) {
        .m2-hero-mascot { width: 190px; right: 24px; bottom: 160px; }
    }
    @media (max-width: 1180px) {
        .m2-hero-mascot { display: none; }
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
        padding: 40px 24px;
        background: #fafafa;
    }
    @media (min-width: 1024px) {
        .m2-form-side { padding: 40px 64px; background: #ffffff; }
    }
    .m2-form-card { width: 100%; max-width: 420px; }

    .m2-brand-mobile {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 32px;
    }
    @media (min-width: 1024px) { .m2-brand-mobile { display: none; } }
    .m2-brand-mobile .m2-brand-name { color: #111; }
    .m2-brand-mobile .m2-brand-sub { color: #888; }

    .m2-form-title {
        font-size: 24px; font-weight: 800; color: #111;
        margin-bottom: 8px; letter-spacing: -0.3px;
    }
    .m2-form-subtitle {
        color: #666; font-size: 14px; line-height: 1.55;
        margin-bottom: 28px;
    }
</style>

<div class="m2-auth-layout">

    {{-- ===== Hero (esquerda) ===== --}}
    <aside class="m2-hero">
        <div class="m2-brand">
            <div class="m2-brand-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5l-8-3z"/>
                </svg>
            </div>
            <div class="m2-brand-text">
                <div class="m2-brand-name">GUARDIÃO DIGITAL</div>
                <div class="m2-brand-sub">Continuous Human Risk Management</div>
            </div>
        </div>

        <div class="m2-hero-content">
            <div class="m2-hero-pill">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Security Awareness Platform
            </div>

            <h1 class="m2-hero-title">
                {{ $title }}<br>
                <em>{{ $titleHighlight }}</em>
            </h1>

            <p class="m2-hero-lead">{{ $lead }}</p>

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
        </div>

        <img src="{{ asset('images/mascots/' . $mascot) }}" alt="Mascote Guardião" class="m2-hero-mascot">

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

        <div class="m2-legal">© {{ date('Y') }} M2 Cloud &amp; Security · Guardião Digital</div>
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
