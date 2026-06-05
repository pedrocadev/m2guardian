@php
    $admin = auth('admin')->user();
    if (!$admin) return;

    // Só renderiza na home do dashboard (não em todas as páginas admin)
    if (!request()->routeIs('filament.admin.pages.dashboard')) return;

    // Já mostrado nesta sessão? Não repete (reload, navegação não disparam de novo)
    if (session('release_popup_shown')) return;

    $release = \App\Models\Release::latestPublished();
    if (!$release) return;

    // Side-effect intencional: marca a sessão como "já viu o popup" no momento do render.
    // Isso garante que reload/navegação não dispare o popup de novo dentro da mesma sessão.
    // Logout invalida a sessão → próximo login a flag estará ausente → popup aparece de novo.
    session(['release_popup_shown' => true]);

    // Substitui placeholders no conteúdo
    $firstName = explode(' ', trim($admin->name ?? ''))[0] ?: 'admin';
    $content = strtr($release->content, [
        '{nome}'          => $firstName,
        '{nome_completo}' => $admin->name ?? '',
        '{email}'         => $admin->email ?? '',
    ]);
@endphp

<div id="release-popup-overlay" class="release-popup-overlay" role="dialog" aria-modal="true">
    <div class="release-popup-card">
        <div class="release-popup-header">
            <div class="release-popup-badge">🎉 NOVIDADE</div>
            <button type="button" class="release-popup-close" onclick="document.getElementById('release-popup-overlay').remove()" aria-label="Fechar">
                ✕
            </button>
        </div>

        <div class="release-popup-body">
            <div class="release-popup-meta">
                <span class="release-popup-date">{{ $release->released_at->format('d/m/Y') }}</span>
                <span class="release-popup-sep">·</span>
                <span class="release-popup-brand">Guardião Digital</span>
            </div>

            <h2 class="release-popup-title">{{ $release->title }}</h2>

            <div class="release-popup-content">
                {!! \Illuminate\Support\Str::markdown($content) !!}
            </div>
        </div>

        <div class="release-popup-footer">
            <button type="button" class="release-popup-btn release-popup-btn-secondary" onclick="document.getElementById('release-popup-overlay').remove()">
                OK, entendi
            </button>
            <a href="{{ route('filament.admin.resources.releases.index') }}" class="release-popup-btn" onclick="document.getElementById('release-popup-overlay').remove()">
                Ver atualizações →
            </a>
        </div>
    </div>
</div>
