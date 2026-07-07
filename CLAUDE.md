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
- `Scenario` — `company_id = NULL` means default M2 template; `demo_eligible` controls demo selection; `target_areas` (JSON) tags audience departments via `Scenario::AREAS` constant
- `TrainingSession` — exactly one per collaborator (UNIQUE constraint); aggregates score/duration
- `Answer` — one row per question answered (granular metrics); stores `scenario_version` for snapshot integrity
- `MagicLink` — polymorphic (`tokenable_type` + `tokenable_id`)
- `AuditLog` — written via `AuditLog::record(actor, action, target, payload, ip, ua)` on all sensitive ops

### Modos de plataforma no chat de treinamento (feature nova, ver em `training/show.blade.php`)

Cada cenário tem um campo `platform` (`wapp`, `teams`, `email`, `outro`). O chat de treinamento renderiza **visualmente** o cenário como se fosse a plataforma real, ativado via classe `body.platform-{wapp|teams|email}`. Toda a lógica visual está em `resources/views/training/show.blade.php`.

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

#### Sidebar de conversas (compartilhada wapp+teams+email)

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

## Estado atual (2026-07-07) — trabalho pendente de commit

⚠️ **IMPORTANTE PRO PRÓXIMO DEV:** o working tree tem **1300+ linhas não commitadas** em 5 arquivos. Antes de fazer alterações grandes, revise e commite ou faça `git stash` do que já está lá pra não perder trabalho.

**Arquivos modificados:**
```
M  app/Filament/Resources/ScenarioResource.php
M  app/Filament/Resources/ScenarioResource/Pages/CreateScenario.php
M  app/Filament/Resources/ScenarioResource/Pages/EditScenario.php
M  app/Http/Controllers/CollaboratorController.php
M  resources/views/training/show.blade.php
```

**Trabalho pendente contém 2 blocos lógicos separáveis:**

**Bloco 1 — Cenários imersivos por plataforma no chat** (`CollaboratorController.php` + `show.blade.php`):
- 3 modos de renderização (WhatsApp Web / Teams / E-mail) ativados via `body.platform-*`
- Sidebar de conversas + 4 estados de cenário (active/completed/available/locked)
- Helper `completedScenarioIds()` eliminando N+1
- `abort(403)` contra acesso via URL a cenários locked
- `.faded` opacity 0.35 nas opções não-escolhidas após responder

**Bloco 2 — Editor de cenário no Filament (admin)** (`ScenarioResource.php` + 2 pages):
- **Fix bug SQL:** `'created_at' => now()` no `ScenarioVersion::create` do `EditScenario::afterSave` — resolve o erro "`Field 'created_at' doesn't have a default value`" que aparecia ao salvar/trocar plataforma no editor
- Botão **Salvar** no topo do EditScenario (via `getHeaderActions()` + `->action('save')`)
- Botão **Criar cenário** no topo do CreateScenario (via `getHeaderActions()` + `->action('create')`)
- Nova **Section "Cabeçalho do e-mail"** no form, visível apenas se `platform=email`. 3 campos armazenados em `content.email_from_name/address/subject`
- Textarea "Texto da mensagem": `->rows(2)` → `->rows(12)` + `->autosize()` (antes vinha apertado, agora abre expandido)

Ambos os blocos estão **prontos e passaram por clean-code-reviewer**. Aguardando OK do Pedro pra commit + push + deploy quando ele voltar OU decisão do novo dev.

**Untracked (homolog adiado — NÃO commitar):**
```
?? deploy/.env.homolog
?? deploy/04-deploy-homolog.sh
?? deploy/nginx-homolog.conf
?? docs/HOMOLOG-SETUP.md
```

Homolog está pronto pra ativar mas foi pausado por escolha do Pedro — ver seção Ambientes.

## Known Backlog (post-launch)

| # | Item | Priority | Status |
|---|------|----------|--------|
| 1 | Configure SMTP M365 (email delivery) | High | **Em progresso** — domínio provisionado, roteiro pronto em `docs/EMAIL-SMTP-SETUP.md` OU memória `project-email-smtp-pendente`. Falta criar caixa + habilitar SMTP AUTH + editar `.env` da VM |
| 2 | Commit + deploy dos 2 blocos pendentes no working tree (cenários imersivos por plataforma + editor de cenário) | High | Prontos e revisados, aguardando OK |
| 3 | Enable 2FA TOTP on super admin | High | Backlog |
| 4 | Ativar homologação (`homolog.m2guardiao.com.br`) — 4 arquivos untracked prontos | Medium | Adiado por escolha do Pedro |
| 5 | Investigate intermittent dropdown logout bug (Filament user menu, possibly cache-related) | Medium | Backlog |
| 6 | Migrate CSP from `Report-Only` to enforced after observation | Medium | Backlog |
| 7 | Move `Logo_guardiao.png` de `backgrounds/` pra `brand/` (semanticamente correto) | Low | Cosmético |
| 8 | Visual refinements with marketing team | Low | Backlog |
| 9 | LGPD legal copy (privacy policy + consent) | Low | Backlog |
| 10 | Upgrade Nginx 1.18 → 1.24+ in maintenance window | Low | Backlog |
