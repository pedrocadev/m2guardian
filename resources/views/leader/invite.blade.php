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
        .brand { display: flex; align-items: center; }
        .brand img { height: 44px; width: auto; display: block; filter: drop-shadow(0 4px 14px rgba(204, 0, 0, 0.35)); }
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

        .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-scroll table { min-width: 720px; }
        .table-scroll::-webkit-scrollbar { height: 8px; }
        .table-scroll::-webkit-scrollbar-track { background: #f5f5f5; }
        .table-scroll::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
        .table-scroll::-webkit-scrollbar-thumb:hover { background: #999; }

        .pill { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .pill-done { background: #dcfce7; color: #16a34a; }
        .pill-pending { background: #fef9c3; color: #854d0e; }

        .btn-resend { background: transparent; border: 1px solid #ddd; color: #666; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; cursor: pointer; }
        .btn-resend:hover { border-color: #CC0000; color: #CC0000; }
        .btn-copy { background: #111; border: none; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; cursor: pointer; }
        .btn-copy:hover { background: #333; }
        .btn-copy.copied { background: #16a34a; }
        .btn-edit { background: transparent; border: 1px solid #ddd; color: #666; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; cursor: pointer; }
        .btn-edit:hover { border-color: #2563eb; color: #2563eb; }

        /* Modal de edição de e-mail */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center; justify-content: center;
            z-index: 1000;
        }
        .modal-overlay.show { display: flex; animation: fadeIn 0.15s; }
        .modal-box {
            background: #fff;
            border-radius: 12px;
            padding: 28px 32px;
            width: 100%; max-width: 460px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .modal-box h3 { font-size: 17px; font-weight: 800; margin-bottom: 6px; }
        .modal-info { font-size: 13px; color: #666; margin-bottom: 20px; }
        .modal-info strong { color: #111; }
        .modal-box label { display: block; font-size: 12px; font-weight: 700; color: #555; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; }
        .modal-box input[type=email] { width: 100%; border: 1px solid #ddd; border-radius: 6px; padding: 10px 12px; font-size: 14px; color: #111; outline: none; }
        .modal-box input[type=email]:focus { border-color: #CC0000; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }
        .modal-actions button { border: none; border-radius: 6px; padding: 9px 20px; font-size: 13px; font-weight: 700; cursor: pointer; }
        .modal-cancel { background: #f0f0f0; color: #444; }
        .modal-cancel:hover { background: #e0e0e0; }
        .modal-save { background: #CC0000; color: #fff; }
        .modal-save:hover { background: #aa0000; }

        .empty-state { text-align: center; padding: 40px; color: #aaa; font-size: 14px; }

        .toast { position: fixed; bottom: 24px; right: 24px; background: #111; color: #fff; padding: 12px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; display: none; z-index: 999; border-left: 3px solid #16a34a; }
        .toast.show { display: block; animation: fadeIn 0.2s; }
        @keyframes fadeIn { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="header">
    <div class="brand">
        <img src="{{ asset('images/backgrounds/Logo_guardiao.png') }}" alt="Guardião Digital">
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
        <div class="table-scroll">
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
                        <button type="button" class="btn-edit"
                            onclick="openEditEmailModal({{ $collab->id }}, @js($collab->email), @js($collab->name ?? ''))">
                            ✏️ Editar e-mail
                        </button>
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
        </div>
        @else
        <div class="empty-state">
            Nenhum colaborador convidado ainda. Use o formulário acima para começar.
        </div>
        @endif
    </div>
</div>

<div class="toast" id="toast">✅ Link copiado para a área de transferência!</div>

<div class="modal-overlay" id="editEmailModal" role="dialog" aria-modal="true" aria-labelledby="editEmailTitle">
    <div class="modal-box">
        <h3 id="editEmailTitle">Editar e-mail do colaborador</h3>
        <p class="modal-info">Colaborador: <strong id="editEmailName">—</strong></p>
        <form id="editEmailForm" method="POST">
            @csrf
            @method('PATCH')
            <label for="editEmailInput">Novo e-mail</label>
            <input type="email" name="email" id="editEmailInput" required maxlength="180" autocomplete="off">
            <div class="modal-actions">
                <button type="button" class="modal-cancel" onclick="closeEditEmailModal()">Cancelar</button>
                <button type="submit" class="modal-save">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditEmailModal(collaboratorId, currentEmail, name) {
    const modal = document.getElementById('editEmailModal');
    const form = document.getElementById('editEmailForm');
    const input = document.getElementById('editEmailInput');
    const nameEl = document.getElementById('editEmailName');

    form.action = `/lider/convidar/${collaboratorId}/email`;
    input.value = currentEmail;
    nameEl.textContent = name || currentEmail;
    modal.classList.add('show');
    setTimeout(() => { input.focus(); input.select(); }, 50);
}

function closeEditEmailModal() {
    document.getElementById('editEmailModal').classList.remove('show');
}

document.getElementById('editEmailModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditEmailModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('editEmailModal').classList.contains('show')) {
        closeEditEmailModal();
    }
});

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
