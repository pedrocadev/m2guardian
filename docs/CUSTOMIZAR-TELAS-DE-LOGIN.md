# Customizar as telas de login

Guia prático pra editar as telas de login do **admin** e do **líder** sem precisar entender o código todo. Cada seção tem o "o que mudar" + "onde mudar" + exemplo.

---

## 📍 Arquivos envolvidos

| Arquivo | O que controla |
|---------|----------------|
| [`resources/views/components/auth-layout.blade.php`](../resources/views/components/auth-layout.blade.php) | **Layout compartilhado** — hero escuro, estatísticas, brand, CSS comum dos 2 logins |
| [`resources/views/filament/pages/auth/login.blade.php`](../resources/views/filament/pages/auth/login.blade.php) | **Login admin** — só o conteúdo específico (texto, mascote) |
| [`resources/views/leader/login.blade.php`](../resources/views/leader/login.blade.php) | **Login líder** — só o conteúdo específico + estilos do formulário |
| [`public/images/mascots/`](../public/images/mascots/) | Mascotes — `login-admin.png` e `login-leader.png` |
| [`public/images/README.md`](../public/images/README.md) | Mapa completo de imagens do projeto |

---

## 🎨 1. Trocar o texto do hero (lado escuro)

O hero tem 3 textos principais: **título**, **destaque colorido** e **lead** (parágrafo explicativo).

### Admin

Edita `resources/views/filament/pages/auth/login.blade.php`:

```blade
<x-auth-layout
    title="O elo humano,"                    {{-- ← linha de cima --}}
    title-highlight="protegido."             {{-- ← gradient vermelho --}}
    lead="Plataforma de simulação de phishing e relatórios de conformidade. Meça e eleve a maturidade de segurança das equipes que você gerencia."
    ...
>
```

### Líder

Edita `resources/views/leader/login.blade.php`:

```blade
<x-auth-layout
    title="Eleve a maturidade"
    title-highlight="da sua equipe."
    lead="Acompanhe campanhas de phishing, métricas por colaborador..."
    ...
>
```

> **Dica:** o título quebra automaticamente entre `title` e `title-highlight`. O `title-highlight` é exibido com gradient vermelho (efeito visual).

---

## ✅ 2. Trocar os bullets de features

São os 3 itens com check vermelho abaixo do parágrafo lead.

```blade
:features="[
    'Campanhas realistas em E-mail, Teams e WhatsApp',
    'Métricas e ranking por colaborador',
    'Evidências para LGPD e ISO 27001',
]"
```

Pode adicionar mais ou tirar — não precisa ser exatamente 3. Layout se adapta.

---

## 🖼️ 3. Trocar o mascote

```blade
mascot="login-admin.png"      {{-- nome do arquivo em public/images/mascots/ --}}
```

### Pra usar uma imagem diferente

**Opção A — substituir a imagem existente** (mais simples):
1. Renomeia sua imagem nova como `login-admin.png` (ou `login-leader.png`)
2. Sobrescreve em `public/images/mascots/`
3. Pronto, sem mexer em código

**Opção B — adicionar uma imagem nova mantendo as outras:**
1. Coloca a imagem em `public/images/mascots/seu-mascote.png`
2. Atualiza o atributo: `mascot="seu-mascote.png"`

> Veja a lista completa de mascotes em [public/images/README.md](../public/images/README.md).

---

## 🎯 4. Trocar título do formulário (lado direito)

```blade
form-title="Entrar — Equipe M2"
form-subtitle="Acesse o painel administrativo do Guardião Digital com suas credenciais."
```

---

## 📊 5. Mudar as 3 estatísticas (91% / 74% / -60%)

⚠️ Estatísticas são **iguais nos 2 logins** porque são valores institucionais sobre o mercado de segurança.

Edita `resources/views/components/auth-layout.blade.php`. Procura por `m2-hero-stats`:

```blade
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
```

