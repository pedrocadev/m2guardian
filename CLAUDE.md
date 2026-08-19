# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## 🚀 Onboarding — Setup para novo desenvolvedor

Passos completos pra um novo dev clonar e rodar o projeto localmente do zero.

### Pré-requisitos (Windows)

1. **Laravel Herd** — https://herd.laravel.com/windows (grátis)
   - Instala PHP 8.4 + Nginx + MariaDB automaticamente
   - Cria domínio `.test` local (o projeto será acessado em `http://m2guardian.test`)

2. **Node.js 20+** — https://nodejs.org

3. **Composer** — vem com o Herd

4. **Git** — https://git-scm.com/download/win

5. **Editor** — VS Code recomendado (existe extensão do Filament e Laravel)

### Passo 1 — Clonar o repositório

Você precisa acesso aos repos GitHub. Se está tomando este projeto do Pedro, peça:
- Acesso ao **M2-Cloud-Dev/M2Guardian** (privado, source de produção)
- Opcionalmente acesso ao **pedrocadev/m2guardian** (público, espelho pessoal do Pedro)

```powershell
cd C:\Projects   # ou qualquer pasta que preferir
git clone https://github.com/M2-Cloud-Dev/M2Guardian.git m2guardian
cd m2guardian
```

### Passo 2 — Configurar Herd

1. Abre o Herd
2. **Sites** → **Park directory** → aponta pra `C:\Projects` (isso faz o Herd escanear e criar `.test` automaticamente pra cada pasta que tem `public/index.php`)
3. Verifica que `m2guardian.test` aparece na lista de sites
4. **Services** → confirma que **MariaDB** está rodando

### Passo 3 — Adicionar PHP no PATH da sessão

Todo terminal novo precisa disso (Herd não seta globalmente):

```powershell
$env:PATH = "C:\Users\<SEU-USUARIO>\.config\herd\bin;$env:PATH"
```

Substitui `<SEU-USUARIO>` pelo nome do usuário Windows. Dica: coloca isso no perfil do PowerShell (`$PROFILE`) pra automatizar.

### Passo 4 — Instalar dependências

```powershell
composer install
npm install
```

### Passo 5 — Configurar `.env`

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Edita `.env`:
```env
APP_URL=http://m2guardian.test
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=m2guardian
DB_USERNAME=root
DB_PASSWORD=          # deixa vazio — Herd MariaDB roda sem senha em dev
```

### Passo 6 — Criar o banco

Abre o **HeidiSQL** (ou outro cliente MySQL apontando pra `127.0.0.1:3306`, user `root`, sem senha) e executa:
```sql
CREATE DATABASE m2guardian CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Passo 7 — Rodar migrations + seeders

```powershell
php artisan migrate --seed
```

Isso vai:
- Criar todas as tabelas
- Popular 13 cenários de treinamento default
- Criar um super admin: `suporte@m2cloud.com.br` / `M2Guardian@2026`

### Passo 8 — Compilar assets

```powershell
npm run build
# OU pra dev com hot-reload:
npm run dev
```

### Passo 9 — Acessar

Abre no navegador:
- **Admin panel:** http://m2guardian.test/admin (login com `suporte@m2cloud.com.br` / `M2Guardian@2026`)
- **Login do líder:** http://m2guardian.test/lider/login
- **Fluxo do colaborador:** só via magic link — gera um pelo admin ou painel do líder

### Passo 10 — Rodar testes

```powershell
php artisan test
```

22 testes de smoke. Se todos passarem, seu ambiente está OK.

---

## 🎯 Fluxos principais do sistema (como testar)

### Fluxo 1: Admin cria empresa + líder
1. Login `/admin/login`
2. **Empresas** → **Nova empresa**
3. Preenche CNPJ (a razão social é buscada automaticamente na BrasilAPI)
4. Aba **Líder Principal** (obrigatório) → nome + e-mail + senha
5. Salva → empresa + líder criados em uma transação

### Fluxo 2: Líder convida colaborador
1. Login `/lider/login` com credenciais criadas
2. **Convidar** → preenche e-mail do colaborador
3. Clica **Enviar convite** → gera magic link + tenta enviar e-mail
4. Ou **Copiar link** → pega o URL e envia manualmente
5. Colaborador clica no link → vê vídeo intro → welcome → chat de treinamento

### Fluxo 3: Colaborador faz treinamento
1. Abre magic link `/m/{token}` → consome (single-use)
2. Vídeo intro de 6s (skip após 50%)
3. Welcome unificado ("Bem-vindo à Jornada Guardião")
4. Lista de cenários (bloqueados em ordem)
5. Cenário aberto → chat imersivo estilo plataforma (ver "Modos de plataforma no chat")
6. Responde perguntas → recebe feedback
7. Concluiu tudo → tela final com certificado

### Fluxo 4: Líder analisa resultados
1. Dashboard `/lider/dashboard`
2. Vê postura corporativa + score por colaborador
3. Clica em colaborador → drill-down individual `/lider/colaborador/{id}/postura`

---

## Project Overview

**M2 Guardião Digital** is a **B2B SaaS** for corporate security-awareness training. M2 admins provision client companies; each company has leaders (managers) who invite collaborators (employees) to take phishing/BEC/social-engineering training scenarios. Two license tiers (Demo: 3 collaborators / 3 scenarios; Pro: configurable / all 13 scenarios).

**Production status (deployed 2026-05-28):** Live at **https://m2guardiao.com.br** on Oracle Cloud Always Free (ARM Ampere, Ubuntu 22.04 LTS, 8GB RAM). HTTPS via Let's Encrypt with auto-renewal. Cost: R$ 0/month.

The dev workflow is **local-first** (Herd on Windows) with **git-deployed production** (Ubuntu VPS via scripts in `deploy/`). Never edit code directly on the production server — always commit and run `deploy/03-deploy-app.sh`.

## Environment

- **Runtime:** Laravel 11 + PHP 8.4 via **Laravel Herd** on Windows (production also runs PHP 8.4 — required by Symfony 8.x)
- **Database:** MariaDB (local — managed by Herd; DB name `m2guardian`)
- **Local URL:** `http://m2guardian.test` (Herd auto-site)
- **PHP not in PATH by default.** Every new PowerShell session needs:
  ```powershell
  $env:PATH = "C:\Users\Pedrosa\.config\herd\bin;$env:PATH"
  ```

## Git Setup (multi-remote)

Single `git push origin main` deploys to **both repositories simultaneously** via configured multi-pushURL:

- **Personal (public):** https://github.com/pedrocadev/m2guardian
- **Company (private, source of production):** https://github.com/M2-Solution-Dev/M2Guardian.2-0

`origin` is configured with 2 pushURLs. To verify: `git remote -v` → expect `origin` with one fetch URL and two push URLs.

Production VM pulls from M2-Solution-Dev/M2Guardian.2-0 via HTTPS with a PAT stored in `/var/www/m2guardian/.git-credentials` (chmod 600). Org bans Deploy Keys, so PAT is the only option.

## Checkpoints (git tags)

Marcos estáveis usados para rollback rápido antes de refatorações grandes. Cada tag é **anotada** (contém metadados) e replicada nos 2 remotes.

| Tag | Commit | Data | Estado preservado |
|-----|--------|------|-------------------|
| `checkpoint-pre-quiz-refactor` | `1afb4a6` | 2026-07-02 | Login/mascotes/logo novos deployados; painel do líder com editar e-mail + scroll horizontal; chat com barra de progresso colorida. **Estado imediatamente anterior à refatoração da aba de perguntas do usuário final (`training/show.blade.php`).** |
| `checkpoint-pre-retry-system` | `69dd288` | 2026-07-08 | Feature de troca obrigatória de senha do líder no primeiro acesso já implantada. **Estado imediatamente anterior à refatoração da regra de aprovação (≥80%) + refazer teste + múltiplas TrainingSessions por colaborador + certificado.** |
| `checkpoint-pre-wizard-e-emails` | `d0f7fa5` | 2026-07-29 | Sistema com regra ≥80%, refazer teste e certificado já implantados. **Estado imediatamente anterior às features grandes de 29/07: wizard 3 passos no editor de cenários, novo layout do e-mail de convite, transição rápida entre chats mesma plataforma e 10 correções pós-review dos 5 agentes.** |
| `checkpoint-pre-logo-global` | `1ea56b7` | 2026-07-30 | Wizard editor + novo e-mail + transição rápida já implantados. **Estado imediatamente anterior à varredura de logo antiga em 7 telas (chat/2FA/completed/leader-invite/magic-link-invalid/training-pdf/auth-layout).** Se rollback: telas voltam a mostrar texto "GUARDIÃO DIGITAL" ou emoji + SVG. |
| `checkpoint-pre-features-31jul` | `9369355` | 2026-07-31 | Logo oficial em 7 telas + randomização das opções por sessão + workflow `recorrente.yml`. **Estado imediatamente anterior às próximas features grandes de 31/07.** Se rollback: opções voltam a aparecer sempre na ordem cadastrada no admin (risco de gabarito na empresa). |
| `checkpoint-31jul-multiempresas` | `206034b` | 2026-07-31 | Randomização + logo global + **feature many-to-many de cenários ↔ empresas (pivot `company_scenario`)** + aba "Cenários vinculados" na tela de empresa via RelationManager. Regra nova: empresa com vínculos vê SÓ os vinculados; sem vínculos vê SÓ os `is_default`. **Marco antes da próxima alteração grande.** Se rollback: cenários voltam ao belongs-to `company_id` (belongs-to UMA empresa apenas). |
| `checkpoint-pre-mascote-intro-textos` | `8ea1cfe` | 2026-08-05 | Estado imediatamente anterior à personalização das falas do mascote na tela de transição entre cenários (título/subtítulo específicos por plataforma + botão "Iniciar Missão" + timer 10s no lugar de 5s). Se rollback: transição volta ao "Agora é hora do X" genérico + "Bora encarar →" + auto-avanço 5s. |
| `checkpoint-pre-telegram-azul` | `40dc804` | 2026-08-05 | Falas do mascote personalizadas já implantadas. **Estado antes da paleta violeta `#8774E1` do Telegram ser substituída pelo `#0088cc` do logo.** Se rollback: Telegram volta ao roxo escuro violeta. Preservado por Pedro pra permitir voltar caso a nova paleta não agrade. |
| `checkpoint-pre-telegram-nightblue` | `40dc804` | 2026-08-05 | Idem `checkpoint-pre-telegram-azul` — snapshot preservado durante iterações da paleta azul (Night Blue `#17212B` foi testada e descartada em favor do `#0088cc` vívido). |
| `checkpoint-pre-slack` | `dc67186` | 2026-08-10 | Telegram já com paleta `#0088cc` deployada. **Estado imediatamente anterior à adição da plataforma Slack (5ª plataforma).** Se rollback: enum volta a `('wapp','teams','email','telegram','outro')`, tab "Slack" some do admin, CSS `.platform-slack` some. Migration `down()` do commit da release já cuida da migração `slack → outro` antes do rollback do enum. |

**Voltar a um checkpoint (não-destrutivo, cria detached HEAD):**
```bash
git checkout <nome-da-tag>
```

**Voltar destrutivamente (⚠️ perde commits posteriores da main):**
```bash
git reset --hard <nome-da-tag>
git push origin main --force-with-lease  # só se realmente for necessário
```

