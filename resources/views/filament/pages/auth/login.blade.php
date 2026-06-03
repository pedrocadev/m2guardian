<x-filament-panels::page.simple>
    <style>
        /* Reset do container central do Filament para fazer split-screen full-bleed */
        body.fi-simple-layout { background: #ffffff; }
        .fi-simple-main-ctn,
        .fi-simple-main {
            max-width: none !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: 100vh !important;
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }
        .fi-simple-header,
        .fi-simple-footer { display: none !important; }

        /* Override de cor primária dos botões do Filament para o vermelho M2 */
        .m2-form-side .fi-btn-color-primary {
            --c-50: 254 242 242;
            --c-400: 204 0 0;
            --c-500: 204 0 0;
            --c-600: 163 0 0;
        }
    </style>

    <x-auth-layout
        title="O elo humano,"
        title-highlight="protegido."
        lead="Plataforma de simulação de phishing e relatórios de conformidade. Meça e eleve a maturidade de segurança das equipes que você gerencia."
        :features="[
            'Campanhas realistas em E-mail, Teams e WhatsApp',
            'Métricas e ranking por colaborador',
            'Evidências para LGPD e ISO 27001',
        ]"
        mascot="login-admin.png"
        form-title="Entrar — Equipe M2"
        form-subtitle="Acesse o painel administrativo do Guardião Digital com suas credenciais."
    >
        <x-filament-panels::form wire:submit="authenticate">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
    </x-auth-layout>
</x-filament-panels::page.simple>