Mude o número e o label livremente. Se quiser **2 stats** em vez de 3, remove um bloco `<div>` inteiro — o layout se adapta automaticamente.

---

## 🎨 6. Mudar cores do hero (avançado)

Edita `resources/views/components/auth-layout.blade.php`, dentro do `<style>`:

### Cor de fundo do hero

```css
.m2-hero {
    background:
        radial-gradient(1200px 600px at 20% 0%, rgba(204, 0, 0, 0.22) 0%, transparent 60%),
        radial-gradient(900px 500px at 80% 100%, rgba(204, 0, 0, 0.10) 0%, transparent 60%),
        linear-gradient(180deg, #0a0a0a 0%, #161616 100%);
}
```

- `#0a0a0a` e `#161616` = tons de preto do fundo
- `rgba(204, 0, 0, ...)` = vermelho M2 (o "brilho" no fundo)

### Cor do destaque do título (gradient)

```css
.m2-hero-title em {
    background: linear-gradient(90deg, #ff4444, #ff7878);
}
```

### Cor do pill "Security Awareness Platform"

```css
.m2-hero-pill {
    border: 1px solid rgba(204, 0, 0, 0.5);
    color: #ff5e5e;
    background: rgba(204, 0, 0, 0.08);
}
```

---

## 📏 7. Mudar tamanho/posição do mascote

Edita `resources/views/components/auth-layout.blade.php`:

```css
.m2-hero-mascot {
    right: 40px;       /* ← distância da direita */
    bottom: 140px;     /* ← distância de baixo */
    width: 320px;      /* ← tamanho */
}
```

Se quiser ele mais pra esquerda, aumenta `right`. Pra subir, aumenta `bottom`. Pra crescer, aumenta `width`.

---

## 🔘 8. Mudar texto do botão de login

### Admin (usa componentes Filament)

O botão do admin é controlado pela classe customizada do Filament. Pra mudar o texto:

Edita `app/Filament/Pages/Auth/Login.php`:

```php
class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    protected function getAuthenticateFormAction(): \Filament\Actions\Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Entrar agora');     // ← muda o texto aqui
    }
}
```

### Líder

Edita `resources/views/leader/login.blade.php`, procura o `<button type="submit">`:

```blade
<button type="submit">Acessar painel</button>
```

---

## 🚨 9. Adicionar mensagem extra ou aviso

### Exemplo: aviso de manutenção em cima do form

Edita o login que você quer (admin OU líder), e adiciona ANTES do `<x-filament-panels::form>` (admin) ou ANTES do `<form>` (líder):

```blade
<div style="background:#fef3c7; border:1px solid #fbbf24; padding:12px; border-radius:8px; margin-bottom:18px; font-size:13px; color:#92400e;">
    ⚠️ Sistema em manutenção entre 22h-23h hoje.
</div>
```

---

## 🌐 10. Trocar URLs externas (footer mobile, esqueci a senha, etc.)

### Footer de instrução do líder

Edita `resources/views/leader/login.blade.php`:

```blade
<div class="leader-footer-note">
    Não recebeu acesso? Fale com seu <strong>contato M2</strong> para receber suas credenciais.
</div>
```

---

## 💡 Dicas gerais

1. **Sempre limpa o cache do Blade depois de editar:** `php artisan view:clear`
2. **Force reload no navegador (Ctrl+F5)** pra garantir que o CSS atualizou
3. **Não mexa em `body.fi-simple-layout` ou `.fi-simple-main` no admin** — são overrides necessários do Filament. Outras edições são seguras.
4. **Sempre teste em mobile** — o lado escuro (hero) some abaixo de 1024px e aparece um branding compacto. Use o DevTools do navegador (F12 → modo responsivo).

---

## ❓ Algo que não está nesse guia?

Peça pra eu (Claude) fazer e adicionar a explicação aqui. Os arquivos relevantes são compactos e fáceis de editar — qualquer customização nova vale ser documentada pra você consultar depois.

---

*M2 Cloud & Security · Guardião Digital · Junho 2026*
