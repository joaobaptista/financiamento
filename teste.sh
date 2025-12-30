#!/bin/bash
set -euo pipefail

# ==============================================================================
# Laravel Production Install Script
# Domain: origocrowd.com.br
# OS: Ubuntu 24.10
# Stack: Apache + PHP 8.4 (FPM) + PostgreSQL
# CI/CD Ready
# ==============================================================================

# ------------------------------------------------------------------------------
# Config
# ------------------------------------------------------------------------------
DOMAIN="origocrowd.com.br"
PROJECT_DIR="/var/www/$DOMAIN"

DB_NAME="catarse_db"
DB_USER="catarse_user"

PHP_VERSION="8.4"
CERTBOT_EMAIL="dev@origocrowd.com.br"

# ------------------------------------------------------------------------------
# Helpers
# ------------------------------------------------------------------------------
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

log()   { echo -e "${GREEN}>>> $1${NC}"; }
error() { echo -e "${RED}!!! $1${NC}"; exit 1; }

[ "$EUID" -ne 0 ] && error "Execute como root (sudo)."

# ------------------------------------------------------------------------------
# 1. System
# ------------------------------------------------------------------------------
log "Atualizando sistema"
apt update && apt upgrade -y
apt install -y git curl unzip ca-certificates software-properties-common

# ------------------------------------------------------------------------------
# 2. Apache
# ------------------------------------------------------------------------------
log "Instalando Apache"
apt install -y apache2
a2enmod rewrite proxy proxy_fcgi setenvif headers ssl
systemctl enable apache2

# ------------------------------------------------------------------------------
# 3. PostgreSQL
# ------------------------------------------------------------------------------
log "Instalando PostgreSQL"
apt install -y postgresql postgresql-contrib

read -s -p "Senha do usuário PostgreSQL ($DB_USER): " DB_PASSWORD
echo
[ -z "$DB_PASSWORD" ] && error "Senha vazia."

sudo -u postgres psql -tc "SELECT 1 FROM pg_user WHERE usename='$DB_USER'" | grep -q 1 || \
sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASSWORD';"

sudo -u postgres psql -tc "SELECT 1 FROM pg_database WHERE datname='$DB_NAME'" | grep -q 1 || \
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;"

# ------------------------------------------------------------------------------
# 4. PHP 8.4 ONLY
# ------------------------------------------------------------------------------
log "Instalando PHP $PHP_VERSION ONLY"
add-apt-repository ppa:ondrej/php -y
apt update

apt install -y \
php$PHP_VERSION \
php$PHP_VERSION-fpm \
php$PHP_VERSION-cli \
php$PHP_VERSION-pgsql \
php$PHP_VERSION-mbstring \
php$PHP_VERSION-xml \
php$PHP_VERSION-bcmath \
php$PHP_VERSION-zip \
php$PHP_VERSION-curl \
php$PHP_VERSION-intl \
php$PHP_VERSION-opcache

update-alternatives --set php /usr/bin/php$PHP_VERSION
php -v | grep "PHP $PHP_VERSION" || error "PHP 8.4 não ativo"

systemctl enable php$PHP_VERSION-fpm
systemctl start php$PHP_VERSION-fpm

# ------------------------------------------------------------------------------
# 5. Composer
# ------------------------------------------------------------------------------
log "Instalando Composer"
php -r "copy('https://getcomposer.org/installer','composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
composer --version

# ------------------------------------------------------------------------------
# 6. Node.js LTS
# ------------------------------------------------------------------------------
log "Instalando Node.js LTS"
curl -fsSL https://deb.nodesource.com/setup_lts.x | bash -
apt install -y nodejs
node -v
npm -v

# ------------------------------------------------------------------------------
# 7. Deploy da Aplicação
# ------------------------------------------------------------------------------
log "Clonando aplicação"
mkdir -p /var/www
if [ ! -d "$PROJECT_DIR/.git" ]; then
    git clone https://github.com/joaobaptista/financiamento.git "$PROJECT_DIR"
fi

cd "$PROJECT_DIR"

# .env
[ ! -f .env ] && cp .env.example .env

sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
sed -i "s|^APP_URL=.*|APP_URL=https://$DOMAIN|" .env
sed -i "s/^APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/^APP_DEBUG=.*/APP_DEBUG=false/" .env

# Permissões base
chown -R www-data:www-data "$PROJECT_DIR"

# ------------------------------------------------------------------------------
# 8. Composer (produção limpa)
# ------------------------------------------------------------------------------
log "Composer install (produção)"
sudo -u www-data composer install \
--no-dev \
--no-interaction \
--prefer-dist \
--optimize-autoloader \
--classmap-authoritative

# ------------------------------------------------------------------------------
# 9. npm – FIX DEFINITIVO (cache local)
# ------------------------------------------------------------------------------
log "Configurando npm cache local"

rm -rf /var/www/.npm /var/www/.npmrc || true
rm -rf "$PROJECT_DIR/node_modules" "$PROJECT_DIR/.npm" || true

mkdir -p "$PROJECT_DIR/.npm"

cat > "$PROJECT_DIR/.npmrc" <<EOF
cache=$PROJECT_DIR/.npm
EOF

chown -R www-data:www-data "$PROJECT_DIR"

log "Instalando dependências frontend"
sudo -u www-data npm install
sudo -u www-data npm run build

# ------------------------------------------------------------------------------
# 10. Laravel otimizações
# ------------------------------------------------------------------------------
log "Laravel otimizações"
php artisan key:generate --force
php artisan migrate --force

php artisan optimize:clear
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

chmod -R 775 storage bootstrap/cache

# ------------------------------------------------------------------------------
# 11. Apache VirtualHost (PHP-FPM)
# ------------------------------------------------------------------------------
log "Configurando Apache VirtualHost"
a2dissite 000-default.conf || true

VHOST="/etc/apache2/sites-available/$DOMAIN.conf"

cat > "$VHOST" <<EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $PROJECT_DIR/public

    <Directory $PROJECT_DIR/public>
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php$PHP_VERSION-fpm.sock|fcgi://localhost/"
    </FilesMatch>

    ErrorLog \${APACHE_LOG_DIR}/$DOMAIN-error.log
    CustomLog \${APACHE_LOG_DIR}/$DOMAIN-access.log combined
</VirtualHost>
EOF

a2ensite "$DOMAIN.conf"
systemctl reload apache2

# ------------------------------------------------------------------------------
# 12. SSL
# ------------------------------------------------------------------------------
log "Configurando SSL"
apt install -y certbot python3-certbot-apache

certbot --apache \
-d "$DOMAIN" \
-d "www.$DOMAIN" \
--non-interactive \
--agree-tos \
--email "$CERTBOT_EMAIL" \
--redirect || echo "⚠️ SSL pendente (DNS)"

# ------------------------------------------------------------------------------
# Done
# ------------------------------------------------------------------------------
log "Deploy concluído com sucesso 🚀"
echo "URL: https://$DOMAIN"
php -v
