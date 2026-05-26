# M2 Guardian — Guia de Deploy em Produção

Roteiro completo para subir o M2 Guardian numa VM Ubuntu 22.04 LTS.

---

## Pré-requisitos

- VM Ubuntu 22.04 LTS com **mínimo 4GB RAM** (8GB recomendado)
- Acesso SSH como **root** (ou usuário com sudo)
- IP público da VM
- Conta Microsoft 365 com **Senha de Aplicativo** habilitada (para SMTP)
- (Opcional) Domínio para HTTPS

---

## FASE A — Preparar o Servidor

### A.1 — Primeiro acesso SSH

```bash
ssh root@SEU_IP_DA_VM
# ou: ssh ubuntu@SEU_IP_DA_VM   (em algumas VMs vem com usuário ubuntu)
```

### A.2 — Criar usuário admin (se logou como root)

```bash
adduser m2admin
usermod -aG sudo m2admin
# Copia sua chave SSH para o novo usuário
rsync --archive --chown=m2admin:m2admin ~/.ssh /home/m2admin
# Sai e reconecta como o novo usuário
exit
ssh m2admin@SEU_IP_DA_VM
```

### A.3 — Clonar os scripts de deploy

```bash
# Pasta temporária só pra rodar os scripts
sudo mkdir -p /opt/m2guardian-deploy
cd /opt/m2guardian-deploy
sudo git clone https://github.com/pedrocadev/m2guardian.git .
```

### A.4 — Rodar provisionamento (instala PHP, Nginx, MariaDB, Node, etc.)

```bash
sudo bash deploy/01-server-setup.sh
```

Deve levar 5-10 minutos. Ao final, deve mostrar as versões de PHP 8.3, Node 20, MariaDB 10.11, Nginx.

### A.5 — Configurar o banco

```bash
sudo bash deploy/02-database-setup.sh
```

**ANOTAR A SENHA gerada** que aparece no final (também fica salva em `/root/.m2guardian-db-credentials`).

---

## FASE B — Subir a Aplicação

### B.1 — Primeiro deploy

```bash
sudo bash /opt/m2guardian-deploy/deploy/03-deploy-app.sh
```

O script vai:
1. Clonar o repo em `/var/www/m2guardian`
2. Instalar dependências (Composer + npm)
3. Compilar assets
4. Criar `.env` a partir do template e **parar pra você editar**

### B.2 — Editar o .env

Quando o script pausar:

```bash
sudo -u m2guardian nano /var/www/m2guardian/.env
```

Atualize:

```env
APP_URL=http://SEU_IP_DA_VM

DB_PASSWORD=COLE_A_SENHA_QUE_ANOTOU

MAIL_USERNAME=seu-email@suaempresa.com.br
MAIL_PASSWORD=SUA_SENHA_DE_APP_M365
MAIL_FROM_ADDRESS="seu-email@suaempresa.com.br"
```

**Sobre M365 Senha de Aplicativo:**
1. Entre em https://mysignins.microsoft.com/security-info
2. Adicionar método → Senha de aplicativo
3. Use a senha gerada (16 caracteres) como `MAIL_PASSWORD`
4. Requer 2FA ativado na conta

Pressione `ENTER` no terminal pra continuar o script. Ele vai rodar migrations + seeders e otimizar.

---

## FASE C — Web Server

### C.1 — Configurar Nginx (HTTP por enquanto)

```bash
sudo cp /var/www/m2guardian/deploy/nginx-http.conf /etc/nginx/sites-available/m2guardian
sudo ln -sf /etc/nginx/sites-available/m2guardian /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
```

### C.2 — Testar

Abra no navegador: `http://SEU_IP_DA_VM`

- Deve redirecionar para `/admin/login`
- Use as credenciais: `suporte@m2cloud.com.br` / `M2Guardian@2026`

**Trocar a senha do admin imediatamente** (Painel admin → seu perfil).

---

## FASE D — Workers e Cron

### D.1 — Supervisor (queue worker)

```bash
sudo cp /var/www/m2guardian/deploy/supervisor-worker.conf /etc/supervisor/conf.d/
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start m2guardian-worker:*
sudo supervisorctl status
```

Deve mostrar 2 processos `m2guardian-worker:*` em `RUNNING`.

### D.2 — Cron (scheduler + backup)

```bash
sudo crontab -u m2guardian -e
```

Cole o conteúdo de `/var/www/m2guardian/deploy/cron-scheduler.txt`.

Crie a pasta de backup:

```bash
sudo mkdir -p /var/backups
sudo chown m2guardian:m2guardian /var/backups
```

---

## FASE E — Domínio + HTTPS *(quando tiver domínio)*

### E.1 — Apontar DNS

No seu provedor de domínio (Registro.br, GoDaddy, Cloudflare):

