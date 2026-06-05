{{--
    Termômetro gameficado de nível/postura.

    Variáveis esperadas:
    - $thermometer: array do ScoreService::buildThermometer()
        - segments: array de 5 ['key', 'short', 'name', 'color', 'min', 'max']
        - percentage: int (0-100)
        - current_name: string
        - current_color: string (hex)
        - next_name: string|null
        - next_min: int|null
        - gap: int|null
        - is_top: bool
    - $variant: 'individual' ou 'corporate' (apenas afeta as legendas; default = 'individual')
--}}

@php
    $variant = $variant ?? 'individual';
    $t = $thermometer;
    $pct = max(0, min(100, $t['percentage']));
@endphp

@once
<style>
    .m2-thermometer {
        margin: 14px 0;
        font-family: ui-sans-serif, system-ui, sans-serif;
    }
    .m2-thermo-track {
        position: relative;
        display: flex;
        width: 100%;
        height: 28px;
        border-radius: 999px;
        overflow: visible;
        background: #eee;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.08);
        margin-top: 40px;
    }
    .m2-thermo-segment {
        position: relative;
        height: 100%;
        opacity: 0.45;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.2s;
    }
    .m2-thermo-segment:first-child { border-top-left-radius: 999px; border-bottom-left-radius: 999px; }
    .m2-thermo-segment:last-child  { border-top-right-radius: 999px; border-bottom-right-radius: 999px; }
    .m2-thermo-segment:hover { opacity: 0.75; }
    .m2-thermo-segment-label {
        font-size: 9.5px;
        color: #fff;
        font-weight: 800;
        letter-spacing: 1px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.35);
        pointer-events: none;
    }

    .m2-thermo-marker {
        position: absolute;
        top: -36px;
        transform: translateX(-50%);
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        animation: m2-thermo-bounce 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .m2-thermo-marker-flag {
        color: #fff;
        font-weight: 800;
        font-size: 13px;
        padding: 5px 11px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.18);
        white-space: nowrap;
        position: relative;
        background: var(--flag-bg, #111);
    }
    .m2-thermo-marker-flag::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 0; height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid var(--flag-bg, #111);
    }
    .m2-thermo-marker-needle {
        width: 3px;
        height: 38px;
        margin-top: 2px;
        border-radius: 2px;
        box-shadow: 0 0 0 2px #fff, 0 1px 3px rgba(0,0,0,0.2);
    }
    @keyframes m2-thermo-bounce {
        0%   { opacity: 0; transform: translateX(-50%) translateY(-12px); }
        60%  { opacity: 1; transform: translateX(-50%) translateY(3px); }
        100% { transform: translateX(-50%) translateY(0); }
    }

    .m2-thermo-message {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        padding: 12px 16px;
        background: #f9fafb;
        border-radius: 8px;
        font-size: 13.5px;
        color: #333;
        border: 1px solid #f0f0f0;
    }
    .m2-thermo-message strong { color: #111; font-weight: 700; }
    .m2-thermo-rocket, .m2-thermo-trophy {
        font-size: 22px;
        line-height: 1;
        flex-shrink: 0;
    }

    @media (max-width: 600px) {
        .m2-thermo-segment-label { display: none; }
        .m2-thermo-marker-flag { font-size: 11px; padding: 4px 8px; }
        .m2-thermo-message { font-size: 12.5px; padding: 10px 12px; }
    }
</style>
@endonce

<div class="m2-thermometer">
    {{-- Barra de fundo segmentada --}}
    <div class="m2-thermo-track"
         role="progressbar"
         aria-valuenow="{{ $pct }}"
         aria-valuemin="0"
         aria-valuemax="100"
         aria-label="{{ $variant === 'corporate' ? 'Postura corporativa' : 'Nível de Guardião' }}: {{ $t['current_name'] }} ({{ $pct }}%)">
        @foreach($t['segments'] as $seg)
            @php $rangeWidth = ($seg['max'] - $seg['min'] + 1); @endphp
            <div class="m2-thermo-segment"
                 style="flex: {{ $rangeWidth }}; background: {{ $seg['color'] }};"
                 title="{{ $seg['name'] }} ({{ $seg['min'] }}-{{ $seg['max'] }}%)"
                 aria-hidden="true">
                <span class="m2-thermo-segment-label">{{ $seg['short'] }}</span>
            </div>
        @endforeach

        {{-- Marcador da posição atual --}}
        <div class="m2-thermo-marker" style="left: {{ $pct }}%;" aria-hidden="true">
            <div class="m2-thermo-marker-flag" style="--flag-bg: {{ $t['current_color'] }};">
                <span class="m2-thermo-marker-pct">{{ $pct }}%</span>
            </div>
            <div class="m2-thermo-marker-needle" style="background: {{ $t['current_color'] }};"></div>
        </div>
    </div>

    {{-- Mensagem motivacional --}}
    <div class="m2-thermo-message">
        @if($t['is_top'])
            <span class="m2-thermo-trophy">🏆</span>
            <span>
                <strong>{{ $t['current_name'] }}!</strong>
                {{ $variant === 'corporate' ? 'Postura máxima alcançada — a equipe é referência.' : 'Você atingiu o topo!' }}
            </span>
        @else
            <span class="m2-thermo-rocket">🚀</span>
            <span>
                @if($t['gap'] <= 0)
                    Pronto pra subir — chegou em <strong>{{ $t['next_name'] }}</strong>.
                @else
                    Faltam
                    <strong>{{ $t['gap'] }}</strong>
                    {{ $t['gap'] === 1 ? 'ponto' : 'pontos' }}
                    pra
                    <strong>{{ $variant === 'corporate' ? 'Postura ' . $t['next_name'] : $t['next_name'] }}</strong>
                @endif
            </span>
        @endif
    </div>
</div>
