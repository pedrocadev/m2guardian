#!/bin/bash
# ─────────────────────────────────────────────────────────────────────────────
# M2 Guardian — Server Setup (Ubuntu 22.04)
# Rodar como root logo após criar a VM. Instala toda a stack base.
# ─────────────────────────────────────────────────────────────────────────────
set -euo pipefail

echo "============================================================"
echo " M2 Guardian — Provisionamento Ubuntu 22.04"
echo "============================================================"

# 1) Atualizar sistema
apt update && apt upgrade -y

# 2) Pacotes base
apt install -y curl wget git unzip software-properties-common \
    ca-certificates lsb-release apt-transport-https gnupg \
    ufw fail2ban htop nano vim

# 3) PHP 8.4 (via Ondrej PPA — exigência de Laravel 11 + Symfony 8)
LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php8.4 php8.4-fpm php8.4-cli php8.4-common \
    php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl \
    php8.4-zip php8.4-gd php8.4-bcmath php8.4-intl \
    php8.4-soap php8.4-redis php8.4-imagick php8.4-readline

# 4) Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# 5) Node 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# 6) Nginx
apt install -y nginx

# 7) MariaDB 10.11
apt install -y mariadb-server mariadb-client
systemctl enable mariadb
systemctl start mariadb

# 8) Supervisor (queue worker)
apt install -y supervisor
systemctl enable supervisor

# 9) Certbot (SSL futuro)
apt install -y certbot python3-certbot-nginx

# 10) Firewall — só 22 (SSH), 80 (HTTP) e 443 (HTTPS)
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

# 11) Fail2ban (proteção SSH)
systemctl enable fail2ban
systemctl start fail2ban

# 12) Criar usuário do app (sem login direto)
if ! id "m2guardian" &>/dev/null; then
    useradd -r -s /bin/bash -d /var/www/m2guardian m2guardian
fi
mkdir -p /var/www/m2guardian
chown -R m2guardian:m2guardian /var/www/m2guardian

# 13) Configurações de PHP recomendadas para Laravel/Filament
PHP_INI="/etc/php/8.4/fpm/php.ini"
sed -i 's/^memory_limit = .*/memory_limit = 512M/' $PHP_INI
sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 32M/' $PHP_INI
sed -i 's/^post_max_size = .*/post_max_size = 32M/' $PHP_INI
sed -i 's/^max_execution_time = .*/max_execution_time = 120/' $PHP_INI
sed -i 's/^;date.timezone =.*/date.timezone = America\/Sao_Paulo/' $PHP_INI

systemctl restart php8.4-fpm

echo ""
echo "============================================================"
echo " ✅ Provisionamento concluído"
echo "============================================================"
echo " Versões instaladas:"
php -v | head -1
node -v
composer --version
nginx -v
mariadb --version | head -1
echo ""
echo " Próximo passo: rodar ./02-database-setup.sh"
echo "============================================================"
