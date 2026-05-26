# M2 Guardião Digital — Status do Projeto

> **Documento Técnico — Snapshot do Desenvolvimento**
> Data: 26/05/2026
> Versão: 1.0 (pré-produção)
> Status: ✅ **Desenvolvimento concluído — pronto para deploy em produção**

---

## 1. Visão Geral do Produto

**M2 Guardião Digital** é uma plataforma **SaaS B2B** (Software-as-a-Service para empresas) de **conscientização em segurança digital corporativa**. A M2 Cloud & Security cadastra empresas-cliente, e estas empresas usam a plataforma para treinar seus colaboradores em cenários reais de fraude (phishing, BEC, engenharia social).

### Modelo de negócio
- **3 personas de usuário** dentro do sistema
- **2 níveis de licença** (Demo gratuito + Pro pago)
- Provisionamento controlado pela M2 (clientes não se auto-cadastram)

### Personas
| Persona | Acesso | Como entra |
|---------|--------|------------|
| **Admin M2 (nós)** | Painel administrativo completo | Email + senha + 2FA |
| **Líder Cliente** | Painel da empresa dele | Email + senha gerada pela M2 |
| **Colaborador** | Tela de treinamento | Magic link único por e-mail |

### Licenças
| Recurso | Demo | Pro |
|---------|------|-----|
| Máx. colaboradores | 3 (fixo) | Configurável pela M2 |
| Cenários disponíveis | 3 (1 WhatsApp + 1 Teams + 1 E-mail) | 13 (todos) |
| Dashboard do líder | Parcial (algumas métricas com blur) | Completo |
| Customização de cenários | Não | Sim (M2 edita por cliente) |
| Relatório PDF | Sim (básico) | Sim (completo) |

---

## 2. Stack Tecnológica

### Backend
| Camada | Tecnologia | Versão |
|--------|-----------|--------|
| Linguagem | **PHP** | 8.3 |
| Framework | **Laravel** | 11.x |
| Painel Admin | **Filament** | 3.3 |
| ORM | Eloquent (nativo Laravel) | — |
| Templating | Blade | nativo |
| Validação | Form Requests (Laravel) | nativo |
| Filas | Queue driver `database` | nativo |
| Cache | Driver `database` | nativo |
| Sessão | Driver `database` (encrypted) | nativo |

### Frontend
| Camada | Tecnologia | Versão |
|--------|-----------|--------|
| CSS | Tailwind CSS | 3.x (via Filament) |
| JavaScript | Alpine.js + Livewire | 3.x / 3.x |
| Build | Vite | 5.x |
| Ícones | Heroicons | (via Blade Icons) |

### Banco de Dados
| Item | Valor |
|------|-------|
| SGBD | **MariaDB** 10.11 (compatível 100% MySQL) |
| Engine | InnoDB |
| Charset | utf8mb4 (suporte completo a emojis) |
| Collation | utf8mb4_unicode_ci |
| Tabelas próprias | 12 |
| Tabelas Laravel | 7 (sessions, jobs, cache, etc.) |

### Bibliotecas externas (pacotes Composer)
| Pacote | Função |
|--------|--------|
| `filament/filament` | Painel administrativo |
| `barryvdh/laravel-dompdf` | Geração de PDF dos relatórios |
| `pragmarx/google2fa-laravel` | Autenticação em 2 fatores (TOTP) |
| `pestphp/pest` | Framework de testes automatizados |
| `livewire/livewire` | Componentes reativos |

---

## 3. Funcionalidades Implementadas

### 3.1 — Painel Admin (M2)
- **Gestão de Empresas-cliente** — CRUD completo (criar, editar, suspender)
- **Gestão de Líderes** — vincular líder à empresa, gerar senha aleatória, "Mostrar Credenciais" com botões de copiar funcionais (clipboard JS com fallback HTTP)
- **Gestão de Colaboradores** — visualização global, filtros por empresa/status, envio de convites individual ou em massa
- **Editor visual de cenários** — interface drag-and-drop com Repeater do Filament para criar/editar mensagens e perguntas; versionamento automático ao editar
- **Listagem de cenários por abas** — 5 abas (Todos, WhatsApp, Teams, E-mail, Outras Plataformas)
- **Modal de resultados por empresa** — visão consolidada com cards de KPI e tabela de colaboradores
- **2FA TOTP** — setup com QR code, códigos de recuperação, challenge após login
- **Audit log** — todas operações registradas (criação de empresa, login de líder, mudança de licença, etc.)

