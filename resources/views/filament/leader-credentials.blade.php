@php
    /** @var \App\Models\Leader $leader */
    /** @var string $password */
    /** @var string $loginUrl */
@endphp

<div x-data="{ copy(text, btn) {
        const orig = btn.innerText;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            const ta = document.createElement('textarea');
            ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
            document.body.appendChild(ta); ta.focus(); ta.select();
            try { document.execCommand('copy'); } catch(e) {}
            document.body.removeChild(ta);
        }
        btn.innerText = '✓ Copiado!';
        btn.classList.add('!bg-green-600');
        setTimeout(() => { btn.innerText = orig; btn.classList.remove('!bg-green-600'); }, 2000);
    }}" class="space-y-4">

    <div class="rounded-lg bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 p-3 text-sm text-amber-800 dark:text-amber-200">
        ⚠️ Esta senha será mostrada <strong>apenas uma vez</strong>. Copie e envie ao líder agora.
        Se perder, será necessário gerar uma nova.
    </div>

    <div>
        <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
            Empresa
        </label>
        <div class="text-sm font-semibold">{{ $leader->company->name ?? '—' }}</div>
    </div>

    <div>
        <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
            URL de Acesso
        </label>
        <div class="flex gap-2">
            <input type="text" readonly value="{{ $loginUrl }}"
                   class="flex-1 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm font-mono"
                   onclick="this.select()">
            <button type="button" @click="copy('{{ $loginUrl }}', $event.currentTarget)"
                    class="px-3 py-2 rounded-md bg-gray-800 dark:bg-gray-700 text-white text-xs font-bold whitespace-nowrap">
                Copiar
            </button>
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
            E-mail (Usuário)
        </label>
        <div class="flex gap-2">
            <input type="text" readonly value="{{ $leader->email }}"
                   class="flex-1 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm font-mono"
                   onclick="this.select()">
            <button type="button" @click="copy('{{ $leader->email }}', $event.currentTarget)"
                    class="px-3 py-2 rounded-md bg-gray-800 dark:bg-gray-700 text-white text-xs font-bold whitespace-nowrap">
                Copiar
            </button>
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
            Senha Gerada
        </label>
        <div class="flex gap-2">
            <input type="text" readonly value="{{ $password }}"
                   class="flex-1 px-3 py-2 rounded-md border border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20 text-sm font-mono font-bold text-red-700 dark:text-red-300"
                   onclick="this.select()">
            <button type="button" @click="copy('{{ $password }}', $event.currentTarget)"
                    class="px-3 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-bold whitespace-nowrap">
                Copiar
            </button>
        </div>
    </div>

    <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
        <button type="button"
                @click="copy('Acesso ao Painel do Líder — Guardião Digital\n\nURL: {{ $loginUrl }}\nE-mail: {{ $leader->email }}\nSenha: {{ $password }}\n\nObs: senha pessoal e intransferível.', $event.currentTarget)"
                class="w-full px-4 py-2.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold">
            📋 Copiar Tudo (URL + Email + Senha)
        </button>
    </div>
</div>