**Deploy a partir de um checkpoint na VM:**
```bash
cd /var/www/m2guardian
sudo -u m2guardian git fetch --tags
sudo -u m2guardian git checkout <nome-da-tag>
sudo -u m2guardian php artisan optimize:clear
sudo systemctl restart php8.4-fpm
```

**Criar novo checkpoint antes de refactor grande:**
```bash
git tag -a <nome> -m "descrição detalhada do estado preservado"
git push origin <nome>
```
Depois adicione uma linha nova na tabela acima.

## Common Commands

```powershell
# Clear all caches (run after .env, route, or config changes)
php artisan optimize:clear

# Reset DB completely and re-seed (DANGER — wipes data)
php artisan migrate:fresh --seed

# Run only specific seeders (safe — uses updateOrCreate)
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=ScenarioSeeder

# Tests (Pest) — uses SQLite in-memory, does NOT touch local DB
php artisan test
php artisan test tests/Feature/MagicLinkTest.php   # single file

# Process queued emails (driver: database)
php artisan queue:work

# Interactive REPL
php artisan tinker
```

**Default super admin:** `suporte@m2cloud.com.br` / `M2Guardian@2026` — created by `AdminSeeder`. **Re-run `db:seed --class=AdminSeeder` if it disappears** (rare cases where local DB was wiped). The production admin password was changed manually after deploy.

## Slash Commands (skills) for Production Validation

Two specialized subagents live in `.claude/agents/` with thin slash-command wrappers in `.claude/commands/`:

- **`/test-prod`** → invokes the `production-tester` subagent. Runs 10 HTTP smoke tests against `https://m2guardiao.com.br` (status codes, redirects, asset loads, auth-required routes, response headers). Returns a markdown report with pass/fail + severity. Use after every production deploy.
- **`/test-security`** → invokes the `security-tester` subagent. Runs 12 defensive (white-hat, non-invasive) checks: TLS cert validity, security headers, 19 sensitive file paths blocked, rate-limit functioning, CSRF enforced, SQL/XSS pattern rejection, directory listing disabled, auth on protected routes. Returns a 0-100 score with OWASP-mapped findings.

Both agents run **purely via HTTP** — no SSH, no code modifications, no rate-limit breaching. Safe to invoke any time.

## Architecture

### Authentication — 3 independent guards

| Guard | Model | Auth method | Middleware alias | Entry route |
|---|---|---|---|---|
| `admin` | `Admin` | Email + password + 2FA TOTP | (Filament handles) | `/admin/login` |
| `leader` | `Leader` | **Email + password** (generated by admin) | `auth.leader` | `/lider/login` |
| `collaborator` | `Collaborator` | **Magic link** (single-use, 30 days) | `auth.collaborator` | `/m/{token}` (legado: `/auth/acesso?t=...`) |

Guards configured in `config/auth.php`. Middleware aliases registered in `bootstrap/app.php`. **Leader auth is password-based now**, not magic link (changed mid-development). Magic link controller still exists and works for both leader/collaborator polymorphically — but the canonical leader entry point is the password login page.

### Magic Link mechanism (`MagicLink` model)

- `MagicLink::generateFor($model, $purpose, $expiresDays)` returns `['plain_token' => ..., 'magic_link' => $record]`
- `MagicLink::generateUrlFor($model, $purpose, $expiresDays)` — wrapper que já retorna a URL pronta (`route('magic-link.short', ...)`)
- DB stores **SHA-256 hash only** — plain token never persisted
- Token: 12 chars de `[A-Za-z0-9]` (~71 bits de entropia)
- URL preferida: `/m/{token}` (curta, ~46 chars total). URL legado `/auth/acesso?t=...` mantida pra emails antigos
- `MagicLinkController::consume()` aceita token via path param OU query string, re-hash, finds via `findValid()`, marca `consumed_at`, logs into the correct guard polymorphically
- Single-use (`consumed_at` non-null = exhausted) + time-limited (`expires_at`)

### Admin Panel (Filament 3.3)

Mounted at `/admin`, uses `admin` guard. Config in `app/Providers/Filament/AdminPanelProvider.php`. Custom M2 branding (red `#CC0000` + black sidebar) injected via `public/css/filament-theme.css` through `renderHook('panels::head.end')`.

Resources auto-discovered from `app/Filament/Resources/`. Key resources:
- `AdminResource` — super-admin only (`canAccess()` checks `isSuper()`)
- `CompanyResource` — campo CNPJ obrigatório com **lookup live na BrasilAPI** (via [CnpjService](app/Services/CnpjService.php)) que preenche a razão social automaticamente; campo "Apelido" obrigatório; CNPJ **não editável após salvar** (`->disabledOn('edit')`); inclui modal "Ver Resultados" com stats por empresa
- `LeaderResource` — actions: `Gerar/Resetar Senha`, `Mostrar Credenciais` (modal with working JS clipboard), `Enviar por E-mail`
- `CollaboratorResource` — actions: `Enviar Convite`, `Copiar Link`, bulk invite
- `ScenarioResource` — visual editor with nested Repeaters; **5 tabs** by platform in `ListScenarios::getTabs()`

### Cadastro de empresa: regras fortes

**Empresa só nasce com líder.** O form de criar empresa tem uma seção "Líder Principal" (visível apenas em create via `->visibleOn('create')`). O `CreateCompany::handleRecordCreation()` envolve a criação de **empresa + líder em uma única transação DB** — se um falha, o outro é revertido. Não é possível criar empresa sem líder.

**Empresa nunca é deletada — só arquivada.** Tanto `Company` quanto `Leader` usam `SoftDeletes`. A `DeleteAction` foi renomeada pra "Arquivar" e o `BulkDeleteAction` foi removido. Filtro `TrashedFilter` mostra arquivadas. `RestoreAction` desarquiva. `forceDelete()` continua possível via tinker pra casos extremos, mas não pelo painel.

**Empresa nunca fica sem líder.** O `Leader::booted()` observa o evento `deleting` e lança `RuntimeException` se o líder em questão for o último ativo da empresa. Camada de UI (`->before()` nas DeleteActions de `LeaderResource` e `EditLeader`) faz check via `$leader->canBeArchived()` e mostra notification amigável antes de chamar a ação — mas o Model é o último guard contra deletes via tinker/API.

**Líder principal (`is_primary`).** O líder cadastrado **junto com a empresa** via `CreateCompany` recebe `is_primary = true` automaticamente. Esse líder tem 3 proteções fortes:
- Não pode ser **arquivado** nunca (`canBeArchived()` retorna false; `booted::deleting` lança exception específica)
- Não pode ter **`company_id` alterado** (`booted::saving` bloqueia — vínculo com a empresa é permanente)
- Não pode ter **`name` alterado** (mesmo guard)

Campos `email`, `phone`, `role_label`, senha continuam editáveis. No UI Filament (`LeaderResource`), os campos imutáveis aparecem `->disabled()` com helperText explicando. A coluna "Nome" mostra "★ Líder Principal" em negrito como descrição. Backfill na migration: o líder mais antigo (menor `id`) de cada empresa foi marcado como primary pra empresas pré-existentes.

**CNPJ é único (incluindo arquivados).** O `unique()` no form NÃO filtra `whereNull('deleted_at')` — porque a UNIQUE constraint do banco abrange registros trashed também. Tentar criar com CNPJ duplicado mostra mensagem amigável sugerindo verificar o filtro "Arquivadas" + desarquivar.

### Release notes popup (atualizações no admin)

Popup que aparece **uma vez por sessão** (a cada login) na home `/admin/dashboard` mostrando a release publicada mais recente. Inspirado no aviso de atualização do Milvus.

**Tabela `releases`** (id, title, released_at, content markdown, published bool). Gerenciada via `ReleaseResource` em **Configurações → Atualizações**.

**Trigger por sessão (não por DB):** o blade [resources/views/filament/release-popup.blade.php](resources/views/filament/release-popup.blade.php) é injetado via `panels::body.end` render hook em [AdminPanelProvider.php](app/Providers/Filament/AdminPanelProvider.php). Lógica:

1. Só renderiza se `request()->routeIs('filament.admin.pages.dashboard')`
2. Se `session('release_popup_shown')` já existe → return (já viu nesta sessão)
3. Busca `Release::latestPublished()` — se null, return
4. Side-effect: `session(['release_popup_shown' => true])` durante o render
5. Renderiza HTML do popup com fechamento JS (sem backend)

**Logout** invalida a sessão → `release_popup_shown` some → próximo login mostra de novo. **Não há** controller de dismiss — o botão "OK, entendi" é só JS (`document.getElementById(...).remove()`).

**Placeholders no `content`** (substituídos antes do markdown via `strtr`):
- `{nome}` → primeiro nome do admin logado
- `{nome_completo}` → nome completo
- `{email}` → email

CSS no [public/css/filament-theme.css](public/css/filament-theme.css) bloco "Release notes popup" — badge "🎉 NOVIDADE" + gradiente vermelho M2 + animação pop-in cubic-bezier.

### Filament closure-parameter gotcha (important!)

Filament resolves closures via **parameter name reflection**, NOT positional binding. `modifyQueryUsing()` on `Tab` binds `$query` specifically. **Using `$q` (or any other name) throws `BindingResolutionException: [$q] was unresolvable`.** Always:

```php
->modifyQueryUsing(fn ($query) => $query->where(...))   // ✅
->modifyQueryUsing(fn ($q) => $q->where(...))           // ❌ breaks
```

Same applies to filter `->query()` callbacks.

### ⚠️ Blade parser gotcha — `@if(...)` textual dentro de `<style>` quebra a compilação

Blade compiler é **ganancioso** e detecta qualquer string `@if(`, `@endif`, `@foreach`, etc como diretiva — **mesmo dentro de tags `<style>` ou `<script>`, mesmo dentro de comentários CSS/JS**. Comentário do tipo `/* Blade envolve o span com @if(condicao) */` faz o parser abrir uma diretiva `@if` que nunca fecha → `ParseError: syntax error, unexpected end of file, expecting "elseif" or "else" or "endif"` na compilação, HTTP 500 em produção.

**Aconteceu em 2026-08-18** — comentário CSS `Blade envolve o span com @if(platform === 'teams')` na seção `.external-tag` do `training/show.blade.php` derrubou toda a rota `/treinamento/cenario/{id}` com 500. Fix (`78a6bd1`): trocar o `@if` textual por outra formulação sem o `@` (ex: `Blade condicional (platform === teams)`).

**Como detectar antes de subir:** `grep -c "@if(" show.blade.php` vs `grep -c "@endif" show.blade.php` — se os números não baterem, tem `@if` textual em algum lugar (comentário/string). Blade compile via `php artisan view:cache` também flagra localmente antes do deploy.

**Regra:** ao escrever comentário/documentação DENTRO de blade file, nunca usar `@if`, `@foreach`, `@endif` etc de forma literal. Alternativas: usar `@@if` (escape Blade — vira `@if` literal na saída), OU reescrever sem o `@` (`if condicional`, `condicional Blade`, etc).

### Filament CSS overlay gotcha (learned the hard way)

Do **NOT** use a fixed `body::before` overlay with `position: relative; z-index: 1;` on `.fi-topbar` / `.fi-sidebar` / `.fi-main` to create background effects. This creates a new stacking context that **silently breaks Filament's user-menu dropdown** (it opens visually but clicks fall through to nothing). The dropdown uses Floating UI which positions absolute at body level — the stacking context disrupts event handling.