### 3.2 — Painel do Líder Cliente
- **Login dedicado** em `/lider/login` com e-mail + senha (gerada pela M2)
- **Dashboard com métricas**:
  - Total de colaboradores convidados / concluídos / pendentes
  - Taxa de conclusão (%)
  - Pontuação média (%)
  - Top 5 cenários com pior desempenho (acertos por cenário)
  - Estatísticas por departamento
- **Blur Pro/Demo** — métricas avançadas borradas para Demo com CTA para upgrade
- **Página de convites** — formulário para convidar colaboradores + listagem dos já convidados com status (Aguardando / Em andamento / Concluído)
- **Botão "Copiar Link"** — gera magic link individual com clipboard funcional
- **Botão "Reenviar e-mail"** — para colaboradores pendentes
- **Exportação PDF** — relatório completo com branding M2
- **Brute-force lockout** — 5 tentativas inválidas = 15 min de bloqueio

### 3.3 — Experiência do Colaborador
- **Acesso por magic link único** (sem necessidade de senha)
- **Magic link de uso único** — token consumido após primeiro acesso; expira em 30 dias
- **Tela de treinamento estilo chat animado** — mensagens aparecem progressivamente com indicador "digitando..."
- **3 plataformas simuladas** — WhatsApp, Microsoft Teams e E-mail (cada uma com layout próprio)
- **Perguntas com 4 alternativas** e feedback educacional detalhado em cada resposta
- **Sistema de pontuação** — acertos contados por cenário e total
- **Tela de conclusão** — exibe score, parabenização e dispara notificação para o líder

### 3.4 — Conteúdo de Treinamento
**13 cenários completos** migrados do protótipo original (`m2shield.html`), cada um com 2-4 perguntas e 4 alternativas com feedback educacional:

**WhatsApp (6 cenários):**
1. Fraude do CEO (BEC clássico)
2. Fornecedor Estratégico (alteração bancária)
3. Suporte TI Externo (typosquatting de TeamViewer)
4. Banco Corporativo (phishing de credenciais)
5. RH Corporativo (salary diversion)
6. Parceiro Executivo (anexo .exe disfarçado)

**Microsoft Teams (4 cenários):**
1. CEO Marcus Ribeiro (NDA + urgência)
2. IT Security Global (RAT instalação remota)
3. Jurídico Contencioso (depósito judicial via PIX pessoal)
4. Compliance & Auditoria (credenciais do ERP)

**E-mail (3 cenários):**
1. Bradesco Empresas (spear phishing com regulação fake)
2. TechSupply LTDA (invoice fraud com subdomínio falso)
3. CEO via Gmail pessoal (gift card scam)

Cada cenário pode ser marcado para uma ou mais **áreas-alvo** (9 opções: Todos, Diretoria, Financeiro, RH, TI, Comercial, Jurídico, Operacional, Compras) — facilita escolher cenários adequados ao perfil de cada colaborador.