```
Tipo:  A
Nome:  @ (raiz)        → SEU_IP_DA_VM
Tipo:  A
Nome:  www             → SEU_IP_DA_VM
```

Aguarde propagação (5-30 min). Verifique:

```bash
nslookup SEU.DOMINIO.com
```

### E.2 — Atualizar Nginx com domínio

```bash
sudo nano /etc/nginx/sites-available/m2guardian
# Trocar 'server_name _;' por 'server_name SEU.DOMINIO.com www.SEU.DOMINIO.com;'

sudo nginx -t && sudo systemctl reload nginx
```

### E.3 — Gerar SSL com Certbot

```bash
sudo certbot --nginx -d SEU.DOMINIO.com -d www.SEU.DOMINIO.com
```

- Forneça email
- Aceite termos
- Escolha `2: Redirect` (forçar HTTPS)

O Certbot edita o nginx automaticamente. Renovação é automática (cron interno do certbot).

### E.4 — Atualizar .env para HTTPS

```bash
sudo -u m2guardian nano /var/www/m2guardian/.env
```

```env
APP_URL=https://SEU.DOMINIO.com
SESSION_SECURE_COOKIE=true
```

```bash
sudo -u m2guardian php /var/www/m2guardian/artisan config:cache
sudo systemctl restart php8.3-fpm
```

---

## FASE F — Verificações Finais

### Checklist de saúde

```bash
# Status dos serviços
sudo systemctl status nginx php8.3-fpm mariadb supervisor

# Logs da aplicação (últimas 20 linhas)
sudo tail -n 20 /var/www/m2guardian/storage/logs/laravel.log

# Logs do queue worker
sudo tail -n 20 /var/log/m2guardian-worker.log

# Logs do Nginx
sudo tail -n 20 /var/log/nginx/m2guardian-error.log
```

### Teste end-to-end

1. ✅ Admin loga com credenciais e troca a senha
2. ✅ Cria uma empresa de teste
3. ✅ Cria um líder, gera senha, abre o modal de credenciais
4. ✅ Login do líder funciona em `/lider/login`
5. ✅ Líder convida um colaborador (e-mail real)
6. ✅ Colaborador recebe e-mail e completa o treinamento
7. ✅ Dashboard do líder mostra métricas
8. ✅ Exportar PDF funciona

---

## Atualizações Futuras

Para deploy de nova versão (depois de `git push` no repo):

```bash
sudo bash /var/www/m2guardian/deploy/03-deploy-app.sh
```

O script é idempotente: faz `git pull`, atualiza dependências, roda migrations novas, limpa caches, reinicia worker.

---

## Solução de Problemas

### Erro 500 ao acessar
```bash
sudo tail -n 50 /var/www/m2guardian/storage/logs/laravel.log
sudo tail -n 50 /var/log/nginx/m2guardian-error.log
```

### Permissões quebradas
```bash
sudo chown -R m2guardian:www-data /var/www/m2guardian
sudo chmod -R 775 /var/www/m2guardian/storage /var/www/m2guardian/bootstrap/cache
```

### Emails não estão sendo enviados
```bash
# Ver fila travada
sudo -u m2guardian php /var/www/m2guardian/artisan queue:failed

# Reprocessar
sudo -u m2guardian php /var/www/m2guardian/artisan queue:retry all

# Testar SMTP direto
sudo -u m2guardian php /var/www/m2guardian/artisan tinker
> Mail::raw('teste', fn($m) => $m->to('seu@email.com')->subject('Teste'));
```

### Queue worker travou
```bash
sudo supervisorctl restart m2guardian-worker:*
```

### Restaurar backup do banco
```bash
zcat /var/backups/m2guardian-20260601.sql.gz | mysql -u m2guardian -p m2guardian
```

---

## Segurança Adicional (Pós-Deploy)

1. **SSH só por chave** (desabilitar senha):
   ```bash
   sudo nano /etc/ssh/sshd_config
   # PasswordAuthentication no
   sudo systemctl restart sshd
   ```

2. **Trocar porta SSH** (de 22 pra outra):
   ```bash
   # Edita /etc/ssh/sshd_config: Port 2222
   sudo ufw allow 2222/tcp
   sudo ufw delete allow 22/tcp
   ```

3. **Ativar 2FA do admin** no painel logo no primeiro login

4. **Monitorar tentativas de SSH**:
   ```bash
   sudo fail2ban-client status sshd
   ```

---

## Recursos do Servidor

Com 8GB RAM, configurado conforme este guia, suporta:
- ~50 empresas simultâneas (Demo + Pro)
- ~500 colaboradores ativos em treinamento
- ~2000 sessões por dia

Quando precisar escalar:
- Adicionar Redis (cache + sessão)
- Mover queue para Redis
- Aumentar `numprocs` do worker
- Migrar DB para servidor dedicado
