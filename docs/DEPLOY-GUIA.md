# Guia de Deploy — M2 Guardião

Documento prático com os passos pra subir código pro Git e depois pra produção.

**Atalhos:**

1. [Subir código pro Git](#1-subir-código-pro-git)
2. [Deploy na VM (produção)](#2-deploy-na-vm-produção)
3. [Validar se deu certo](#3-validar-se-deu-certo)
4. [Rollback de emergência](#4-rollback-de-emergência)

---

## 1. Subir código pro Git

### 1.1 Antes de subir (checklist)

- [ ] Testei a alteração em `http://m2guardian.test` (Herd local)
- [ ] Não tem `.env` ou `.env.production` no `git status` (são privados, ignorados)
- [ ] Mensagens de commit em português, descritivas

### 1.2 Comandos no PowerShell

Abre o PowerShell **dentro de** `c:\Projects\m2guardian`:

```powershell
# 1. Ver o que mudou
git status

# 2. Ver o diff (opcional, pra revisar)
git diff

# 3. Adicionar os arquivos específicos (NUNCA "git add .")
git add caminho/do/arquivo1.php caminho/do/arquivo2.blade.php

# 4. Commit com mensagem clara
git commit -m "Fix: descricao curta do que mudou e por que"

# 5. Push (envia pros DOIS repos automaticamente)
git push origin main
```

### 1.3 O que acontece no push

O `git push origin main` foi configurado pra **multi-push**: um único comando envia o código pros DOIS repositórios:

```
git push origin main
  ├─→ pedrocadev/m2guardian          (repo pessoal)
  └─→ M2-Solution-Dev/M2Guardian.2-0 (repo da M2)
```

Se o push der certo, você vê 2 confirmações na saída — uma pra cada repo.

### 1.4 Se o push falhar

| Erro | O que fazer |
|------|-------------|
| `Authentication failed` | Seu PAT (Personal Access Token) expirou. Gera novo em github.com/settings/tokens e roda `git config --global credential.helper store` |
| `non-fast-forward` | Alguém (ou outra máquina sua) commitou antes. Roda `git pull origin main --rebase` e tenta de novo |
| `Permission denied` | M2-Solution-Dev exige aprovação do PAT. Vai em github.com/organizations/M2-Solution-Dev/settings/personal-access-token-requests |

---

## 2. Deploy na VM (produção)

### 2.1 Conectar via PuTTY

1. Abre o **PuTTY**
2. Carrega a sessão `guardiao` (ou conecta manualmente):
   - **Host:** `137.131.186.168`
   - **Port:** `22`
   - **Auth:** SSH > Auth > Credentials > Private key file = arquivo `.ppk` que você gerou via PuTTYgen
3. Login como `ubuntu`
4. Cola pra virar root:

```bash
sudo -i
```

> **Atenção:** o Ctrl+V não funciona no PuTTY. Cola com **botão direito do mouse**.

### 2.2 Rodar o deploy

Um comando só:

```bash
/var/www/m2guardian/deploy/03-deploy-app.sh
```

Se aparecer `Permission denied`, rode uma vez:

```bash
chmod +x /var/www/m2guardian/deploy/*.sh
```

E aí roda o deploy normal.

### 2.3 O que o script faz

```
1. git fetch + git reset --hard origin/main    (puxa o codigo novo)
2. composer install --no-dev --optimize        (atualiza pacotes PHP)
3. npm install + npm run build                 (compila CSS/JS Vite)
4. php artisan migrate --force                 (roda migrations novas, se houver)
5. php artisan config:cache                    (cache de config)
6. php artisan route:cache                     (cache de rotas)
7. php artisan view:cache                      (cache de views)
8. php artisan storage:link                    (link simbolico pra uploads)
9. systemctl reload php8.4-fpm                 (reinicia PHP-FPM)
10. supervisorctl restart m2guardian-worker:*  (reinicia queue workers)
```

Quando ver `Deploy concluído`, está no ar. Costuma demorar **2 a 4 minutos**.

### 2.4 Se o deploy falhar

| Erro | O que fazer |
|------|-------------|
| `npm install` ou `npm run build` falhando | `cd /var/www/m2guardian && rm -rf node_modules package-lock.json && npm install` |
| `composer install` falhando | Verifica se o PHP está em 8.4: `php -v`. Se estiver em outra versão, `sudo update-alternatives --set php /usr/bin/php8.4` |
| `migrate` falhando | Olha o log: `tail -80 /var/www/m2guardian/storage/logs/laravel-$(date +%Y-%m-%d).log` |
| 500 no site depois do deploy | Mesmo log acima. Procura por `ERROR` ou `Exception` |

---

## 3. Validar se deu certo

### 3.1 Teste rápido manual

Abre no navegador: **https://guardiao.m2cloud.com.br/admin/login**

Deve carregar a tela de login do Filament com a logo do Guardião. Faz login e clica em algumas telas pra ver se tudo responde.

### 3.2 Skills automatizadas (no Claude Code)

Tenho duas skills criadas que rodam testes automatizados em produção:

```
/test-prod        → 10 testes funcionais (HTTP, rotas, assets)
/test-security    → 12 testes defensivos (headers, CSRF, info disclosure)
```

Roda esses quando quiser confirmar que está tudo de pé.

### 3.3 Ver logs em tempo real

No PuTTY:

```bash
sudo tail -f /var/www/m2guardian/storage/logs/laravel-$(date +%Y-%m-%d).log
```

Deixa essa janela aberta enquanto navega no site — qualquer erro aparece em tempo real.

---

## 4. Rollback de emergência

Se algo quebrar em produção e você precisa voltar agora:

### 4.1 Descobrir o commit anterior

No PowerShell local:

```powershell
git log --oneline -10
```

Vai aparecer algo tipo:

```
a6527b0 Fix botao Copiar Link no admin
48e5513 Fix 500 ao criar lider duplicado
f033765 Treinamento gameficado
9fde040 Merge branch 'main' ...
```

O **commit que você quer voltar** é o segundo (`48e5513` no exemplo) — aquele que estava no ar antes da bagunça.

### 4.2 Reverter

```powershell
# Cria um commit que desfaz o ultimo
git revert HEAD --no-edit

# Push do revert
git push origin main
```

### 4.3 Redeploy na VM

No PuTTY:

```bash
/var/www/m2guardian/deploy/03-deploy-app.sh
```

Em **3 minutos** o sistema volta ao estado anterior. Sem perda de dados — `git revert` só reverte código, não banco.

> **Nunca use `git reset --hard` ou `git push --force` em main** — pode destruir histórico e travar os colegas.

---

## Resumo visual

```
┌─────────────────────────────────────────────────────────────┐
│  MAQUINA LOCAL (Windows + Herd)                             │
│  ───────────────────────────────                            │
│  1. Edita codigo                                            │
│  2. Testa em http://m2guardian.test                         │
│  3. git add + commit + push  ───┐                           │
└──────────────────────────────────┼──────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────┐
│  GITHUB (2 repositorios sincronizados)                      │
│  ─────────────────────────────────────                      │
│   ▸ pedrocadev/m2guardian                                   │
│   ▸ M2-Solution-Dev/M2Guardian.2-0                          │
└──────────────────────────────────┬──────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────┐
│  VM ORACLE CLOUD (Ubuntu + PuTTY)                           │
│  ─────────────────────────────────                          │
│  4. SSH via PuTTY                                           │
│  5. sudo -i                                                 │
│  6. /var/www/m2guardian/deploy/03-deploy-app.sh             │
│  7. Site no ar em https://guardiao.m2cloud.com.br           │
└─────────────────────────────────────────────────────────────┘
```

---

## Anexos úteis

**IP da VM:** `137.131.186.168`
**Usuário SSH:** `ubuntu` (depois `sudo -i` pra virar root)
**URL produção:** `https://guardiao.m2cloud.com.br`
**URL local (Herd):** `http://m2guardian.test`
**Pasta da app no servidor:** `/var/www/m2guardian`
**Logs do Laravel:** `/var/www/m2guardian/storage/logs/laravel-YYYY-MM-DD.log`
**Backup diário do DB:** `/var/backups/m2guardian/` (cron diário 03:00 UTC)

---

*Documento gerado em 2026-05-28 · M2 Cloud & Security*
