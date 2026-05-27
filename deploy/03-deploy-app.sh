#!/bin/bash
# ─────────────────────────────────────────────────────────────────────────────
# M2 Guardian — Deploy/Atualização da Aplicação
# Pode ser rodado várias vezes (idempotente). Usa o usuário m2guardian.
# Rodar como root.
# ─────────────────────────────────────────────────────────────────────────────
set -euo pipefail

APP_DIR="/var/www/m2guardian"
# Usa SSH Deploy Key (cadastrada em Settings > Deploy keys do repo)
REPO_URL="git@github.com:M2-Solution-Dev/M2Guardian.2-0.git"
APP_USER="m2guardian"

echo "============================================================"
echo " M2 Guardian — Deploy da Aplicação"
echo "============================================================"

# 1) Primeiro deploy: clonar; depois: atualizar
if [ ! -d "$APP_DIR/.git" ]; then
    echo "→ Primeiro deploy: clonando repositório..."
    sudo -u $APP_USER git clone $REPO_URL $APP_DIR
else
    echo "→ Atualizando repositório existente..."
    cd $APP_DIR
    sudo -u $APP_USER git pull origin main
fi

cd $APP_DIR

# 2) Instalar dependências PHP (sem dev, otimizado)
echo "→ composer install..."
sudo -u $APP_USER composer install --no-dev --optimize-autoloader --no-interaction

# 3) Instalar dependências JS e compilar assets
echo "→ npm ci && npm run build..."
sudo -u $APP_USER npm ci
sudo -u $APP_USER npm run build

# 4) .env — se não existe, copia do template e gera APP_KEY
if [ ! -f .env ]; then
    echo "→ Criando .env a partir do template..."
    sudo -u $APP_USER cp deploy/.env.production .env
    sudo -u $APP_USER php artisan key:generate --force
    echo ""
    echo "⚠️  .env criado. EDITE AGORA antes de continuar:"
    echo "    nano $APP_DIR/.env"
    echo ""
    echo "    Atualize: DB_PASSWORD, MAIL_* (M365), APP_URL, APP_DOMAIN"
    echo ""
    read -p "Pressione ENTER depois de editar o .env... "
fi

# 5) Permissões — pastas escritas pelo Laravel
sudo chown -R $APP_USER:www-data $APP_DIR
sudo chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# 6) Migrations
echo "→ Rodando migrations..."
sudo -u $APP_USER php artisan migrate --force

# 7) Seeders (só na primeira vez)
if [ ! -f $APP_DIR/storage/.seeded ]; then
    echo "→ Seeders (admin + cenários)..."
    sudo -u $APP_USER php artisan db:seed --force
    sudo -u $APP_USER touch $APP_DIR/storage/.seeded
fi

# 8) Otimizações — cache de config, rotas, views
echo "→ Otimizando Laravel..."
sudo -u $APP_USER php artisan config:cache
sudo -u $APP_USER php artisan route:cache
sudo -u $APP_USER php artisan view:cache
sudo -u $APP_USER php artisan event:cache
sudo -u $APP_USER php artisan filament:cache-components 2>/dev/null || true

# 9) Storage link (uploads públicos)
sudo -u $APP_USER php artisan storage:link 2>/dev/null || true

# 10) Restart serviços
echo "→ Restart PHP-FPM e queue..."
systemctl restart php8.3-fpm
supervisorctl restart m2guardian-worker:* 2>/dev/null || true

echo ""
echo "============================================================"
echo " ✅ Deploy concluído"
echo "============================================================"
echo " App em: $APP_DIR"
echo " Logs:   $APP_DIR/storage/logs/laravel.log"
echo "============================================================"