### 3.5 — Sistema de E-mail
- **Provider:** Microsoft 365 (SMTP autenticado com Senha de Aplicativo)
- **Envio assíncrono** via Laravel Queue (driver database + Supervisor)
- **Templates HTML responsivos** com branding M2 (preto + vermelho #CC0000)
- **Tipos de e-mail:**
  - Convite ao colaborador (com magic link)
  - Credenciais do líder (URL + e-mail + senha)
- **Retry automático** em caso de falha de SMTP

---

## 4. Segurança

| Camada | Implementação |
|--------|--------------|
| **2FA TOTP** | Admin (obrigatório), via app autenticador (Google/Microsoft/Authy) |
| **Magic Links** | Tokens armazenados como hash SHA-256 (não em plain), single-use, expiração configurável (7d líder / 30d colaborador) |
| **Senhas** | bcrypt cost 12 (admin) ou cost padrão Laravel (líder) |
| **CSRF** | Proteção global via middleware `VerifyCsrfToken` |
| **XSS** | Blade auto-escape em todas saídas; conteúdo HTML controlado |
| **SQL Injection** | Eloquent ORM (prepared statements 100%) |
| **Rate Limiting** | Magic link: 10/min/IP, Admin login: 5/min/IP, Convites: 20/min/líder |
| **Brute-force lockout** | 5 tentativas inválidas = conta bloqueada por 15 min (admin e líder) |
| **Security Headers** | X-Frame-Options DENY, HSTS, X-Content-Type-Options, Referrer-Policy, Permissions-Policy |
| **Sessões** | Driver `database` (não cookies/file), encrypted, httpOnly, SameSite=Strict, Secure (em HTTPS) |
| **Audit Log** | Todas operações sensíveis gravadas em tabela dedicada (LGPD compliance) |
| **HTTPS** | Configuração pronta para Let's Encrypt (renovação automática) |

---

## 5. Testes Automatizados

**Framework:** Pest (sobre PHPUnit)
**Banco de testes:** SQLite em memória (não toca no banco principal)

**17 testes funcionais cobrindo:**
- Magic Link (6 testes)
  - Geração e consumo válido
  - Token único (não pode ser reusado)
  - Token expirado é rejeitado
  - Token inválido redireciona para erro
  - Login correto para líder e colaborador
- Fluxo de Treinamento (4 testes)
  - Colaborador não-autenticado é redirecionado
  - Acesso autenticado funciona
  - Cenários demo são listados
  - Completar treinamento marca colaborador como "concluído"
- Brute-force Admin (4 testes)
  - Tentativa falha incrementa contador
  - 5 falhas bloqueiam a conta
  - Admin bloqueado não acessa painel
  - Admin suspenso não acessa painel
- Smoke tests (3)

**Resultado atual:** ✅ 17 testes passando, 34 asserções, sem falhas

---

## 6. Infraestrutura e Deploy

### 6.1 — Repositório
- **Hospedagem:** GitHub
- **URL:** https://github.com/pedrocadev/m2guardian
- **Branch principal:** `main`
- **Workflow:** desenvolvimento local → commit → push → deploy via script no servidor

### 6.2 — Ambiente de Desenvolvimento
- **Sistema:** Windows 11 Pro
- **Ambiente local:** Laravel Herd (PHP 8.4 + MariaDB)
- **IDE:** VS Code + Claude Code (assistente IA)
- **Versionamento:** Git

### 6.3 — Ambiente de Produção (planejado)
- **Servidor:** VPS Ubuntu 22.04 LTS, 8GB RAM (provisionamento sob demanda)
- **Web Server:** Nginx
- **Runtime:** PHP 8.3-FPM
- **Banco:** MariaDB 10.11
- **Filas:** Supervisor mantendo 2 workers `queue:work`
- **Scheduler:** Cron rodando `php artisan schedule:run` a cada minuto
- **Backup:** Cron diário às 3h, retenção de 7 dias
- **SSL/HTTPS:** Let's Encrypt via Certbot (renovação automática)
- **Firewall:** UFW + Fail2ban
- **Email:** Microsoft 365 SMTP (porta 587 TLS)

### 6.4 — Bundle de Deploy
Pasta `deploy/` no repositório contém **scripts e configurações prontos para produção**:
- `01-server-setup.sh` — provisiona toda a stack (PHP, Nginx, MariaDB, Node, Supervisor, Certbot, UFW)
- `02-database-setup.sh` — cria banco + usuário com senha aleatória + otimizações
- `03-deploy-app.sh` — clona/atualiza o app, instala dependências, roda migrations, otimiza caches (idempotente — pode rodar várias vezes)
- `nginx-http.conf` / `nginx-https.conf` — configs web (HTTP inicial + HTTPS pós-domínio)
- `.env.production` — template de configuração
- `supervisor-worker.conf` — config do queue worker
- `cron-scheduler.txt` — agendamento + backup automático
- `DEPLOY.md` — guia passo a passo de 6 fases (servidor → app → web → workers → SSL → verificação)

### 6.5 — Tempo estimado de deploy
- **Primeira vez:** 20-25 minutos do SSH inicial até o admin logando
- **Deploys futuros (atualizações):** 30 segundos (1 comando: `sudo bash deploy/03-deploy-app.sh`)

---

## 7. Capacidade e Performance

**Configuração com 8GB de RAM suporta confortavelmente:**
- ~50 empresas-cliente ativas (Demo + Pro)
- ~500 colaboradores em treinamento simultâneo
- ~2.000 sessões/dia
- Envio de e-mails: ~500/hora via M365 (limite do provedor)

**Plano de escala (quando necessário):**
- Adicionar Redis (cache + sessão + queue)
- Aumentar número de queue workers
- CDN para assets estáticos
- Migrar banco para servidor dedicado
- Load balancer + múltiplas instâncias

---

## 8. Compliance e LGPD

- ✅ **Audit log completo** — todas ações sensíveis registradas
- ✅ **Senhas hasheadas** (bcrypt) — nunca em texto plano
- ✅ **Magic links hasheados** (SHA-256) — token original nunca armazenado
- ✅ **Sessões encriptadas** no banco
- ✅ **Sem PII em logs aplicacionais** — apenas IDs
- ✅ **Soft deletes** — recuperação de dados deletados acidentalmente
- ✅ **HTTPS obrigatório em produção**
- ⏳ **Política de Privacidade** — pendente (texto jurídico)
- ⏳ **Termo de Consentimento** — pendente (no fluxo do colaborador)
- ⏳ **Exportação de dados pessoais** — não implementado (LGPD Art. 18)

---

## 9. Resumo Executivo (uma página)

| Quesito | Resposta |
|---------|---------|
| **O que é** | SaaS B2B de treinamento em segurança digital corporativa |
| **Linguagem backend** | PHP 8.3 |
| **Framework backend** | Laravel 11 |
| **Painel admin** | Filament 3 |
| **Banco de dados** | MariaDB 10.11 (compatível MySQL 100%) |
| **Frontend** | Blade + Tailwind CSS + Alpine.js + Livewire |
| **Autenticação** | Multi-guard: senha + 2FA (admin), senha (líder), magic link (colaborador) |
| **E-mail** | Microsoft 365 SMTP |
| **Geração de PDF** | dompdf |
| **Versionamento** | Git + GitHub (`pedrocadev/m2guardian`) |
| **Testes** | Pest — 17 testes passando |
| **Servidor produção** | Ubuntu 22.04 LTS + Nginx + PHP-FPM + MariaDB |
| **SSL** | Let's Encrypt (Certbot) |
| **Filas/jobs** | Supervisor + Laravel Queue |
| **Conteúdo pronto** | 13 cenários de treinamento (WhatsApp, Teams, E-mail) |
| **Fases concluídas** | 8 de 8 (100%) |
| **Status atual** | ✅ **Pronto para deploy em produção** |
| **Tempo de deploy** | ~25 min primeira vez, ~30 seg atualizações |

---

## 10. Próximos Passos

1. ⏳ **Provisionar VM de produção** (Ubuntu 22.04, 8GB RAM, qualquer provedor)
2. ⏳ **Executar bundle de deploy** (`deploy/DEPLOY.md`)
3. ⏳ **Apontar domínio + ativar HTTPS** (quando o domínio estiver definido)
4. ⏳ **Configurar M365 com Senha de Aplicativo** para envio de e-mails
5. ⏳ **Testar fluxo end-to-end** em produção (criar empresa → líder → colaborador → treinamento → relatório)
6. ⏳ **Refinamento visual** com o time de marketing (planejado para depois)
7. ⏳ **Texto jurídico LGPD** (política de privacidade, termo de consentimento)

---

**Documento gerado pela equipe técnica em conjunto com Claude Sonnet 4.6 (Anthropic).**
