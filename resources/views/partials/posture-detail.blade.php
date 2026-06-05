{{--
    Detalhe completo de postura individual de um colaborador.
    Reutilizado pelo modal admin e pela view drill-down do líder.

    Variáveis esperadas:
    - $scoreData: array do ScoreService::forCollaborator()
--}}

@once
<style>
    .m2-pd-level-card {
        color: #fff;
        border-radius: 12px;
        padding: 22px 26px;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 20px;
        align-items: center;
        margin-bottom: 18px;
        background: linear-gradient(135deg, var(--lvl-color, #111), var(--lvl-color-dark, #333));
        box-shadow: 0 6px 18px var(--lvl-shadow, rgba(0,0,0,0.18));
    }
    .m2-pd-icon { font-size: 44px; line-height: 1; filter: drop-shadow(0 3px 8px rgba(0,0,0,0.25)); }
    .m2-pd-tag { font-size: 10.5px; letter-spacing: 2.5px; font-weight: 800; opacity: 0.88; margin-bottom: 3px; }
    .m2-pd-name { font-size: 19px; font-weight: 900; }
    .m2-pd-pct { font-size: 34px; font-weight: 900; line-height: 1; text-align: right; }
    .m2-pd-pct-sub { font-size: 11px; opacity: 0.85; margin-top: 3px; }

    .m2-pd-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px; }
    .m2-pd-panel {
        background: #f9fafb;
        border-radius: 10px;
        padding: 16px 18px;
    }
    .m2-pd-panel.strong { border-left: 4px solid #16a34a; }
    .m2-pd-panel.evolution { border-left: 4px solid #ea580c; }
    .m2-pd-panel-title {
        font-size: 11.5px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.8px;
        margin-bottom: 12px;
        display: flex; align-items: center; gap: 6px;
    }
    .m2-pd-panel.strong .m2-pd-panel-title { color: #15803d; }
    .m2-pd-panel.evolution .m2-pd-panel-title { color: #9a3412; }
    .m2-pd-panel ul { list-style: none; padding: 0; margin: 0; }
    .m2-pd-panel li {
        display: flex; justify-content: space-between; align-items: center;
        gap: 10px;
        padding: 8px 0;
        font-size: 13px;
        color: #333;
        border-bottom: 1px solid #eee;
    }
    .m2-pd-panel li:last-child { border-bottom: none; }
    .m2-pd-pill {
        font-weight: 700; font-size: 12px;
        padding: 3px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .m2-pd-panel.strong .m2-pd-pill { background: rgba(22, 163, 74, 0.12); color: #15803d; }
    .m2-pd-panel.evolution .m2-pd-pill { background: rgba(234, 88, 12, 0.12); color: #9a3412; }
    .m2-pd-empty { text-align: center; color: #999; padding: 14px 8px; font-size: 12.5px; }

    .m2-pd-all {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 18px 20px;
    }
    .m2-pd-all-title {
        font-size: 12px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.8px;
        color: #555;
        margin-bottom: 14px;
    }
    .m2-pd-cat-row {
        display: grid; grid-template-columns: 1fr 220px; gap: 14px;
        align-items: center; padding: 7px 0;
    }
    .m2-pd-cat-name { font-size: 13px; color: #333; font-weight: 500; }
    .m2-pd-cat-name small { color: #999; font-size: 11px; }
    .m2-pd-bar { display: flex; align-items: center; gap: 10px; }
    .m2-pd-bar-track { flex: 1; height: 8px; background: #eee; border-radius: 4px; overflow: hidden; }
    .m2-pd-bar-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }
    .m2-pd-bar-fill.green { background: #16a34a; }
    .m2-pd-bar-fill.yellow { background: #ea580c; }
    .m2-pd-bar-fill.red { background: #dc2626; }
    .m2-pd-bar-pct { font-size: 12px; font-weight: 700; color: #333; min-width: 40px; text-align: right; }

    @media (max-width: 600px) {
        .m2-pd-cols { grid-template-columns: 1fr; }
        .m2-pd-cat-row { grid-template-columns: 1fr; gap: 4px; }
        .m2-pd-level-card { grid-template-columns: auto 1fr; }
        .m2-pd-pct { text-align: left; grid-column: 1 / -1; font-size: 28px; }
    }
</style>
@endonce

@php
    $levelStyles = [
        'n1' => ['color' => '#dc2626', 'colorDark' => '#991b1b', 'shadow' => 'rgba(220, 38, 38, 0.35)', 'icon' => '⚠️'],
        'n2' => ['color' => '#ea580c', 'colorDark' => '#9a3412', 'shadow' => 'rgba(234, 88, 12, 0.35)', 'icon' => '🌱'],
        'n3' => ['color' => '#ca8a04', 'colorDark' => '#854d0e', 'shadow' => 'rgba(202, 138, 4, 0.35)', 'icon' => '🛡️'],
        'n4' => ['color' => '#16a34a', 'colorDark' => '#15803d', 'shadow' => 'rgba(22, 163, 74, 0.35)', 'icon' => '⚔️'],
        'n5' => ['color' => '#CC0000', 'colorDark' => '#7f0000', 'shadow' => 'rgba(204, 0, 0, 0.45)', 'icon' => '🏆'],
    ];
    $L = $levelStyles[$scoreData['level_key']] ?? $levelStyles['n1'];
@endphp

<div class="m2-pd-level-card" style="--lvl-color: {{ $L['color'] }}; --lvl-color-dark: {{ $L['colorDark'] }}; --lvl-shadow: {{ $L['shadow'] }};">
    <div class="m2-pd-icon">{{ $L['icon'] }}</div>
    <div>
        <div class="m2-pd-tag">{{ $scoreData['level_tag'] }}</div>
        <div class="m2-pd-name">{{ $scoreData['level_name'] }}</div>
    </div>
    <div>
        <div class="m2-pd-pct">{{ $scoreData['percentage'] }}%</div>
        <div class="m2-pd-pct-sub">{{ $scoreData['score'] }}/{{ $scoreData['total'] }} acertos</div>
    </div>
</div>

@include('partials.thermometer', ['thermometer' => $scoreData['thermometer'], 'variant' => 'individual'])

<div class="m2-pd-cols">
    <div class="m2-pd-panel strong">
        <div class="m2-pd-panel-title">✅ Pontos Fortes</div>
        @if(empty($scoreData['strong_points']))
            <div class="m2-pd-empty">Nenhuma categoria ≥ 80%.</div>
        @else
            <ul>
                @foreach($scoreData['strong_points'] as $point)
                    <li>
                        <span>{{ $point['label'] }}</span>
                        <span class="m2-pd-pill">{{ $point['percentage'] }}%</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="m2-pd-panel evolution">
        <div class="m2-pd-panel-title">📈 Pontos de Evolução</div>
        @if(empty($scoreData['evolution_points']))
            <div class="m2-pd-empty">Nenhuma categoria abaixo de 60%.</div>
        @else
            <ul>
                @foreach($scoreData['evolution_points'] as $point)
                    <li>
                        <span>{{ $point['label'] }}</span>
                        <span class="m2-pd-pill">{{ $point['percentage'] }}%</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<div class="m2-pd-all">
    <div class="m2-pd-all-title">Desempenho por categoria comportamental</div>
    @if(empty($scoreData['by_category']))
        <div class="m2-pd-empty">Sem dados disponíveis.</div>
    @else
        @foreach($scoreData['by_category'] as $cat)
            <div class="m2-pd-cat-row">
                <div class="m2-pd-cat-name">{{ $cat['label'] }} <small>({{ $cat['hits'] }}/{{ $cat['total'] }})</small></div>
                <div class="m2-pd-bar">
                    <div class="m2-pd-bar-track">
                        <div class="m2-pd-bar-fill {{ $cat['percentage'] >= 80 ? 'green' : ($cat['percentage'] >= 50 ? 'yellow' : 'red') }}"
                            style="width: {{ $cat['percentage'] }}%"></div>
                    </div>
                    <span class="m2-pd-bar-pct">{{ $cat['percentage'] }}%</span>
                </div>
            </div>
        @endforeach
    @endif
</div>
