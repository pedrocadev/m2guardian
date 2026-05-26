#!/bin/bash
# ─────────────────────────────────────────────────────────────────────────────
# M2 Guardian — Configuração do Banco MariaDB
# Rodar como root depois de 01-server-setup.sh
# ─────────────────────────────────────────────────────────────────────────────
set -euo pipefail

echo "============================================================"
echo " M2 Guardian — Setup do Banco de Dados"
echo "============================================================"

# Senha aleatória para o usuário do app (12 chars)
APP_DB_PASS=$(tr -dc 'A-Za-z0-9' < /dev/urandom | head -c 16)
ROOT_DB_PASS=$(tr -dc 'A-Za-z0-9' < /dev/urandom | head -c 16)

# Ainda não usamos o root_db_pass; deixa o root via socket (padrão do MariaDB no Ubuntu)

# 1) Secure setup automatizado
mariadb -e "DELETE FROM mysql.user WHERE User='';"
mariadb -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost','127.0.0.1','::1');"
mariadb -e "DROP DATABASE IF EXISTS test;"
mariadb -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"
mariadb -e "FLUSH PRIVILEGES;"

# 2) Criar database + usuário do app
mariadb <<EOF
CREATE DATABASE IF NOT EXISTS m2guardian
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'm2guardian'@'localhost' IDENTIFIED BY '${APP_DB_PASS}';
GRANT ALL PRIVILEGES ON m2guardian.* TO 'm2guardian'@'localhost';
FLUSH PRIVILEGES;
EOF

# 3) Salvar credenciais em arquivo seguro
cat > /root/.m2guardian-db-credentials <<EOF
# M2 Guardian — Credenciais do Banco
# Gerado em: $(date)
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=m2guardian
DB_USERNAME=m2guardian
DB_PASSWORD=${APP_DB_PASS}
EOF
chmod 600 /root/.m2guardian-db-credentials

# 4) Configuração do MariaDB — otimizada para VPS 8GB
cat > /etc/mysql/mariadb.conf.d/99-m2guardian.cnf <<EOF
[mysqld]
# Performance — VPS 8GB
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M
max_connections = 200
query_cache_size = 0
query_cache_type = 0

# Charset
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
init-connect = 'SET NAMES utf8mb4'

# Slow query log (debug)
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
EOF

systemctl restart mariadb

echo ""
echo "============================================================"
echo " ✅ Banco configurado"
echo "============================================================"
echo " Database: m2guardian"
echo " Usuário:  m2guardian"
echo " Senha:    ${APP_DB_PASS}"
echo ""
echo " ⚠️  Credenciais salvas em /root/.m2guardian-db-credentials"
echo "    Use estes valores ao configurar o .env da aplicação"
echo "============================================================"