**Use stacked CSS backgrounds instead** (current technique in `public/css/filament-theme.css`):

```css
body.fi-body {
    background-image:
        linear-gradient(rgba(255,255,255,0.92), rgba(255,255,255,0.92)),
        url('/images/backgrounds/admin-bg.jpg') !important;
    background-attachment: fixed !important;
}
```

No pseudo-elements, no z-index manipulation. Equivalent visual result, no JS-breaking side effects.

### Areas by guard

- `/admin/*` — Filament panel (admin guard)
- `/lider/login` — password login form
- `/lider/dashboard` — leader dashboard (`auth.leader`)
- `/lider/convidar` — leader's collaborator invite UI
- `/lider/relatorio/pdf` — PDF report download
- `/treinamento/*` — collaborator training flow (`auth.collaborator`)
- `/m/{token}` (preferida) e `/auth/acesso?t=` (legado) — magic link consumption (public, rate-limited)
- `/admin/dois-fatores/*` — 2FA setup/challenge routes

### Key models and relationships

- `Company` → hasMany `Leader`, `Collaborator`, `Scenario`; belongsTo `Admin` (created_by); hasOne `Setting`
- `Leader` → belongsTo `Company`; hasMany `Collaborator` (invited); morphMany `MagicLink`
- `Collaborator` → belongsTo `Company`, `Leader`; hasOne `TrainingSession`; hasMany `Answer`; morphMany `MagicLink`
- `Scenario` — belongsToMany `Company` via pivot `company_scenario` (m2m, desde `206034b`); `is_default=true` marca cenário como catálogo padrão M2 (fallback pra empresas sem vínculos próprios); `demo_eligible` controls demo selection; `target_areas` (JSON) tags audience departments via `Scenario::AREAS` constant. Labels/cores de `platform` e `status` centralizados em constantes `PLATFORM_LABELS/COLORS/STATUS_LABELS/COLORS` no próprio model
- `TrainingSession` — exactly one per collaborator (UNIQUE constraint); aggregates score/duration
- `Answer` — one row per question answered (granular metrics); stores `scenario_version` for snapshot integrity
- `MagicLink` — polymorphic (`tokenable_type` + `tokenable_id`)
- `AuditLog` — written via `AuditLog::record(actor, action, target, payload, ip, ua)` on all sensitive ops

### Modos de plataforma no chat de treinamento (feature nova, ver em `training/show.blade.php`)

Cada cenário tem um campo `platform` (`wapp`, `teams`, `email`, `telegram`, `slack`, `outro`). O chat de treinamento renderiza **visualmente** o cenário como se fosse a plataforma real, ativado via classe `body.platform-{wapp|teams|email|telegram|slack}`. Toda a lógica visual está em `resources/views/training/show.blade.php`. **Ao adicionar uma nova plataforma**, atualizar: (1) enum na migration (guardar SQLite com `if driver === sqlite return`; migration `down()` DEVE migrar linhas com o valor novo pra `'outro'` antes de reduzir o enum — sem isso truncate silencioso); (2) `Scenario::PLATFORM_LABELS` + `PLATFORM_COLORS` no model; (3) tab em `ListScenarios::getTabs()` com ícone Heroicon; (4) arrays em `transition.blade.php` (textos de intro + mascotes) + `index.blade.php` (labels); (5) `@if(in_array(..., ['wapp','teams','email','telegram','slack']))` que ativa a sidebar em `show.blade.php`; (6) CSS `.platform-{nome}` seguindo o padrão dos outros modos; (7) **INCLUIR a nova plataforma nas 3 regras compartilhadas** de `.chat-main`, `.chat-main .chat-area` e `.mascote-fixo` (linhas ~302-323 do `show.blade.php`) — regressão descoberta no clean-code do Slack: esquecer isso deixa mascote flutuante indevido e chat-area sem scroll no desktop.

#### Modo WhatsApp Web (`platform=wapp`)
- Layout 2 colunas: sidebar de conversas + chat principal
- Header verde do WhatsApp (`#075E54`) com avatar redondo + nome do contato + "online"
- Wallpaper bege com padrão de doodles (SVG data-URI)
- Bolhas com **tail (rabinho)** via `clip-path polygon`
- Bolha recebida: branco + tail à esquerda
- Bolha enviada: verde claro `#DCF8C6` + tail à direita
- Timestamp cinza dentro da bolha + checkmarks azuis (`✓✓`)
- Fonte: Segoe UI
- Opções de resposta como **quick-reply verde** empilhadas à direita
- Feedback como "resposta do sistema" (bolha branca com remetente "Guardião Digital" em verde)

#### Modo Microsoft Teams (`platform=teams`)
- Mesma estrutura 2-colunas do WhatsApp Web
- Header verde substituído por **branco** com bordas cinza (`#e1dfdd`)
- Avatares **redondos** (Teams usa circle)
- Item ativo na sidebar: **barra vertical roxa (`#6264A7`)** à esquerda + fundo cinza claro
- Bolhas **sem tail** (cantos arredondados 8px, sombra sutil)
- Bolha recebida: cinza claro `#f5f5f5`
- Bolha enviada: lilás roxo claro `#ebebfa`
- Opções acerto viram roxo Teams `#6264A7`
- Sem checkmarks visuais (Teams não usa `✓✓` publicamente)

#### Modo E-mail (`platform=email`)
- Layout DIFERENTE: sidebar estilo **caixa de entrada Outlook** (azul `#0078d4`) + área principal com **envelope de e-mail estático**
- Envelope contém: assunto grande, barra de metadados (avatar + nome + endereço + data), corpo com parágrafos formatados (não bolhas), botões "Responder / Encaminhar" decorativos
- Mensagens aparecem **INSTANTANEAMENTE** (sem typing indicator, sem delay entre parágrafos) — e-mail não é conversa em tempo real
- Opções de resposta como **botões retangulares empilhados verticalmente** (não quick-reply)
- Assunto/remetente/endereço vêm de campos JSON no `content` (ver abaixo)

#### Modo Telegram (`platform=telegram`)
- Layout 2 colunas (mesma estrutura do WhatsApp Web)
- **Paleta "Telegram Blue vívido" (aplicada em 2026-08-05, commit `dc67186`):** chrome inteiro em `#0088cc` (a cor do logo do Telegram), com header do canal semi-transparente `rgba(0, 136, 204, 0.85)` + `backdrop-filter: blur(10px)` sobre o wallpaper. Iteração inicial usou paleta violeta `#8774E1` (2026-07-31) e depois "Night Blue" `#17212B`, mas Pedro pediu explicitamente o azul do logo (`#0088cc`) — histórico das iterações preservado nos checkpoints `checkpoint-pre-telegram-azul` e `checkpoint-pre-telegram-nightblue`.
- **Bolha "them" (recebidas), question prompt, typing e feedback box:** `#17212B` (Night Blue escuro pra destacar sobre o wallpaper)
- **Bolha "me" (enviadas), chat-item.active, chat-badge, option-btn base, continue-btn base:** `#005F8C` (azul-escuro accent pra contrastar com o chrome vívido)
- **Hover state option-btn:** `#0088cc` (fica vívido, igual chrome)
- **Search bar + hover chat-item + scrollbar:** `#33A1D6` (mais claro pra destacar sobre chrome vívido)
- Wallpaper `/images/telegran.jpg` no `.chat-wrapper` (typo intencional — Pedro nomeou assim)
- Ícones/textos secundários brancos (`#fff` ou `rgba(255,255,255,0.7-0.9)`) pra contraste sobre o chrome azul — inclui `wapp-chat-time`, `wapp-chat-preview`, `s-info-online`, ícones da header/sidebar
- Chat area transparente (wallpaper vem do `.chat-wrapper` por trás), max-width 560px como o wapp
- `body.platform-telegram` sem `background:` próprio — herda o `training-show.jpg` do body
- Mascote intro com fundo `#1c1c1c` sólido + texto `#e8e8e8` (contraste sobre wallpaper escuro)
- Sem `letras A/B/C/D` nas opções do colaborador (mesma remoção cross-platform de 31/07)
- Cenário exemplo `ceo-telegram` só existe no banco **local** (id=28) — pra produção, duplicar via painel admin

#### Modo Slack (`platform=slack`) — **novo em 2026-08-10, commit `ccfb11f`**
- Layout **3 colunas fiéis ao Slack real** (não é mais só cor variando — reestrutura de HTML condicional):
  1. **Nav rail** (68px) em aubergine escuro `#3F0E40` — workspace icon amarelo/laranja no topo, ícones grandes de Home/MDs/Atividade/Mais empilhados, botão `+` e avatar do usuário no bottom. HTML dedicado `<aside class="slack-nav-rail">` renderizado só quando `platform=slack`
  2. **Sidebar de canais** (`.wapp-sidebar`) em **lilás muito claro `#F8F0F6`** com texto escuro — "Acme Inc ▾" no topo (via `::before` do sidebar-header), header "▾ Canais" (via `::before` do search span), cada canal listado como `# nome-do-canal`, canal ativo com fundo roxo escuro `#4A154B` + texto branco
  3. **Chat area branca** com header do canal `# nome ▾` grande em bold + subtitle cinza vindo do `preview` do cenário (`.s-info-sub` reativada só pro slack)
- **Mensagens SEM bolha** — texto direto sobre branco, avatar quadrado 36×36 à esquerda (via `::before` do `.msg` — decorativo, roxo com "M" pro them e azul-verde com "V" pro me), nome+hora hardcoded no CSS (`"Marcelo Andrade 10:07"` / `"Você 10:07"`) via `::before` do `.bubble` — limitação conhecida: nome do sender é fixo (mexer no JS pra passar nome quebraria outras plataformas)
- **Question card** vira "poll message" cinza claro `#F4F4F4` com borda-left roxa `#611F69`, alinhado com o body pós-avatar (`margin-left: 46px`)
- **Opções** como **quick-reply buttons** brancos com borda azul `#1264A3` (Slack interaction style de bots), hover: fundo azul + texto branco
- **Continue-btn** verde Slack `#007A5A`
- **Feedback box** com borda-left cinza (base) / verde (correto) / vermelho (errado)
- **Correto/errado** = `#007A5A` (verde Slack) / `#CD2553` (vermelho Slack)
- **Input decorativo** no rodapé (`<div class="slack-input-bar">`) simulando o compositor Slack: toolbar top com **B I S**, links, listas; placeholder "Mensagem para # canal"; toolbar bottom com `+ 😊 @ Aa 📹 🎤 /` + botão de enviar verde. Não é funcional — só visual
- **Fonte:** Lato (fallback -apple-system, Segoe UI, Roboto)
- Cenário exemplo `ceo-slack` (id=29) só existe no banco **local** — pra produção, duplicar via painel admin
- **PLATFORM_COLORS[slack] = 'danger'** (badge vermelho no admin — diferencia visualmente das outras)

#### Sidebar de conversas — 2 estruturas diferentes

Wapp / Teams / E-mail / Telegram compartilham a estrutura **2 colunas** (sidebar + chat). Slack tem estrutura **3 colunas** própria (`slack-nav-rail` + `wapp-sidebar` + `chat-main`, com `grid-template-columns: 68px minmax(220px, 260px) 1fr`).

