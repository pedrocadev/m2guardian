<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convidar Colaboradores — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f5f7; color: #111; min-height: 100vh; }

        .header { background: #111; border-bottom: 3px solid #CC0000; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; height: 60px; }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand-name { color: #fff; font-weight: 900; font-size: 15px; letter-spacing: 1px; }
        .brand-sub { color: #888; font-size: 10px; display: block; }
        .header-right { display: flex; align-items: center; gap: 16px; }
        .header-user { color: #ccc; font-size: 13px; text-align: right; }
        .header-user strong { color: #fff; display: block; }
        .btn-logout { background: transparent; border: 1px solid #444; color: #ccc; padding: 6px 14px; border-radius: 4px; font-size: 12px; cursor: pointer; text-decoration: none; }
        .btn-logout:hover { border-color: #CC0000; color: #CC0000; }
        .btn-back { background: transparent; border: 1px solid #444; color: #ccc; padding: 6px 14px; border-radius: 4px; font-size: 12px; text-decoration: none; }
        .btn-back:hover { border-color: #fff; color: #fff; }

        .main { max-width: 900px; margin: 0 auto; padding: 32px 24px; }

        .page-header { margin-bottom: 28px; }
        .page-header h1 { font-size: 22px; font-weight: 800; }
        .page-header p { color: #666; font-size: 14px; margin-top: 4px; }

        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 28px 32px; margin-bottom: 28px; }
        .card-title { font-size: 15px; font-weight: 800; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }
        label { font-size: 12px; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
        input, select { border: 1px solid #ddd; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #111; outline: none; transition: border-color 0.15s; }
        input:focus, select:focus { border-color: #CC0000; }
        .field-error { font-size: 11px; color: #dc2626; margin-top: 2px; }

        .btn-submit { background: #CC0000; color: #fff; border: none; border-radius: 8px; padding: 12px 28px; font-size: 14px; font-weight: 700; cursor: pointer; letter-spacing: 0.5px; }
        .btn-submit:hover { background: #aa0000; }

        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; }
        .alert-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
        .alert-warning { background: #fff8e1; border: 1px solid #fde047; color: #854d0e; }
        .alert-error { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }

        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f9f9f9; font-size: 11px; font-weight: 700; color: #999; letter-spacing: 0.5px; text-transform: uppercase; padding: 11px 16px; text-align: left; border-bottom: 1px solid #eee; }
        tbody td { padding: 12px 16px; font-size: 13px; color: #333; border-bottom: 1px solid #f5f5f5; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #fafafa; }

        .pill { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .pill-done { background: #dcfce7; color: #16a34a; }
        .pill-pending { background: #fef9c3; color: #854d0e; }

        .btn-resend { background: transparent; border: 1px solid #ddd; color: #666; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; cursor: pointer; }
        .btn-resend:hover { border-color: #CC0000; color: #CC0000; }
        .btn-copy { background: #111; border: none; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; cursor: pointer; }
        .btn-copy:hover { background: #333; }
        .btn-copy.copied { background: #16a34a; }

        .empty-state { text-align: center; padding: 40px; color: #aaa; font-size: 14px; }

        .toast { position: fixed; bottom: 24px; right: 24px; background: #111; color: #fff; padding: 12px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; display: none; z-index: 999; border-left: 3px solid #16a34a; }
        .toast.show { display: block; animation: fadeIn 0.2s; }
        @keyframes fadeIn { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="header">
    <div class="brand">
        <span style="font-size:22px;">🛡️</span>
        <div>
            <span class="brand-name">GUARDIÃO DIGITAL</span>
            <span class="brand-sub">by M2 Cloud & Security</span>
        </div>
    </div>
    <div class="header-right">
        <a href="{{ route('leader.dashboard') }}" class="btn-back">← Painel</a>
        <div class="header-user">
            <strong>{{ $leader->name }}</strong>
            {{ $leader->company->name }}
        </div>
        <form method="POST" action="{{ route('leader.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Sair</button>
        </form>
    </div>
</div>

<div class="main">
    <div class="page-header">
        <h1>Convidar Colaboradores</h1>
        <p>Envie o link de treinamento diretamente para o e-mail de cada colaborador.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning">⚠️ {{ session('warning') }}</div>
    @endif

    {{-- Formulário de convite --}}
    <div class="card">
        <div class="card-title">Novo Convite</div>
        <form method="POST" action="{{ route('leader.invite.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>E-mail *</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="colaborador@empresa.com" required>
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Nome (opcional)</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="João da Silva">
                </div>
                <div class="form-group">
                    <label>Departamento (opcional)</label>
                    <input type="text" name="department" value="{{ old('department') }}" placeholder="RH, Financeiro, TI...">
                </div>
                <div class="form-group">
                    <label>Perfil</label>
                    <select name="profile">
                        <option value="outro" {{ old('profile') == 'outro' ? 'selected' : '' }}>Outro</option>
                        <option value="rh" {{ old('profile') == 'rh' ? 'selected' : '' }}>RH</option>
                        <option value="financeiro" {{ old('profile') == 'financeiro' ? 'selected' : '' }}>Financeiro</option>
                        <option value="operacao" {{ old('profile') == 'operacao' ? 'selected' : '' }}>Operação</option>
                    </select>
                </div>
                <div class="form-group full" style="margin-top: 4px;">
                    <button type="submit" class="btn-submit">Enviar Convite →</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Lista de colaboradores --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px 28px 16px; border-bottom: 1px solid #f0f0f0;">
            <span style="font-size:15px; font-weight:800;">Colaboradores Convidados</span>
            <span style="background:#f0f0f0; color:#666; font-size:11px; font-weight:700; padding:2px 8px; border-radius:20px; margin-left:8px;">{{ $collaborators->count() }}</span>
        </div>
        @if($collaborators->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Nome / E-mail</th>
                    <th>Departamento</th>
                    <th>Convidado em</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($collaborators as $collab)
                <tr>
                    <td>
                        <strong>{{ $collab->name ?? '—' }}</strong><br>
                        <span style="color:#888; font-size:12px;">{{ $collab->email }}</span>
                    </td>
                    <td>{{ $collab->department ?? '—' }}</td>
                    <td>{{ $collab->invited_at?->format('d/m/Y') ?? '—' }}</td>
                    <td>
                        @if($collab->completed_at)
                            <span class="pill pill-done">✔ Concluído</span>
                        @elseif($collab->first_access_at)
                            <span class="pill" style="background:#eff6ff; color:#1d4ed8;">Em andamento</span>
                        @else
                            <span class="pill pill-pending">Aguardando</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        @if(!$collab->completed_at)
                        <form method="POST" action="{{ route('leader.invite.resend', $collab->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-resend">Reenviar e-mail</button>
                        </form>
                        <button class="btn-copy"
                            data-url="{{ route('leader.invite.generate-link', $collab->id) }}"
                            data-token="{{ csrf_token() }}"
                            onclick="copyLink(this)">
                            📋 Copiar Link
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            Nenhum colaborador convidado ainda. Use o formulário acima para começar.
        </div>
        @endif
    </div>
</div>

<div class="toast" id="toast">✅ Link copiado para a área de transferência!</div>

<script>
async function copyLink(btn) {
    btn.textContent = '⏳ Gerando...';
    btn.disabled = true;

    let url = null;

    try {
        const res = await fetch(btn.dataset.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': btn.dataset.token,
                'Accept': 'application/json',
            },
        });

        if (!res.ok) throw new Error('Falha na requisição: ' + res.status);
        const data = await res.json();
        url = data.url;
    } catch (e) {
        btn.textContent = '📋 Copiar Link';
        btn.disabled = false;
        alert('Erro ao gerar link: ' + e.message);
        return;
    }

    // Copia para clipboard (funciona em HTTP e HTTPS)
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(url);
        } else {
            // Fallback para HTTP
            const input = document.createElement('textarea');
            input.value = url;
            input.style.position = 'fixed';
            input.style.opacity = '0';
            document.body.appendChild(input);
            input.focus();
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
        }

        btn.textContent = '✔ Copiado!';
        btn.classList.add('copied');

        const toast = document.getElementById('toast');
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
            btn.textContent = '📋 Copiar Link';
            btn.classList.remove('copied');
            btn.disabled = false;
        }, 3000);

    } catch (e) {
        // Se até o fallback falhar, mostra o link para copiar manualmente
        btn.textContent = '📋 Copiar Link';
        btn.disabled = false;
        prompt('Copie o link manualmente (Ctrl+C):', url);
    }
}
</script>
</body>
</html>
