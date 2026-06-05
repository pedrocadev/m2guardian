# M2 Guardião Digital

Plataforma B2B de **treinamento de segurança da informação** para colaboradores corporativos. Empresas-cliente convidam seus funcionários, que passam por cenários simulados de ataque (WhatsApp, e-mail, Teams) e aprendem a identificar ameaças.

🌐 **Produção:** [m2guardiao.com.br](https://m2guardiao.com.br)

## Stack

- **Backend:** Laravel 11 + PHP 8.4
- **Admin panel:** Filament 3.3 (M2 branding — vermelho `#CC0000` + dark sidebar)
- **Banco:** MariaDB 10.6 (InnoDB, utf8mb4)
- **Frontend treinamento:** Blade + CSS puro (sem framework)
- **Auth:** Multi-guard (admin, leader, collaborator)
- **Testes:** Pest 3 / PHPUnit 11
- **Infra:** Oracle Cloud Always Free (ARM Ampere) + Nginx + PHP-FPM + Supervisor

## Quick start (local)

Requer **Laravel Herd** (Windows/macOS) com PHP 8.4 + MariaDB local.

```bash
git clone https://github.com/pedrocadev/m2guardian.git
cd m2guardian
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

App roda em `http://m2guardian.test` (Herd auto-detecta a pasta).

## Estrutura

```
app/
├── Filament/Resources/       # CRUDs admin (Company, Leader, Collaborator, Scenario, Admin)
├── Http/Controllers/         # Controllers (MagicLink, Collaborator, Leader, Report)
├── Models/                   # Eloquent models
├── Services/CnpjService.php  # Validação CNPJ + lookup BrasilAPI
└── Mail/                     # Templates de e-mail (invites)

resources/views/
├── training/                 # Fluxo do colaborador (welcome, how-it-works, transition, show, completed)
├── leader/                   # Painel do líder (dashboard, invite)
├── filament/                 # Customizações Filament
└── emails/                   # Templates de e-mail

docs/                         # Documentação (deploy, status, entregas)
.claude/
├── agents/                   # Agentes especializados (test-local, test-prod, test-security, db-health, clean-code-reviewer)
└── commands/                 # Slash commands que invocam os agentes
```

## Documentação

| Arquivo | O que tem |
|---------|-----------|
| [docs/DEPLOY-GUIA.md](docs/DEPLOY-GUIA.md) | Como subir código pro Git, deploy na VM, rollback |
| [docs/DEPLOY-REPORT.md](docs/DEPLOY-REPORT.md) | Relatório técnico da implantação inicial (Mai/2026) |
| [docs/STATUS.md](docs/STATUS.md) | Visão técnica completa pra liderança |
| [docs/ENTREGAS-RESUMO.md](docs/ENTREGAS-RESUMO.md) | Sumário das entregas (Jun/2026) |
| [CLAUDE.md](CLAUDE.md) | Guia para Claude Code (convenções, gotchas, comandos) |

## Slash commands (Claude Code)

Roda dentro de `c:\Projects\m2guardian`:

| Comando | Função |
|---------|--------|
| `/test-local` | 12 testes funcionais em `m2guardian.test` |
| `/test-prod` | 11 testes funcionais em produção |
| `/test-security` | 11 testes defensivos (HTTPS, headers, injection, XSS) |
| `/db-health` | Diagnóstico do banco em produção (via PuTTY) |
| `/clean-code` | Revisão de duplicação, código morto, redundância no diff atual |

## Comandos comuns

```bash
php artisan test                          # Suite Pest completa
php artisan test --filter=MagicLinkTest   # Suite específica
php artisan migrate --seed                # Reset DB local com seeders
php artisan optimize:clear                # Limpa caches Laravel
```

## Deploy

Multi-push: um único `git push origin main` envia pros **2 repositórios** simultaneamente (pessoal + organização). Detalhes em [docs/DEPLOY-GUIA.md](docs/DEPLOY-GUIA.md).

Na VM (PuTTY):

```bash
sudo -i
/var/www/m2guardian/deploy/03-deploy-app.sh
```

## Licença

Proprietária — M2 Cloud & Security.
