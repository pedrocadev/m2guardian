<div style="font-family: ui-sans-serif, system-ui, sans-serif; color: #111;">
    <div style="font-size: 12px; color: #666; margin-bottom: 16px;">
        {{ $collaborator->email }}
        · {{ $collaborator->department ?: 'Sem departamento' }}
        · {{ $collaborator->company->name ?? '—' }}
        · Concluiu em {{ $collaborator->completed_at?->format('d/m/Y H:i') ?? '—' }}
    </div>

    @if($scoreData['total'] === 0)
        <div style="text-align: center; color: #999; padding: 30px; font-size: 13px;">Sem dados de treinamento.</div>
    @else
        @include('partials.posture-detail', ['scoreData' => $scoreData])
    @endif
</div>