Filtra cenários pelo `platform` atual e mostra com 4 estados:
- **`active`** — cenário atual (fundo destacado, não clicável)
- **`completed`** — todas perguntas respondidas → clicável (modo revisão, com checkmark azul `✓✓` no preview)
- **`available`** — em progresso ou próximo pendente → clicável (permite continuar)
- **`locked`** — futuros, ainda não desbloqueados (cadeado `🔒`, não clicável, opacity 55%)

O controller `CollaboratorController::show()` calcula `$reachableScenarioIds` e faz `abort(403)` se o usuário tentar acessar um cenário locked via URL. Helper `completedScenarioIds()` usa 1 query agregada (GROUP BY + COUNT DISTINCT) pra evitar N+1.

#### Campos do cabeçalho de e-mail (só quando `platform=email`)

Editáveis via Filament (Section "Cabeçalho do e-mail" só aparece se `platform=email`). Armazenados dentro do JSON `content`:

- `content.email_from_name` — nome do remetente (fallback: `$scenario->label`)
- `content.email_from_address` — endereço fake do e-mail (fallback: gerado do label)
- `content.email_subject` — assunto do e-mail (fallback: `$scenario->preview`)

Útil pra simular endereços de phishing (ex: `bradesco.empresas.-.e-mail@bradescoempresasemail.com` — domínios estranhos deliberadamente).

### Scenarios JSON structure (`scenarios.content` column)

```json
{
  "messages": [
    { "type": "text", "from": "them", "body": "..." },
    { "type": "question", "prompt": "...", "options": [
      { "key": "a", "text": "...", "correct": true,  "feedback": "..." },
      { "key": "b", "text": "...", "correct": false, "feedback": "..." }
    ]}
  ]
}
```

Email scenarios use the same shape — the multi-step email "bodies" from the legacy m2shield prototype are flattened into sequential `text` messages followed by their question.

### Timestamp default trap (MariaDB strict mode)

When adding `timestamp NOT NULL` columns in migrations, **always chain `->useCurrent()`** (or `->nullable()`). MariaDB strict SQL mode rejects `1067 Invalid default value` otherwise. The 7 original migrations were patched for this — keep the convention. Laravel's `'strict' => true` in `config/database.php` is the canonical setting; don't disable it as a workaround.

### Mass-assignment trap

Several `update()` calls have silently failed in the past because the column wasn't in `$fillable`. When adding new columns via migration, **always** also add them to the corresponding model's `$fillable` (especially `Admin::$fillable` for the brute-force lockout fields and `Collaborator::$fillable` for `completed_at`, `score`, `total_questions`).

### Services (`app/Services/`)

