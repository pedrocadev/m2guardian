# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Environment

- **Runtime:** Laravel 11 + PHP 8.4 via **Laravel Herd** (Windows)
- **Database:** MariaDB/MySQL — local via HeidiSQL, database `m2guardian`
- **Local URL:** `http://m2guardian.test` (Herd site + Windows hosts entry)
- **PHP path:** `C:\Users\Pedrosa\.config\herd\bin` (not in default PowerShell PATH — add manually if needed)

To get PHP in a new PowerShell session:
```powershell
$env:PATH = "C:\Users\Pedrosa\.config\herd\bin;$env:PATH"
cd C:\Projects\m2guardian
```

## Common Commands

```powershell
php artisan migrate:fresh --seed     # Reset DB and run all seeders
php artisan config:clear             # Required after .env changes
php artisan route:clear
php artisan view:clear
php artisan tinker                   # Interactive REPL
php artisan queue:work               # Process queued emails (driver: database)
```

Default super admin: `suporte@m2cloud.com.br` / `M2Guardian@2026`

## Architecture

### Authentication — 3 independent guards

| Guard | Model | Auth method | Middleware alias |
|---|---|---|---|
| `admin` | `Admin` | Email + password (Filament) | `auth:admin` (Filament handles) |
| `leader` | `Leader` | Magic link | `auth.leader` |
| `collaborator` | `Collaborator` | Magic link | `auth.collaborator` |

Guards are configured in `config/auth.php`. Middleware aliases registered in `bootstrap/app.php`.

### Magic Link flow

1. `MagicLink::generateFor($model, $purpose, $days)` — generates 48-char random token, stores **only `sha256` hash** in DB, returns `['plain_token' => ..., 'magic_link' => ...]`
2. Token sent via email as URL: `/auth/acesso?t={plain_token}`
3. `MagicLinkController::consume()` — hashes the plain token, calls `MagicLink::findValid()`, marks `consumed_at`, logs in via correct guard
4. Links are single-use (`consumed_at`) and time-limited (`expires_at`)

### Admin Panel (Filament 3.3)

Path: `/admin` — uses `admin` guard. Panel config in `app/Providers/Filament/AdminPanelProvider.php`.

Resources auto-discovered from `app/Filament/Resources/`. Custom branding: M2 red `#CC0000` + black sidebar via `public/css/filament-theme.css` injected via `renderHook('panels::head.end')`.

**`AdminResource`** is super-admin only (`canAccess()` checks `isSuper()`). Creating a `Leader` via Filament automatically generates a magic link and sends the invite email (hooked in `CreateLeader::afterCreate()`). Edit page has a "Reenviar Convite" action.

### Areas by guard

- **`/admin/*`** — Filament panel (admin guard)
- **`/lider/dashboard`** — Leader dashboard (`auth.leader` middleware) → `LeaderController`
- **`/treinamento/*`** — Collaborator training (`auth.collaborator` middleware) → `CollaboratorController`
- **`/auth/acesso?t=`** — Magic link consumption (public) → `MagicLinkController`

### Key models and relationships

- `Company` → hasMany `Leader`, `Collaborator`, `Scenario`; belongsTo `Admin` (created_by)
- `Leader` → belongsTo `Company`; hasMany `Collaborator` (invited); morphMany `MagicLink`
- `Collaborator` → belongsTo `Company`, `Leader`; hasOne `TrainingSession`; morphMany `MagicLink`
- `Scenario` → `company_id = NULL` means default M2 template; `is_default + demo_eligible` controls demo access
- `MagicLink` → polymorphic (`tokenable`) — points to `Leader` or `Collaborator`

### Email

In local dev, `MAIL_MAILER=log` — emails go to `storage/logs/laravel.log`. Queue driver is `database` — run `php artisan queue:work` to process. For production, configure SMTP in `.env` (`MAIL_MAILER=smtp`).

### Scenarios JSON structure (`content` column)

```json
{
  "messages": [
    { "type": "text", "from": "them", "body": "..." },
    { "type": "question", "prompt": "...", "options": [
      { "key": "a", "text": "...", "correct": true, "feedback": "..." },
      { "key": "b", "text": "...", "correct": false, "feedback": "..." }
    ]}
  ]
}
```

## Build Roadmap (current phase)

- **Fase 3 ✅** — Magic link auth + leader dashboard + invite email
- **Fase 4 (next)** — Collaborator training experience (consuming scenarios, saving answers)
- **Fase 5** — Leader dashboard metrics with Demo blur on premium sections
- **Fase 6** — Pro license + scenario editor in admin
- **Fase 7** — Advanced metrics + PDF export
- **Fase 8** — Hardening (2FA TOTP for admin, rate limiting, Pest tests)
