# Configuração de e-mail SMTP (M365)

Roteiro passo-a-passo pra ativar envio real de convites em produção. Escrito em 2026-07-07 quando o processo estava em progresso.

## Estado

- Atualmente `MAIL_MAILER=log` em produção — convites são **gravados no log**, não enviados de verdade
- Provedor escolhido: **Microsoft 365 (Exchange Online)** — M2 Cloud já tem tenant
- Domínio: **`m2guardiao.com.br`** — já provisionado no M365
- Volume esperado: baixo (até 100/dia) — SMTP do M365 sobra (limite 30/min, 10k/dia por conta)

## Pré-requisitos

- Acesso admin ao Microsoft 365 Admin Center (https://admin.microsoft.com)
- Acesso SSH à VM de produção (Ubuntu, IP: `137.131.186.168`)
- Uma licença Exchange Online disponível pra atribuir à nova caixa

## Passos

### 1. Criar a caixa de e-mail

1. Vai em https://admin.microsoft.com
2. **Usuários** → **Usuários ativos** → **Adicionar usuário**
3. Preenche:
   - **Nome de exibição:** "Guardião Digital" (ou o que preferir)
   - **Nome de usuário:** parte antes do `@` — sugestões: `guardiao`, `contato`, `treinamento` (**evita `noreply`** — algumas empresas bloqueiam esse padrão automaticamente)
   - **Domínio:** `m2guardiao.com.br` (deve aparecer no dropdown se o domínio foi verificado)
   - **Senha:** gera uma forte pra login manual (você ainda pode precisar acessar a caixa pra ver respostas)
4. Atribui **licença Exchange Online** (Business Basic ~R$25/mês)
5. Finaliza

> ⚠️ **NÃO usar shared mailbox** (caixa compartilhada) pra SMTP AUTH — Microsoft bloqueia na maioria dos casos.

### 2. Habilitar SMTP AUTH

Por padrão, desde 2022, o SMTP AUTH vem **desligado** em contas novas. Precisa ativar:

1. Ainda no Admin Center → **Usuários** → **Usuários ativos**
2. Clica na conta recém-criada
3. Aba **Correio** → seção "Aplicativos de email" → **Gerenciar aplicativos de email**
4. Marca ✅ **"SMTP autenticado"**
5. Salva

### 3. Gerar App Password (se MFA estiver ligado na conta)

**Se a conta tem MFA (multi-factor authentication) ativado**, a senha normal não funciona pra SMTP. Precisa gerar App Password:

1. Loga na conta em https://mysignins.microsoft.com/security-info (com login da caixa nova)
2. **Adicionar método de entrada** → **Senha de aplicativo**
3. Dá um nome descritivo (ex: "Guardião SMTP produção")
4. **Copia a senha de 16 caracteres** — só aparece uma vez! Guarda em local seguro

Se conta não tem MFA (não recomendado — sempre habilite MFA), usa a senha normal.

### 4. Confirmar DNS (SPF)

Como o domínio já está no M365, o SPF provavelmente está OK. Confirma rodando no terminal:

```bash
nslookup -type=TXT m2guardiao.com.br
```

Deve aparecer entre os TXT records:
```
v=spf1 include:spf.protection.outlook.com -all
```

Se **não** aparecer, adiciona esse registro TXT no painel do provedor DNS (RegistroBR ou onde o domínio está delegado). Sem SPF, e-mails vão pra spam.

**Bonus (recomendado):** habilita DKIM pelo Admin Center → **Segurança** → **Email & collaboration** → **Threat policies** → **DKIM** → seleciona `m2guardiao.com.br` → **Enable**. Isso vai adicionar 2 CNAMEs no seu DNS que precisam ser copiados pro provedor.

### 5. Editar `.env` na VM de produção

Conecta na VM via PuTTY (IP `137.131.186.168`) e:

```bash
sudo nano /var/www/m2guardian/.env
```

Localiza as linhas `MAIL_*` e substitui por:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=<CAIXA-ESCOLHIDA>@m2guardiao.com.br
MAIL_PASSWORD=<APP-PASSWORD-ou-SENHA>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=<CAIXA-ESCOLHIDA>@m2guardiao.com.br
MAIL_FROM_NAME="Guardião Digital"
```

Salva: `Ctrl+O`, `Enter`, `Ctrl+X`.

### 6. Aplicar

```bash
cd /var/www/m2guardian
sudo -u m2guardian php artisan config:clear
sudo -u m2guardian php artisan config:cache
sudo systemctl restart php8.4-fpm
```

### 7. Testar envio real

```bash
sudo -u m2guardian php artisan tinker
```

No tinker:
```php
Mail::raw('Teste do Guardião Digital', function($m) {
    $m->to('SEU-EMAIL-PESSOAL@gmail.com')->subject('Teste SMTP');
});
```

**Sucesso:** retorna `[]` (array vazio). Cheque seu Gmail em ~5s.

**Erros comuns:**
| Erro | Causa | Solução |
|---|---|---|
| `535 5.7.3 Authentication unsuccessful` | SMTP AUTH desabilitado na conta | Voltar ao passo 2 |
| `530 5.7.57 Client was not authenticated` | Senha errada ou App Password expirado | Gera nova App Password (passo 3) |
| `Connection could not be established` | Firewall/porta 587 bloqueada na VM | Testar `telnet smtp.office365.com 587` |
| E-mail chegou em spam | SPF/DKIM não configurado ou domínio novo sem reputação | Configurar SPF+DKIM (passo 4) + esperar reputação criar (dias) |

**Sair do tinker:** `exit`.

### 8. Atualizar template `deploy/.env.production` no repo (SEM SENHA)

Edita [deploy/.env.production](../deploy/.env.production) localmente e substitui as `MAIL_*`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=<CAIXA>@m2guardiao.com.br
MAIL_PASSWORD=CHANGE_ME_ON_VM_DEPLOY
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=<CAIXA>@m2guardiao.com.br
MAIL_FROM_NAME="Guardião Digital"
```

**⚠️ NUNCA COMMITAR A SENHA REAL** — deixa `CHANGE_ME_ON_VM_DEPLOY`. Senha vai só na VM.

Commit + push desse arquivo pro repo.

### 9. Testar envio pelo próprio produto

Depois de tudo:
1. Loga como líder em `https://m2guardiao.com.br/lider/login`
2. **Convidar** → adiciona um colaborador com **seu e-mail pessoal**
3. Clica **Enviar convite**
4. Cheque seu e-mail pessoal em ~10s
5. Se chegou → SMTP tá funcionando pra o produto real

## Checklist de conclusão

- [ ] Caixa criada em `<caixa>@m2guardiao.com.br` com licença Exchange
- [ ] SMTP AUTH habilitado
- [ ] App Password gerado (se MFA on)
- [ ] SPF confirmado (`spf.protection.outlook.com`)
- [ ] DKIM habilitado (bonus)
- [ ] `.env` da VM atualizado
- [ ] Cache limpo + PHP-FPM reiniciado
- [ ] Teste via tinker sucesso
- [ ] Teste via UI do próprio produto sucesso
- [ ] `deploy/.env.production` atualizado no repo (sem senha real)
- [ ] Item #1 do backlog no `CLAUDE.md` marcado como "concluído"
