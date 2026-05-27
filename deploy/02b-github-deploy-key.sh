#!/bin/bash
# ─────────────────────────────────────────────────────────────────────────────
# M2 Guardian — Gera SSH Deploy Key e configura para o usuário do app
# Rodar como root, DEPOIS do 02-database-setup.sh e ANTES do 03-deploy-app.sh
# ─────────────────────────────────────────────────────────────────────────────
set -euo pipefail

APP_USER="m2guardian"
SSH_DIR="/var/www/m2guardian/.ssh"

echo "============================================================"
echo " M2 Guardian — Gerando SSH Deploy Key"
echo "============================================================"

# 1) Cria pasta .ssh do usuário do app
mkdir -p "$SSH_DIR"
chown -R $APP_USER:$APP_USER "$SSH_DIR"
chmod 700 "$SSH_DIR"

# 2) Gera o par de chaves (sem passphrase para uso em scripts)
if [ ! -f "$SSH_DIR/id_ed25519" ]; then
    sudo -u $APP_USER ssh-keygen -t ed25519 -f "$SSH_DIR/id_ed25519" -N "" -C "m2guardian-deploy-key@$(hostname)"
fi

# 3) Adiciona github.com ao known_hosts (evita prompt de confirmação)
sudo -u $APP_USER ssh-keyscan -H github.com >> "$SSH_DIR/known_hosts" 2>/dev/null
chmod 600 "$SSH_DIR/known_hosts"
chown $APP_USER:$APP_USER "$SSH_DIR/known_hosts"

# 4) Configura SSH para não pedir confirmação
cat > "$SSH_DIR/config" <<EOF
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_ed25519
    StrictHostKeyChecking accept-new
EOF
chmod 600 "$SSH_DIR/config"
chown $APP_USER:$APP_USER "$SSH_DIR/config"

# 5) Mostra a chave PÚBLICA para o usuário copiar e cadastrar no GitHub
echo ""
echo "============================================================"
echo " ✅ Deploy Key gerada"
echo "============================================================"
echo ""
echo " 📋 COPIE A CHAVE PÚBLICA ABAIXO (a inteira, em uma linha só):"
echo ""
echo "------------------------ COPIAR DAQUI ------------------------"
cat "$SSH_DIR/id_ed25519.pub"
echo "------------------------ ATÉ AQUI ------------------------"
echo ""
echo " 🌐 AGORA CADASTRE NO GITHUB:"
echo ""
echo "   1. Acesse: https://github.com/M2-Solution-Dev/M2Guardian.2-0/settings/keys"
echo "   2. Clique em \"Add deploy key\""
echo "   3. Title: \"VPS Producao\""
echo "   4. Key: cole a chave acima"
echo "   5. NÃO marque \"Allow write access\" (somente leitura)"
echo "   6. Clique em \"Add key\""
echo ""
echo " 🧪 DEPOIS DE CADASTRAR, TESTE COM:"
echo ""
echo "   sudo -u $APP_USER ssh -T git@github.com"
echo ""
echo "   Deve responder algo como:"
echo '   "Hi M2-Solution-Dev/M2Guardian.2-0! You have successfully authenticated..."'
echo ""
echo "============================================================"
