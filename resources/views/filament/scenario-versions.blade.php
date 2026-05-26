<div style="font-family: Arial, sans-serif;">
    @forelse($versions as $v)
    <div style="border-bottom: 1px solid #eee; padding: 12px 0; display: flex; align-items: flex-start; gap: 16px;">
        <div style="background: #111; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px; white-space: nowrap;">
            v{{ $v->version }}
        </div>
        <div style="flex: 1;">
            <div style="font-size: 13px; color: #555;">{{ $v->edit_summary ?? 'Sem descrição' }}</div>
            <div style="font-size: 11px; color: #aaa; margin-top: 3px;">
                {{ $v->created_at?->format('d/m/Y H:i') }}
                @if($v->editedBy)· {{ $v->editedBy->name }}@endif
            </div>
        </div>
    </div>
    @empty
    <p style="color: #aaa; font-size: 13px; text-align: center; padding: 24px 0;">Nenhuma versão registrada ainda.</p>
    @endforelse
</div>