- [`CnpjService`](app/Services/CnpjService.php) — validação de CNPJ (algoritmo dos 2 dígitos verificadores, offline) e consulta à [BrasilAPI](https://brasilapi.com.br) pra trazer razão social. Métodos estáticos:
  - `CnpjService::validate(string $cnpj): bool` — funciona com ou sem máscara
  - `CnpjService::lookup(string $cnpj): ?array` — retorna `['razao_social' => ..., 'nome_fantasia' => ...]` ou `null` se inválido/timeout/não encontrado. Timeout 8s, falhas são logadas em `Log::warning`. Form não trava se API estiver fora — apenas mostra notification e deixa o admin preencher manualmente.

- [`ScoreService`](app/Services/ScoreService.php) — calcula score/postura comportamental individual e corporativa. Lógica centralizada — qualquer view de score consome este service. Métodos públicos:
  - `forCollaborator(Collaborator $c): array` — nível N1-N5 (`<50`/`50-69`/`70-84`/`85-99`/`100`), %, pontos fortes (≥80%), pontos de evolução (<60%), breakdown por categoria, termômetro.
  - `forCompany(Company $c): array` — % geral, classificação corporativa (`Postura Inicial <30`, `Em Evolução 30-49`, `Atenta 50-69`, `Madura 70-99`, `Guardiã 100`), breakdown por categoria, top 3 cenários problemáticos (erro ≥30% com ≥2 respostas), nº de concluintes, termômetro.
  - `buildThermometer(int $pct, string $type): array` — gera estrutura do termômetro gameficado (`'level'` ou `'posture'`). Consumido pelo partial `resources/views/partials/thermometer.blade.php`.

  **Categorização dos cenários:** coluna `scenarios.category` armazena uma das 6 chaves em `Scenario::CATEGORIES`: `validacao_links`, `atencao_remetentes`, `solicitacoes_urgentes`, `compartilhamento_informacoes`, `cuidado_senhas`, `anexos_downloads`. Cenários sem categoria são silenciosamente ignorados no cálculo (não quebra).

  **Onde aparece no produto:**
  - Painel admin (Filament): ação **"Postura"** no `CollaboratorResource` (modal), modal **"Ver Resultados"** ampliado no `CompanyResource`.
  - Painel líder (Blade): seção **"Postura por Categoria"** + **"Cenários com Maior Taxa de Erro"** no dashboard, rota `/lider/colaborador/{id}/postura` (drill-down individual).
  - Partial reutilizável `resources/views/partials/posture-detail.blade.php` compartilhado entre modal admin e drill-down do líder.

### Email (local vs production)

- **Local dev:** `MAIL_MAILER=log` — emails go to `storage/logs/laravel.log`. Queue driver is `database` — run `php artisan queue:work` to actually process the job.
- **Production:** Currently `MAIL_MAILER=log` — SMTP M365 **em progresso de configuração** (2026-07). Domínio `m2guardiao.com.br` já provisionado no M365, falta criar caixa dedicada e habilitar SMTP AUTH. Roteiro completo detalhado em [docs/EMAIL-SMTP-SETUP.md](docs/EMAIL-SMTP-SETUP.md) OU na memória `project-email-smtp-pendente`. Quando ativado, será port 587 STARTTLS. Emails enviados async via Supervisor-managed worker (`deploy/supervisor-worker.conf` — 2 processes running 24/7 in production).

**Config esperada quando SMTP for ativado (edita `.env` da VM):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=<CAIXA>@m2guardiao.com.br
MAIL_PASSWORD=<APP-PASSWORD>       # NÃO commitar — só na VM
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=<CAIXA>@m2guardiao.com.br
MAIL_FROM_NAME="Guardião Digital"
```

Depois de editar `.env`: `sudo -u m2guardian php artisan config:cache && sudo systemctl restart php8.4-fpm`.

### Hardening summary

Configured in `app/Providers/AppServiceProvider.php` (rate limiters + failed-login listener), `app/Http/Middleware/SecurityHeaders.php` (global), and `app/Http/Middleware/AdminTwoFactor.php` (admin-only 2FA gate):

- Rate limiting: `magic-link` 10/min/IP, `admin-login` 5/min/IP, `invite` 20/min/leader
- Brute-force lockout: 5 failed attempts → 15-min lock (admin and leader, separate counters)
- 2FA TOTP via `pragmarx/google2fa-laravel`, secret encrypted (`Admin::$casts`)
- Security headers: X-Frame-Options DENY, HSTS, X-Content-Type-Options, Referrer-Policy, Permissions-Policy
- **CSP in `Content-Security-Policy-Report-Only` mode** — observation period before switching to enforced. Permits `'unsafe-inline'` + `'unsafe-eval'` since Filament/Livewire/Alpine require both.

Production-side: `server_tokens off` in Nginx (hides version), HTTP→HTTPS redirect 301, fail2ban on SSH, Oracle Cloud Security List as 2nd-tier firewall.

## Testing

- **Pest 3** (PHPUnit 11 under the hood)
- `phpunit.xml` sets `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:` — tests do NOT touch the local MariaDB
- Run `php artisan test` from project root
- 22 tests across `MagicLinkTest`, `TrainingFlowTest`, `CompanyCreationTest` (CNPJ + last-leader guard), `AdminBruteForceTest`, `ExampleTest`

If you add tests that need a model, the factory is probably already in `database/factories/` (`AdminFactory`, `CompanyFactory`, `LeaderFactory`, `CollaboratorFactory`, `ScenarioFactory`).

**Migration tip:** Some migrations use raw `DB::statement("ALTER TABLE ... MODIFY COLUMN ... ENUM(...)")` for MySQL-specific schema changes. These check `DB::getDriverName() === 'sqlite'` and skip on SQLite to keep tests working. Follow this pattern when writing similar MySQL-only DDL.

## Production Deployment

Self-contained scripts in `deploy/` (Oracle Cloud Ubuntu 22.04 ARM-compatible):

- `01-server-setup.sh` — provisions PHP 8.4, Nginx, MariaDB, Node 20, Supervisor, Certbot, UFW
- `02-database-setup.sh` — creates DB + user with random password (uses `openssl rand` to avoid `pipefail+SIGPIPE` bug), writes optimized MariaDB config
- `02b-github-deploy-key.sh` — generates SSH deploy key (UNUSED in current production — `M2-Cloud-Dev` org bans deploy keys; we use HTTPS+PAT instead). PAT is stored in `/root/.git-credentials` on the VM (chmod 600) under user `pedrocadev`. Regenerate at https://github.com/settings/tokens → scope `repo`. See `reference_vm_git_auth` memory for the full reset procedure.
- `03-deploy-app.sh` — **idempotent**; on first run does `git init + remote add + fetch + reset --hard` (works in existing non-empty dir, unlike `git clone`); on subsequent runs does `git pull` + composer + npm install + migrations + cache + service restart
- `nginx-http.conf` / `nginx-https.conf` — server configs (Certbot rewrites these to add SSL block)
- `.env.production` — template (NOT in `.gitignore` — committed for reference; real `.env` is per-server)
- `supervisor-worker.conf` — 2 queue workers running 24/7
- `cron-scheduler.txt` — Laravel scheduler every minute + daily 3AM DB backup with 7-day retention
- `DEPLOY.md` — step-by-step walkthrough

**Update flow** (after `git push origin main`):
```bash
ssh ubuntu@137.131.186.168
sudo bash /var/www/m2guardian/deploy/03-deploy-app.sh
```

⏱️ ~30 seconds to 2 minutes depending on whether composer/npm dependencies changed.

⚠️ **Nginx config in production is NOT touched by `03-deploy-app.sh`.** If you change `deploy/nginx-*.conf` and need it in production, you must manually re-copy and reload nginx. Or use `sed` for targeted changes.

## Reference Documents

Toda documentação narrativa fica em [docs/](docs/):

- **[docs/STATUS.md](docs/STATUS.md)** — feature inventory & stack breakdown for leadership/stakeholders
- **[docs/DEPLOY-GUIA.md](docs/DEPLOY-GUIA.md)** — operational playbook for Git → VM deploys (with .docx version for sharing)
- **[docs/HOMOLOG-SETUP.md](docs/HOMOLOG-SETUP.md)** — one-time setup do ambiente de homologação (homolog.m2guardiao.com.br)
- **[docs/DEPLOY-REPORT.md](docs/DEPLOY-REPORT.md)** — full implementation report (Oracle Cloud setup, 9 deployment bugs fixed in repo, hardening applied, backlog, commit timeline)
- **[docs/ENTREGAS-RESUMO.md](docs/ENTREGAS-RESUMO.md)** — sumário de entregas por ciclo (presentation format)
- **`deploy/`** — scripts (`01-server-setup.sh`, `02-database-setup.sh`, `03-deploy-app.sh`, `04-deploy-homolog.sh`) + nginx configs (prod + homolog) + `.env.production`/`.env.homolog` templates
- All planned phases complete (database, auth, invites, training, dashboard, scenario editor, PDF, hardening)
- 13 production-ready scenarios seeded (6 WhatsApp + 4 Teams + 3 Email)

## Ambientes (prod + homolog)

| Item | Produção | Homologação |
|------|----------|-------------|
| URL | m2guardiao.com.br | homolog.m2guardiao.com.br |
| Pasta na VM | `/var/www/m2guardian` | `/var/www/m2guardian-homolog` |
| Branch git | `main` | `develop` |
| Banco | `m2guardian` | `m2guardian_homolog` |
| Deploy script | `03-deploy-app.sh` | `04-deploy-homolog.sh` |
| Workers (queue) | Supervisor (2 processos) | `sync` (executa inline) |
| Indexação Google | Permitida | Bloqueada via `X-Robots-Tag noindex` no nginx |

**Workflow:** push → `develop` → deploy homolog → testar → merge `develop` → `main` → deploy prod. Detalhes em [docs/HOMOLOG-SETUP.md](docs/HOMOLOG-SETUP.md).

## Banco de imagens (`public/images/`)

Convenção de "1 arquivo por uso" — cada referência no código tem sua própria cópia da imagem. Trocar uma imagem específica (ex: mascote do login admin) **não afeta** outros usos (ex: mascote do welcome do colaborador), mesmo que sejam o mesmo arquivo antes.

Estrutura em [public/images/README.md](public/images/README.md):
```
public/images/
├── brand/logo.png                          ← logo unificado (antigo, ainda usado em alguns lugares)
├── mascots/                                ← 21 slots contextuais (1 por lugar)
│   ├── login-admin.png / login-leader.png
│   ├── training-welcome-* (guardian/greeting/explain)
│   ├── training-index-* (start/progress/done)
│   ├── training-show-* (greeting/sidebar/correct/wrong)
│   ├── training-transition-* (wapp/teams/email/fallback)
│   └── completion-n1.png ... completion-n5.png
├── backgrounds/                            ← 1 background por contexto
│   ├── admin-bg.jpg
│   ├── login-leader.jpg
│   ├── Logo_guardiao.png                   ← logo nova padronizada em 2026-06-17
│   └── training-{welcome,index,show,transition,completed}.jpg
```

**Regra:** ao trocar uma imagem, edita o arquivo específico do contexto (nome contextual). Se outra tela precisa da mesma imagem, é cópia separada.

**Logo Guardião nova (2026-06-17):** `backgrounds/Logo_guardiao.png` (nome "backgrounds" é semanticamente errado mas ficou no repo por escolha do Pedro na época — mover pra `brand/` é um refactor futuro). Ver commit `3d3b108`.

**Mascotes redesenhados (2026-06-17):** todos os 21 slots ganharam nova estética unificada (escudo vermelho M2, robô branco/preto/vermelho). Mapeamento por expressão:
- **Pensativo** → análise/intro/aprendiz iniciante
- **Positivo** → acolhimento/sidebar do chat/guardião atento
- **Comemorando** → acerto/conclusão/estratégico/certificado
- **Triste** → erro no chat
- **Correndo** → transição entre cenários/início/guardião

Exceção: `login-admin.png`, `login-leader.png` e `training-welcome-guardian.png` foram **revertidos pro mascote antigo** (corpo inteiro sem moldura circular branca) porque a "bolinha" destoava dos heros escuros dessas telas.

## Estado atual (2026-08-19) — 11 releases grandes deployadas nos últimos 21 dias + feedback de aprendizado por plataforma pendente

Ritmo intenso. **11 releases** já foram pra prod desde 29/07. Working tree tem 1 mudança pendente (feedback de aprendizado por plataforma — modal ao terminar bloco + admin editável, aguardando OK do Pedro pra commitar) + os 4 arquivos de homolog untracked (política de sempre — NÃO commitar).

### Releases deployadas (mais recente primeiro)

| Commit | Data | O que foi |
|--------|------|-----------|
| _(HEAD, ainda não commitado)_ | 19/08 | **Feedback de aprendizado por plataforma** — modal com fundo esmaecido ao terminar TODOS os cenários de uma plataforma (antes da tela de transição ao próximo bloco, ou antes do resultado no último bloco). Nova aba "Feedbacks" no admin Filament pra editar título + corpo de cada plataforma. Tabela `platform_feedbacks` (unique por platform) seeded com 5 registros default. Backend injeta `block_feedback` no payload do `answer()` só nos dois eventos-chave (mudança de bloco / treinamento completo) |
| `b6a5ae7` | 18/08 | **WhatsApp refeito com layout 3 colunas** fiel ao WhatsApp Web moderno (nav rail Conversas ativo / Chamadas / Status / Comunidades / Meta AI / Arquivadas / Configurações + avatar do usuário no bottom) + título "WhatsApp" grande no header da sidebar + **chips de filtro** (Tudo ativo / Não lidas / Grupos / +) + **input decorativo** no rodapé (+ / emoji / "Digite uma mensagem" / mic). Segue exatamente o padrão do Teams e Slack, mas com paleta WhatsApp (`#f0f2f5`, `#25D366`, `#d9fdd3`) |
| `e5bea12` | 18/08 | **Hotfix retry** — "Refazer treinamento do zero" caía na tela "🏆 Treinamento concluído!" em vez da 1ª missão. Causa: timezone drift entre `started_at` de sessions históricas (UTC) e novas (BRT) fazia `latestOfMany('started_at')` retornar a session antiga. Fix: ordena por `id` (auto_increment é imune a timezone/DST). Regra permanente registrada em gotcha (ver seção "⚠️ Gotcha `latestOfMany`") |
| `78a6bd1` / `ac32369` / `c574422` | 18/08 | **Telegram refinado com input decorativo** (clip 📎 + placeholder "Mensagem" + emoji + microfone circular estilo Telegram Web) no rodapé + **botão flutuante "nova mensagem"** no canto inferior direito da sidebar (gradient azul característico do Telegram) |
| `c574422` | 18/08 | **Teams refeito com layout 3 colunas** fiel ao Teams real (nav rail Atividade/Chat/Calendário/Copilot/Chamadas/OneDrive/Aplicativos/Mais + sidebar de chats + chat área) + **banner amarelo "usuário externo"** sticky no topo do chat + **input decorativo** no rodapé; **tag "Externo" só no Teams** (é o único que tem esse marcador oficial na UX real — foi restrita ao Teams em revisão do Pedro no mesmo dia após aparecer em todas as plataformas por engano) |
| `c574422` | 18/08 | **Upload de foto do remetente nos cenários** — campo `avatar_image` (nullable), Filament FileUpload com editor de crop 1:1, thumbnail na tabela do admin, renderiza em 4 lugares (`.s-avatar` header do chat, `.wapp-chat-avatar` sidebar, `.email-avatar` envelope, `.mission-avatar` card da missão). Backward compat: se `avatar_image` vazio, cai no emoji do campo `avatar`. Accessor `Scenario::getAvatarUrlAttribute()` centraliza a lógica |
| `ccfb11f` | 10/08 | **Nova plataforma Slack** com layout de 3 colunas fiel ao Slack real (nav rail + sidebar canais + chat área), mensagens sem bolha com avatar quadrado, input decorativo, paleta oficial Slack + **fix cross-platform** do mascote de feedback (removido do JS — aparecia indevidamente em Telegram/Slack) |
| `dc67186` | 05/08 | **Paleta azul `#0088cc` do Telegram** — chrome inteiro (sidebar, header, chat-wrapper) em Telegram Blue vívido do logo, bolhas them em Night Blue `#17212B`, ícones/textos secundários brancos pra contraste |
| `40dc804` | 05/08 | **Falas do mascote personalizadas por plataforma** na tela de transição entre cenários + botão "Iniciar Missão" + timer 10s |
| `8ea1cfe` | 31/07 | Atualização do CLAUDE.md refletindo estado até 31/07 |
| `61c59dd` | 31/07 | **Nova plataforma Telegram** nos cenários + filtro "Empresa vinculada" na tabela |
| `206034b` | 31/07 | **Cenários vinculados a múltiplas empresas** (pivot m2m + relation manager) |
| `83901b2` | 31/07 | **Randomização das opções de resposta** por sessão (evita gabarito) |
| `0b0cf31` | 30/07 | **Logo oficial em 7 telas** + botões "Responder"/"Encaminhar" decorativos no chat E-mail |
| `1ea56b7` | 29/07 | Wizard editor de cenários + novo layout do e-mail de convite + transição rápida entre chats + 10 correções pós-review dos 5 agentes |

### Feature summary — Feedback de aprendizado por plataforma v2: Guardião + carousel de slides (mudança pendente 2026-08-19)

**Motivação inicial:** Pedro pediu reforço pedagógico entre blocos ("depois de cada finalização de cenario de bloco tipo: terminou de responder todas perguntas do whatsapp, antes de vim a tela de transição para o outro bloco, tem que aparecer um pop na tela atual esmaecendo o fundo com de apredizado"). A **v1** foi um modal com título + corpo único (monolito). Pedro rejeitou: "com a mensagem grande fica um bloco muito fino e tem que descer" → "quero que esse pop seja largo e a letra maior" → depois "quero em paisagem" → finalmente **"no pop final tem que ter o guardião esquerda e abrir tipo um balão de conversa ao lado. tem que ter essa telinha que vai passando pra ver tudo que aprendeu, em vez de ficar só em um monolito gigante"**.

**v2 (esta release):** Layout paisagem com **Guardião body inteiro à esquerda + balão de fala com carousel de slides à direita**. Cada plataforma tem 5 slides (Intro / Sinais de alerta / Regra de ouro / Resumo / Fecho) baseados no roteiro do docx "Card de encerramento". Setas de navegação + contador "X/N" + dots + botão "Continuar" que só aparece no ÚLTIMO slide (evita o colaborador pular sem ler). Cauda triangular do balão apontando pro Guardião (via `::before`).

**Modelagem** (`platform_feedbacks`) — 3 migrations:
1. `2026_08_19_120000_create_platform_feedbacks_table` — cria tabela com `platform` UNIQUE, `title`, `body` (deprecated), timestamps + seed inicial simples
2. `2026_08_19_140000_expand_platform_feedbacks_for_slides` — ADD COLUMN `guardian_image` (nullable) + `slides` (JSON), depois faz UPDATE nos 5 registros com o roteiro do docx (5 slides cada em HTML pronto pro RichEditor)
3. `2026_08_19_160000_make_body_nullable_on_platform_feedbacks` — **fix clean-code**: `body` era `TEXT NOT NULL` sem default e o form v2 não expõe mais o campo. Qualquer INSERT via código (factory, seeder futuro, plataforma nova adicionada ao enum) quebrava com constraint violation em MySQL strict. Migration torna `body` nullable
- `body` mantido pra backward compat (não é mais editado no admin, mas ainda serve de fallback via `normalized_slides` accessor se `slides` estiver vazio)
- Padrão M2 apenas (não por empresa) — se surgir demanda de personalização, dá pra evoluir com pivot

**Model `PlatformFeedback`:**
- `$table = 'platform_feedbacks'` forçado — **gotcha**: `Str::plural('feedback') === 'feedback'` (palavra invariável em inglês, mesmo pluralismo do `equipment`, `series`, etc). Sem o override o Laravel busca em `platform_feedback` e explode com `no such table`
- Cast `slides` → array (JSON)
- `forPlatform(string): ?self` — retorna null se não achar
- Accessor `getGuardianImageUrlAttribute()` — retorna URL pública via `Storage::disk('public')` ou null (front cai no mascote padrão)
- Accessor `getNormalizedSlidesAttribute()` — retorna sempre `[{title, body}, ...]`; se `slides` vazio E `body` presente, converte legado num slide único

**Admin (Filament `PlatformFeedbackResource`):**
- Nav "Feedbacks" com ícone `academic-cap`, sort 5 (logo abaixo de "Cenários" que é sort 4)
- Form v2:
  - Select `platform` (disabled + options `Scenario::PLATFORM_LABELS`)
  - TextInput `title` (título principal do modal, aparece no cabeçalho)
  - **`FileUpload::make('guardian_image')`** com `image()`, `imageEditor()`, resize target 600, disk `public` (`platform-feedbacks/guardians/`)
  - **`Repeater::make('slides')`** com `TextInput::make('title')` + **`RichEditor::make('body')`** — toolbar: bold, italic, strike, h2, h3, bulletList, orderedList, blockquote, link, undo, redo. Repeater é `reorderableWithButtons`, `cloneable`, `collapsible + collapsed`, `itemLabel` mostra o título do slide, `minItems=1`, `defaultItems=1`. Reordena via drag ou botões
- Table: badge com cor `Scenario::PLATFORM_COLORS`, `updated_at` visível, sort default por `platform`
- `canCreate() = false` + `canDelete() = false` — 5 records são fixos, admin só EDITA (não cria/apaga)
- Sem `CreatePlatformFeedback` page — só `List` + `Edit`

**Limitações conhecidas do RichEditor padrão do Filament 3** (Tiptap-based): NÃO tem underline, alignment (esq/dir/centro) nem escolha de fonte. Se surgir demanda, instalar plugin `awcodes/filament-tiptap-editor` (~5 min de composer install). Cobre bem o essencial: negrito, itálico, tachado, listas, títulos (h2/h3), link, blockquote.

**Backend (`CollaboratorController::answer()`):**
- Novo campo `block_feedback` no payload de resposta (via helper privado `platformFeedbackPayload(string): ?array`)
- Injeta o feedback **apenas em 2 pontos**:
  1. `$allScenariosDone = true` (treinamento inteiro acabou) — usa feedback do cenário atual (o último do último bloco)
  2. `$next->platform !== $scenario->platform` (próximo cenário é de outra plataforma) — usa feedback do cenário atual (o que ele acabou de fechar)
- NÃO injeta quando `quick_transition = true` (mesma plataforma, próximo cenário) — aí o usuário só troca de chat na mesma plataforma, não fecha bloco

**Frontend (`show.blade.php`) — layout paisagem carousel:**
- HTML: `<div class="block-feedback-modal">` fixed com backdrop + card em grid 2 colunas. **Coluna esquerda** = `<div class="bfm-hero">` com `<img class="bfm-guardian">` (imagem body inteira flutuando via animation `bfmFloat`). **Coluna direita** = `<div class="bfm-balloon">` (fundo branco, `border-radius: 20px`) com header (título + contador "X/N"), corpo do slide atual (title + rich HTML), nav (setas prev/next + dots) e botão Continuar hidden até o último slide.
- CSS: cauda triangular do balão apontando pro Guardião via `.bfm-balloon::before` (border trick + `drop-shadow` sutil). Setas circulares `.bfm-arrow` com borda vermelha `#CC0000` que preenche no hover. Dots `.bfm-dot` com scale no ativo. Estilos ricos pro `.bfm-slide-body` cobrem `<ul>/<ol>/<blockquote>/<strong>/<em>/<h2>/<h3>/<a>` (o RichEditor salva HTML — não é markdown). Backdrop com `backdrop-filter: blur(5px)`.
- CSS responsivo: 3 níveis. `≤900px` reduz Guardião pra 200px. `≤640px` colapsa pra 1 coluna vertical (Guardião em cima, sem cauda).
- JS: `showBlockFeedbackModal(payload, onContinue)` — recebe `{title, guardian_image_url, slides: [{title, body}]}`. Carrega Guardião custom (com fallback `onerror` → default), popula dots dinamicamente, `render(idx)` alterna slide + habilita/desabilita setas + toggle do botão Continuar. `slideBodyEl.innerHTML = slide.body` (autor confiável = admin autenticado, sem XSS risk). Scroll do balão volta pro topo em cada slide.
- Botão Continuar SÓ aparece no ÚLTIMO slide (`idx === total - 1`). Antes disso o colaborador é forçado a navegar pelas setas — pedagógico intencional.

**Testes** (`tests/Feature/PlatformFeedbackTest.php`, 4):
1. `block_feedback` é injetado quando termina TODOS de uma plataforma e próxima é diferente (assere estrutura `{platform, title, slides:[{title, body}]}`) ✓
2. `block_feedback` **NÃO** é injetado quando próximo cenário é da mesma plataforma (quick_transition) ✓
3. `block_feedback` é injetado no último cenário (training complete) ✓
4. Backward-compat: registro sem `slides` mas com `body` retorna `[{title:null, body:legado}]` via `normalized_slides` ✓

**Padrão consistente:** todos os elementos decorativos do modal (backdrop) não interferem em navegação de teclado — foco vai direto pro botão "Continuar" quando abre. `aria-modal="true"` no card indica ao leitor de tela que conteúdo atrás está inativo.

### ⚠️ Gotcha `latestOfMany` — nunca ordenar sessions por `started_at` (hotfix 2026-08-18)

**Bug reproduzido em prod:** Lucas Pedrosa reprovou o treinamento → clicou em "Refazer treinamento do zero" → redirecionou pra `/treinamento` (correto) mas viu tela "🏆 Treinamento concluído!" em vez da primeira missão. `completed_at` do collaborator estava correto (null), retry rodou até o fim, sessão nova criada — mas o `index()` calculou `$nextScenario = null` e caiu no `@else` da view.

**Causa raiz:** o Model `Collaborator` usava `hasOne(TrainingSession::class)->latestOfMany('started_at')` pra apontar pra "tentativa atual". Em prod, uma session histórica do Lucas tinha `started_at` gravado em **UTC** (19:41:20), enquanto a session nova criada pelo retry ficou em **BRT** (16:41:28). O `latestOfMany('started_at')` compara strings/timestamps crus e concluiu que **`19:41 > 16:41`** → retornou a session ANTIGA. Aí `completedScenarioIds()` contou os 39 answers da session antiga → todos os cenários "concluídos" → `$nextScenario = null` → tela concluído.

**Fix aplicado:** trocar a ordenação de `started_at` → `id` (auto_increment é sempre monotônico, imune a timezone drift):

```diff
- return $this->hasMany(TrainingSession::class)->latest('started_at');
+ return $this->hasMany(TrainingSession::class)->latest('id');

- return $this->hasOne(TrainingSession::class)->latestOfMany('started_at');
+ return $this->hasOne(TrainingSession::class)->latestOfMany('id');
```

Mesmo tratamento em `CollaboratorController::retry()` (`->latest('id')` no lugar de `->latest('started_at')`).

**Regra permanente:** ao ordenar registros de um mesmo collaborator/entidade por "mais recente", **sempre** ordenar por `id` quando possível — datetimes são vulneráveis a timezone drift, DST, sync de relógio, e importações de dados históricos com timestamps normalizados. `id` (auto_increment) é a única garantia de monotonicidade em MySQL/MariaDB.

**Teste de regressão:** `tests/Feature/RetryTest.php` reproduz o cenário exato (session antiga com `started_at` 3h à frente da nova, simulando UTC vs BRT) e assere `$nextScenario !== null` no index após retry. Antes do fix o teste falha; com o fix passa.

### Feature summary — WhatsApp refeito 3 colunas (mudança pendente 2026-08-18)

**Motivação:** Pedro mandou screenshot do WhatsApp Web moderno ("agora quero que você faça a mesma coisa para o cenário do whatsapp. deixe com mais cara, com os ícones e formatos"). O layout de 2 colunas antigo estava fora de padrão em relação ao Teams e Slack (que agora têm 3 colunas com nav-rail). Refatoração pra deixar o WhatsApp visualmente coerente com o padrão e fiel ao WhatsApp Web real.

**HTML novo** (só quando `platform=wapp`):
- `<aside class="wapp-nav-rail">` antes da `.wapp-sidebar` — coluna 1 estreita com 5 nav items no topo (Conversas ativo / Chamadas / Status / Comunidades / Meta AI com gradient text) + spacer + 2 nav items no bottom (Arquivadas / Configurações) + avatar do usuário. Item ativo tem barra verde `#25D366` vertical à esquerda.
- Header condicional novo: `@elseif($scenario->platform === 'wapp')` mostra `<div class="wapp-brand-title">WhatsApp</div>` (o avatar do usuário saiu do header e foi pra nav-rail, coerente com o WhatsApp Web real). Ícones do header: nova conversa + menu kebab.
- `<div class="wapp-filter-chips">` depois do `.wapp-search` — 4 chips decorativos: Tudo (ativo, fundo verde-claro `#d9fdd3` + texto `#027a48`) / Não lidas / Grupos / +
- `<div class="wapp-input-bar">` depois do `.bottom-spacer` — barra do rodapé com ícone `+` (anexar) + ícone emoji + `.wapp-input-box` (fundo branco arredondado com "Digite uma mensagem") + ícone mic

**CSS `.platform-wapp`** (mudanças no bloco existente):
- `chat-wrapper { grid-template-columns: 68px minmax(320px, 380px) 1fr }` — antes era `minmax(320px, 380px) 1fr` (2 colunas)
- Novo bloco `.wapp-nav-rail` + `.wapp-nav-item` + `.active` (com `::before` verde) + `.meta-ai` (gradient text) + `.wapp-nav-spacer` + `.wapp-nav-avatar`
- Novo `.wapp-brand-title` (fonte 20px peso 500)
- Novo bloco `.wapp-filter-chips` + `.wapp-filter-chip` + `.active` (verde WhatsApp) + `.chip-more`
- Novo bloco `.wapp-input-bar` + `.wapp-input-box` (paleta neutra WhatsApp `#f0f2f5`)
- Media query `max-width: 900px` esconde nav-rail junto com sidebar

**Padrão consistente:** todos os elementos decorativos usam `aria-hidden="true"` + `tabindex="-1"` (não interferem em navegação por teclado dos elementos funcionais). Mesmo padrão do Teams (18/08) e Slack (10/08).

**Backward compat:** o header antigo do WhatsApp (avatar do usuário à esquerda) foi movido pra nav-rail; o `.wapp-user-avatar` continua sendo usado por Telegram e Slack no `@else` do header condicional.

**Aguardando:** validação visual do Pedro antes de commitar.

### Feature summary — Telegram refinado com input decorativo + FAB (deploy 2026-08-18)

**Motivação:** Pedro mandou screenshot do Telegram Web real ("faça a mesma alteração para o telegran"). Diferente do Teams/Slack, Telegram Web é 2 colunas (não 3) — então não faz sentido nav rail. O foco foi trazer os elementos característicos que faltavam.

**HTML novo** (só quando `platform=telegram`):
- `<button class="telegram-new-message-fab">` dentro do `<aside class="wapp-sidebar">` (posicionado absolute no canto inferior direito) — botão circular azul com ícone de lápis, característico do Telegram Web
- `<div class="telegram-input-bar">` depois do `.bottom-spacer` — barra de composição no rodapé com clip 📎 (anexar) + `.telegram-input-box` (fundo semi-transparente com placeholder "Mensagem" + emoji) + botão microfone circular azul

**CSS `.platform-telegram`**:
- `.wapp-sidebar { position: relative }` — pra ancorar o FAB
- Bloco `.telegram-new-message-fab` — 54×54 redondo, gradiente `#33A1D6 → #0088cc`, sombra azul
- Bloco `.telegram-input-bar` — flexbox horizontal, fundo `rgba(15,15,15,0.85)` com blur
- `.telegram-input-mic` também gradiente com sombra (destaca como botão principal)
- Media query `max-width: 700px` esconde o FAB em mobile

**Mantido:** paleta `#0088cc` (Telegram Blue do logo) — cores validadas por Pedro em 05/08 (commit `dc67186`) não foram tocadas. Só adição de novos elementos.

### Feature summary — Teams refeito 3 colunas + tag "Externo" universal (deploy 2026-08-18)

**Motivação:** Pedro mandou screenshot do Teams real ("mais com a cara do Teams, com os icones nas laterais"). Iteração anterior era 2 colunas (`sidebar + chat`) com paleta roxa — servia mas não parecia Teams. Agora tem estrutura autêntica de 3 colunas, seguindo o mesmo padrão que foi feito pro Slack em 10/08.

**HTML novo** (só quando `platform=teams`):
- `<aside class="teams-nav-rail">` antes da `.wapp-sidebar` — coluna 1 estreita com 8 nav items (Atividade / Chat ativo / Calendário / Copilot / Chamadas / OneDrive / Aplicativos / Mais). Cada item é `<button>` com SVG grande + label pequeno embaixo. Item ativo tem barra roxa vertical à esquerda + cor `#6264A7`.
- `<div class="teams-external-banner">` no início da `.chat-area` — banner amarelo `#fff4ce` com borda-left `#f9d65e`, ícone de warning, texto "**{label}** faz parte de uma organização externa. É possível que haja políticas relacionadas às mensagens que serão aplicadas ao chat." + link "Saiba mais" + botão × decorativo
- `<div class="teams-input-bar">` depois do `.bottom-spacer` — input decorativo Teams no rodapé com hint "Responder a participantes externos.", placeholder "Digite uma mensagem" e ícones (Aa / 😊 / GIF / 📎 / ⋯ / send)

**CSS `.platform-teams`** (mudanças no bloco existente):
- `chat-wrapper { grid-template-columns: 68px minmax(280px, 320px) 1fr }` — antes era `minmax(300px, 340px) 1fr` (2 colunas)
- Novo bloco `.teams-nav-rail` + `.teams-nav-item` + estados active/hover
- Novo bloco `.teams-external-banner` (amarelo, warning icon, link Saiba mais)
- Novo bloco `.teams-input-bar` + subitens (hint / box / placeholder / actions / send-btn)

**Tag "Externo" — SÓ no Teams** (a UX real desse marcador é exclusiva do Teams):
- HTML no `.scenario-bar` universal: `<div class="s-info-label-row">` wrap com `<span class="s-info-label">` + `@if($scenario->platform === 'teams')` envolvendo `<span class="external-tag">Externo</span>`
- Cor: `#e8ebfa` bg + `#6264A7` fg (cor real do Teams em contato externo)
- **Nota histórica**: primeiro build (mesma manhã de 18/08) aplicou a tag universalmente em todas as 5 plataformas com overrides de cor por paleta, mas Pedro pediu restrição — WhatsApp/Email/Telegram/Slack no mundo real NÃO têm esse marcador de "Externo" no header. Restrito ao Teams pra ficar fiel. Os overrides de cor das outras plataformas foram removidos como código morto.

### ⚠️ Gotcha Nginx — `/storage/` fora da blocklist (fix 2026-08-18)

Deploy inicial da feature "Upload de foto do remetente" caiu num 403 em prod: uploads apareciam como placeholder quebrado no `/admin/scenarios` mesmo com symlink, permissões e `APP_URL` corretos. Causa: a config Nginx em prod (baseada no template `deploy/nginx-https.conf` original) tinha esta regra:

```nginx
location ~ /(storage|bootstrap|database|tests|deploy|config|app|routes)/ {
    deny all;
}
```

O `storage|` na blocklist bloqueava a URL pública `/storage/...` — que é o **symlink oficial do Laravel** pra `storage/app/public/` (uploads via `disk('public')`). As outras pastas na lista (`bootstrap/`, `database/`, etc) ficam FORA do document root `/public`, então o `deny all` nelas é cinto-e-suspensórios cosmético. Só `storage` estava realmente ativa e quebrando o feature novo.

**Fix aplicado em prod** (via `sudo sed -i` no `/etc/nginx/sites-enabled/m2guardian` + `sudo systemctl reload nginx`):
```nginx
# storage sai da lista
location ~ /(bootstrap|database|tests|deploy|config|app|routes)/ {
    deny all;
}
```

**Fix aplicado no repo** (`deploy/nginx-http.conf` + `deploy/nginx-https.conf` — 2 blocos no HTTPS pra HTTP redirect + HTTPS main) — próximas VMs/homolog não vão sofrer disso.

**Ao adicionar novos disks públicos no futuro**, sempre verificar se o path público NÃO está na blocklist do Nginx. O `03-deploy-app.sh` NÃO reescreve Nginx config — a mudança precisa ser aplicada manualmente em prod (já está feito 18/08).

### Feature summary — Upload de foto do remetente (deploy 2026-08-18)

Cenários ganharam suporte a **foto real de pessoa** no lugar do emoji, pra deixar o chat mais crível ("igual redes sociais mesmo" — Pedro). Backward-compat total: os 13 cenários catálogo com emoji continuam funcionando enquanto Pedro não substitui um a um.

**Migration `2026_08_12_150000_add_avatar_image_to_scenarios_table`:** adiciona `scenarios.avatar_image` VARCHAR(255) nullable, path relativo do disco `public` (ex: `scenarios/avatars/abc123.jpg`).

**Model `Scenario`:** novo accessor `getAvatarUrlAttribute()` retorna URL pública se tiver upload, ou `null` (views caem no emoji). Constrói via `Storage::disk('public')->url($this->avatar_image)`.

**Form Filament (`ScenarioResource`):** `FileUpload::make('avatar_image')` com image editor embutido (crop 1:1 obrigatório), resize automático pra 400x400, `disk('public')` + `directory('scenarios/avatars')`, `maxSize 2048` (2MB). Emoji virou "fallback se sem foto" com hint atualizado. Tabela do admin ganhou `ImageColumn::make('avatar_image')` circular 40px como primeira coluna.

**Views (4 lugares — `show.blade.php` × 3 + `index.blade.php` × 1):** padrão condicional idêntico em todos:
```blade
@if($scenario->avatar_url)
    <img src="{{ $scenario->avatar_url }}" alt="{{ $scenario->label }}">
@else
    {{ $scenario->avatar }}
@endif
```

**CSS:** regras genéricas pra `img` dentro de `.s-avatar / .wapp-chat-avatar / .email-avatar / .mission-avatar` — `width/height: 100%; object-fit: cover; border-radius: inherit`. Container ganha `padding: 0; font-size: 0; overflow: hidden` via `:has(img)` pra zerar o padding fantasma que o emoji deixaria.

**Storage:** o script `deploy/03-deploy-app.sh` já roda `php artisan storage:link` (linha 85), então em prod nada muda no deploy. Local precisou rodar `storage:link` uma vez pra criar o symlink `public/storage → storage/app/public`.

**Slack:** `.wapp-chat-avatar { display: none }` continua — Slack usa `#` de canal (não avatar de pessoa) na sidebar. Header do canal já não tinha avatar. Foto do remetente Slack fica no `.s-avatar` (que está escondido também no Slack, coerente com o design real onde o topo do canal não mostra avatar de sender).

### Feature summary — Plataforma Slack (5ª plataforma, deploy 2026-08-10)

**A primeira plataforma que foi REESTRUTURADA em vez de só variar cor.** Iteração inicial (também no dia 10/08, várias horas antes) fez só troca de paleta sobre o layout wapp/teams/telegram — Pedro reclamou explicitamente "vc só ta duplicando cenarios e mudando a cor". Fizemos v2 com layout de 3 colunas de verdade.

**HTML novo** (só quando `platform=slack`):
- `<aside class="slack-nav-rail">` antes da `.wapp-sidebar` — coluna 1 estreita com workspace icon + 4 nav items + botão `+` + avatar do usuário
- `<div class="slack-input-bar">` depois do `.bottom-spacer` — input decorativo simulando o compositor Slack (toolbar top + placeholder + toolbar bottom + botão send verde). Não funcional

**CSS `.platform-slack`** (~350 linhas em `show.blade.php`, blocos 1696–2033):
- `chat-wrapper { grid-template-columns: 68px minmax(220px, 260px) 1fr }` — 3 colunas
- Sidebar de canais em lilás claro `#F8F0F6` (não escuro como as outras plataformas)
- Mensagens 100% sem bolha (texto puro sobre branco) + avatar quadrado via `::before`

**Trade-off aceito** (documentado no CSS como limitação): nome do sender + hora hardcoded no `::before` do `.bubble` (`"Marcelo Andrade 10:07"` / `"Você 10:07"`). Mexer no JS pra passar sender/hora renderizaria dinamicamente mas quebraria compatibilidade com as outras plataformas — trade-off pra não introduzir divergência estrutural.

**Fix cross-platform que veio junto:** o JS forçava `mascotWrap.style.display = 'flex'` no card de feedback após responder — sobrescrevia o `display: none` do CSS onde não tinha `!important` (Telegram e Slack). Mascote aparecia indevidamente. Removidas as 2 linhas do JS que forçavam o display — agora o CSS `display: none` global vence sempre. **Mascote grande de feedback nunca mais aparece em plataforma nenhuma.**

**Bloco `body.platform-slack` incluído nas 3 regras compartilhadas** de `.chat-main`, `.chat-main .chat-area` e `.mascote-fixo` (linhas ~302-323 do show.blade.php) — regressão descoberta pelo clean-code-reviewer: esquecer isso deixava o mascote flutuante no canto direito e chat-area sem scroll no desktop.

### Feature summary — Paleta azul do Telegram (deploy 2026-08-05)

Cor do chrome + accent do Telegram foi refatorada 3x pra chegar no final:
1. **Iteração 1** (`61c59dd`, 31/07): paleta violeta `#8774E1` (branding moderno do Telegram)
2. **Iteração 2** (parte do refactor de 05/08): Night Blue `#17212B` (tema dark padrão do Telegram Web/Desktop atual). Pedro achou "muito escuro, parece preto"
3. **Iteração 3 final** (`dc67186`, 05/08): `#0088cc` — o azul do LOGO do Telegram, aplicado no chrome inteiro (sidebar, header, chat-wrapper). Bolhas them em Night Blue pra contraste; accent em `#005F8C` pra destacar sobre o chrome vívido; textos/ícones secundários em branco pra contraste sobre o chrome azul brilhante

Iterações intermediárias preservadas em `checkpoint-pre-telegram-azul` e `checkpoint-pre-telegram-nightblue` (ambos apontam pro commit `40dc804`, criadas pela pontualmente antes das mudanças de paleta).

### Feature summary — Falas do mascote personalizadas + botão Iniciar Missão (deploy 2026-08-05)

Substituiu o texto genérico da tela de transição ("Agora é hora do WhatsApp" + "Comunicação corporativa também pode esconder armadilhas" + botão "Bora encarar →") por:

- **Título dinâmico por plataforma** com ordinal derivado de `$position` (1→"primeira", 2→"segunda", 3→"terceira", ...)
- **Nome do colaborador** no texto do Teams: `"Pronto, {firstName}? Sua {ordinal} missão chegou..."`
- **Textos específicos por plataforma** (Wapp foco em mensagens rápidas, Teams em ambiente corporativo, E-mail em remetente/domínio/anexos, Telegram genérico)
- **Botão "Iniciar Missão →"** (era "Bora encarar →")
- **Timer auto-avanço subiu de 5s para 10s** (mais tempo pra ler)

Estrutura vive no bloco `@php` de `resources/views/training/transition.blade.php` (array `$platforms` com `icon/title/text`). Se a empresa tiver ordem/quantidade diferente de cenários (só Teams+Email por exemplo), o ordinal acompanha automaticamente.

### Feature summary — Cenários vinculados a múltiplas empresas (m2m)

Antes `scenarios.company_id` era belongs-to. Agora é **many-to-many** via pivot `company_scenario`.

**Regra nova de visibilidade** em [`CollaboratorController::getScenariosFor()`](app/Http/Controllers/CollaboratorController.php):
- Empresa **com** cenários vinculados via pivot → colaborador vê **APENAS** os vinculados (isolamento)
- Empresa **sem** vínculos → colaborador vê **APENAS** os `is_default=true` (fallback M2)
- Demo continua sendo 3 cenários `is_default+demo_eligible`

**Onde gerenciar** (Filament):
- `/admin/scenarios/{id}/edit` — passo 2 do wizard tem multi-select "Empresas vinculadas"
- `/admin/companies/{id}/edit` — aba "Cenários vinculados" via `ScenariosRelationManager` (attach/detach, filtro platform, action "Abrir" leva pro editor)
- `/admin/scenarios` — filtro "Empresa vinculada" na tabela

Migration `2026_07_31_120000_create_company_scenario_pivot` migrou dados de `company_id` pro pivot e dropou a coluna. É **defensiva** com try/catch pra FK/unique (trabalha em MySQL local, MySQL prod e SQLite dos testes).

### Feature summary — Randomização de opções por sessão

Em `CollaboratorController::show()`, método privado `shuffleQuestionOptions()` embaralha o array `options` de cada question do `content.messages` **em memória** (sem persistir).

Seed: `crc32("t{session_id}-s{scenario_id}-q{question_index}")` — **determinística**:
- Mesmo colaborador em reload/logout+login = mesma ordem
- Colaboradores diferentes = ordens diferentes (evita gabarito)
- Retry (nova TrainingSession) = ordem nova (evita decorar posição)

Chave (`a/b/c/d`) fica constante, backend continua identificando pela `key` (não pela posição). Modo revisão funciona com a nova ordem.

### Feature summary — Plataforma Telegram

Enum `scenarios.platform` agora inclui `telegram`. CSS `.platform-telegram` em `resources/views/training/show.blade.php` (~230 linhas): tema dark violeta com wallpaper em `public/images/telegran.jpg` no `.chat-wrapper`, sidebar opaca escura, header semi-transparente com backdrop-filter blur, bolhas violetas à direita, chat area transparente pra deixar o wallpaper passar. Cenário exemplo `ceo-telegram` (id=28) foi criado no banco **local** via script tinker — em prod precisa duplicar via painel.

Também rolou uma limpa: as letras **A/B/C/D** das opções não aparecem mais pro colaborador (a `<span class="option-key">` foi removida do JS de renderização). As letras continuam sendo mostradas apenas no `itemLabel` do repeater no admin, pra organização interna. As 4 regras CSS `.option-key` órfãs (base + wapp + teams + email) foram removidas.

### Feature summary — Correções pós-review (deploy 2026-07-29)

10 correções aplicadas: `filters()` duplicado do LeaderResource, session fixation no MagicLinkController, SMTP leak no LeaderInviteController, guard `score === null` no ScoreService, `sessionIds` do LeaderController filtra `completed_at`, `send_credentials` envia antes de rotar senha, dead code removido, encrypt/decrypt na session do admin, `retry` com `lockForUpdate` serializando double-clicks.

### Cenários no banco (prod + local)

Todos os cenários da plataforma **WhatsApp** foram atualizados manualmente pelo Pedro conforme o documento enviado (mensagens, perguntas, respostas e feedbacks revisados via painel admin).

**Untracked** (homolog adiado — NÃO commitar):
```
?? deploy/.env.homolog
?? deploy/04-deploy-homolog.sh
?? deploy/nginx-homolog.conf
?? docs/HOMOLOG-SETUP.md
```

**Testes:** 28/28 verde (excluindo o `MagicLinkTest` desatualizado — teste espera redirect `/treinamento`, real vai `/treinamento/intro`; não é bug, está no backlog).

## SMTP M365 — configurado e funcionando (2026-07-10)

Envio real de e-mails funcionando em prod via `noreply@m2guardiao.com.br`. **Gotcha importante:** o erro `535 5.7.139 Authentication unsuccessful, the request did not meet the criteria` **não é** SMTP AUTH desabilitado — é **Conditional Access Policy** "Block legacy authentication" do Entra ID bloqueando. Solução: adicionar exceção da conta noreply na CA policy (portal Entra → Proteção → Acesso Condicional → Políticas → editar → Identidades excluídas). `Set-CASMailbox -SmtpClientAuthenticationDisabled $false` sozinho **não** sobrescreve CA policies.

Sequência de camadas de bloqueio SMTP no M365 (ordem: mais externa → mais interna):
1. **Conditional Access Policies (Entra ID)** ← sobrescreve tudo (foi o culpado)
2. **Security Defaults (Entra ID)**
3. **`Set-TransportConfig -SmtpClientAuthenticationDisabled`** (tenant global)
4. **`Set-CASMailbox -SmtpClientAuthenticationDisabled`** (mailbox individual, override)
5. **Checkbox "SMTP autenticado" no Admin Center** (mesma coisa que #4, UI)

Debug futuro: sempre checar **Message Trace no Exchange Admin Center** (https://admin.exchange.microsoft.com) — se aparecerem entradas, o M365 recebeu (problema é filtro do destinatário); se aparecer vazio, o M365 nunca recebeu (problema é auth ou CA policy).

## Known Backlog (post-launch)

| # | Item | Priority | Status |
|---|------|----------|--------|
| 1 | ~~Configure SMTP M365~~ | ~~High~~ | ✅ **Resolvido 2026-07-10** — ver seção "SMTP M365" acima |
| 2 | ~~Correções pós-review dos 5 agentes~~ | ~~High~~ | ✅ **Deployed 2026-07-29** no commit `1ea56b7` |
| 3 | ~~Feature m2m de cenários × empresas~~ | ~~High~~ | ✅ **Deployed 2026-07-31** no commit `206034b` |
| 4 | ~~Randomização das opções por sessão~~ | ~~Medium~~ | ✅ **Deployed 2026-07-31** no commit `83901b2` |
| 5 | ~~Plataforma Telegram~~ | ~~Medium~~ | ✅ **Deployed 2026-07-31** no commit `61c59dd`; paleta finalizada em `dc67186` (05/08) |
| 6 | ~~Plataforma Slack~~ | ~~High~~ | ✅ **Deployed 2026-08-10** no commit `ccfb11f` — layout 3 colunas fiel ao Slack real |
| 6.1 | **Falta 1 plataforma nova** — Pedro pediu +3 em 2026-07-31 além de wapp/teams/email; feitas Telegram + Slack (2 de 3), falta uma 3ª a definir | High | Próximo grande |
| 7 | Configurar SPF + DKIM no DNS de `m2guardiao.com.br` | High | Backlog — melhora deliverability, evita spam. Primeiros envios podem cair em spam sem esses |
| 8 | Enable 2FA TOTP no super admin | High | Backlog |
| 9 | Ativar homologação (`homolog.m2guardiao.com.br`) — 4 arquivos untracked prontos | Medium | Adiado por escolha do Pedro |
| 10 | Adicionar cenário `ceo-telegram` (ou similar) no `ScenarioSeeder` — hoje o exemplo Telegram só existe no banco local (id=28), em prod precisa duplicar via painel | Medium | Se quiser catálogo padrão M2 de Telegram |
| 10.1 | Adicionar cenário `ceo-slack` no `ScenarioSeeder` — hoje id=29 local só, em prod precisa duplicar via painel | Medium | Idem Telegram |
| 10.2 | **`package-lock.json` não está no repo** (nem tracked, nem no `.gitignore`) — junior descobriu no pull de 10/08. Padrão Node é ter esse arquivo commitado pra determinismo entre máquinas. Decidir: commitar (fixa versões) ou colocar no `.gitignore` (assume que qualquer install gera OK) | Medium | Débito técnico real, aparece como untracked em qualquer `npm install` fresh |
| 10.3 | Nome do sender + hora hardcoded no CSS do `.platform-slack` (`.msg.them .bubble::before = "Marcelo Andrade 10:07"`). Se quiser dinâmico por cenário, mexer no JS de renderização (`addBubble`) pra aceitar `sender_name` + `time` e adicionar essas keys ao JSON de `content.messages` — implicaria alterar também renderers das outras plataformas ou usar campo opcional só pro Slack | Low | Trade-off aceito na v2 do Slack |
| 11 | Investigar bug intermitente do dropdown de logout (menu Filament, possivelmente cache) | Medium | Backlog |
| 12 | Migrar CSP `Report-Only` → enforced após observação | Medium | Backlog |
| 13 | VM Oracle Cloud (137.131.186.168, ARM Ampere Ubuntu 22.04): 50+ updates pendentes + kernel novo pendente. Rodar `apt upgrade` + reboot em janela de manutenção | Medium | Diagnóstico rodado 2026-07-30, VM subutilizada (17% RAM, 6% disco, load 0.00) — não precisa upgrade de máquina |
| 14 | Corrigir teste `MagicLinkTest` desatualizado (espera redirect `/treinamento`, real vai `/treinamento/intro`) | Low | 1 linha de fix |
| 15 | Housekeeping DB: empresa "Empresa Teste Slug 527" sem uso, tokens magic_link expirados 30d+, sessions abandonadas | Low | db-health-checker reportou (nenhum urgente) |
| 16 | Extrair `<x-brand-logo>` como componente Blade (elimina duplicação em 5+ views) | Low | Refactor de branding |
| 17 | Extrair sub-partial dos blocos brand desktop/mobile em `auth-layout.blade.php` | Low | Refactor de branding |
| 18 | Move `Logo_guardiao.png` de `backgrounds/` pra `brand/` | Low | Cosmético |
| 19 | Visual refinements with marketing team | Low | Backlog |
| 20 | LGPD legal copy (privacy policy + consent) | Low | Backlog |
| 21 | Upgrade Nginx 1.18 → 1.24+ in maintenance window | Low | Backlog |
| 22 | `/var/log/journal` na VM tem 1.2 GB — rodar `journalctl --vacuum-time=14d` + setar `SystemMaxUse=500M` | Low | Diagnóstico rodado 2026-07-30 |
| 23 | Considerar adicionar swap de 2 GB na VM (opcional — safety net contra OOM) | Low | VM sem swap hoje, mas RAM sobra |
