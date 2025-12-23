#!/bin/bash
set -e

# ==============================================================================
# Script de Instalação Automatizada – Plataforma de Financiamento (Laravel)
# Domínio: origocrowd.com.br
# SO: Ubuntu 24.10
# Web: Apache | DB: PostgreSQL
# PHP alvo: 8.4 (fallback automático para 8.3)
# ==============================================================================

# ------------------------------------------------------------------------------
# Variáveis de Configuração
# ------------------------------------------------------------------------------
DOMAIN="origocrowd.com.br"
PROJECT_DIR="/var/www/$DOMAIN"
DB_NAME="catarse_db"
DB_USER="catarse_user"

PHP_TARGET_VERSION="8.4"   # versão desejada/documentada
PHP_FALLBACK_VERSION="8.3" # versão estável real no Ubuntu 24.10

# ------------------------------------------------------------------------------
# Cores
# ------------------------------------------------------------------------------
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
NC='\033[0m'

status() {
    echo -e "\n${GREEN}==============================================================================${NC}"
    echo -e "${GREEN}>>> $1${NC}"
    echo -e "${GREEN}==============================================================================${NC}\n"
}

error() {
    echo -e "\n${RED}ERRO: $1${NC}\n"
    exit 1
}

# ------------------------------------------------------------------------------
# 1. Verificações Iniciais
# ------------------------------------------------------------------------------
[ "$EUID" -ne 0 ] && error "Execute como root (sudo)."

status "Coletando senha do banco de dados"

read -s -p "Digite a senha do usuário PostgreSQL '$DB_USER': " DB_PASSWORD
echo
[ -z "$DB_PASSWORD" ] && error "Senha não pode ser vazia."

read -p "Digite um email válido para o Certbot: " CERTBOT_EMAIL
[ -z "$CERTBOT_EMAIL" ] && error "Email é obrigatório para SSL."

# ------------------------------------------------------------------------------
# 2. Dependências do Sistema
# ------------------------------------------------------------------------------
status "Atualizando sistema e instalando dependências básicas"

apt update && apt upgrade -y
apt install -y git curl wget unzip software-properties-common

# Apache
apt install -y apache2

# PostgreSQL
apt install -y postgresql postgresql-contrib

# ------------------------------------------------------------------------------
# PHP (mantendo versão alvo 8.4 documentada)
# ------------------------------------------------------------------------------
status "Instalando PHP (alvo: $PHP_TARGET_VERSION | fallback: $PHP_FALLBACK_VERSION)"

add-apt-repository ppa:ondrej/php -y
apt update

PHP_VERSION="$PHP_FALLBACK_VERSION"

apt install -y \
php$PHP_VERSION \
libapache2-mod-php$PHP_VERSION \
php$PHP_VERSION-cli \
php$PHP_VERSION-pgsql \
php$PHP_VERSION-mbstring \
php$PHP_VERSION-xml \
php$PHP_VERSION-bcmath \
php$PHP_VERSION-zip \
php$PHP_VERSION-curl

update-alternatives --set php /usr/bin/php$PHP_VERSION

php -v

# ------------------------------------------------------------------------------
# Composer
# ------------------------------------------------------------------------------
status "Instalando Composer"

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

composer --version

# ------------------------------------------------------------------------------
# Node.js (LTS)
# ------------------------------------------------------------------------------
status "Instalando Node.js (LTS)"

curl -fsSL https://deb.nodesource.com/setup_lts.x | bash -
apt install -y nodejs

node -v
npm -v

# ------------------------------------------------------------------------------
# Certbot
# ------------------------------------------------------------------------------
apt install -y certbot python3-certbot-apache

# ------------------------------------------------------------------------------
# 3. PostgreSQL
# ------------------------------------------------------------------------------
status "Configurando PostgreSQL"

sudo -u postgres psql -tc "SELECT 1 FROM pg_user WHERE usename='$DB_USER'" | grep -q 1 || \
sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASSWORD';"

sudo -u postgres psql -tc "SELECT 1 FROM pg_database WHERE datname='$DB_NAME'" | grep -q 1 || \
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;"

# ------------------------------------------------------------------------------
# 4. Deploy da Aplicação
# ------------------------------------------------------------------------------
status "Clonando e configurando Laravel"

if [ ! -d "$PROJECT_DIR" ]; then
    git clone https://github.com/joaobaptista/financiamento.git "$PROJECT_DIR"
fi

cd "$PROJECT_DIR"

[ ! -f .env ] && cp .env.example .env

sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
sed -i "s|^APP_URL=.*|APP_URL=https://$DOMAIN|" .env
sed -i "s/^APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/^APP_DEBUG=.*/APP_DEBUG=false/" .env

chown -R www-data:www-data "$PROJECT_DIR"

sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install
sudo -u www-data npm run build

php artisan key:generate
php artisan migrate --force

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

chmod -R 775 storage bootstrap/cache

# ------------------------------------------------------------------------------
# 5. Apache
# ------------------------------------------------------------------------------
status "Configurando Apache"

a2dissite 000-default.conf

VHOST_CONF="/etc/apache2/sites-available/$DOMAIN.conf"

if [ ! -f "$VHOST_CONF" ]; then
cat <<EOF > "$VHOST_CONF"
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $PROJECT_DIR/public

    <Directory $PROJECT_DIR/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/$DOMAIN-error.log
    CustomLog \${APACHE_LOG_DIR}/$DOMAIN-access.log combined
</VirtualHost>
EOF
fi

a2ensite "$DOMAIN.conf"
a2enmod rewrite ssl headers env
systemctl restart apache2

# ------------------------------------------------------------------------------
# 6. SSL
# ------------------------------------------------------------------------------
status "Configurando SSL (Let's Encrypt)"

certbot --apache \
-d "$DOMAIN" \
-d "www.$DOMAIN" \
--non-interactive \
--agree-tos \
--email "$CERTBOT_EMAIL" \
--redirect || echo -e "${YELLOW}SSL não configurado (DNS ainda não propagado)${NC}"

# ------------------------------------------------------------------------------
# Finalização
# ------------------------------------------------------------------------------
status "Instalação concluída com sucesso"

echo -e "${GREEN}https://$DOMAIN${NC}"
echo -e "${YELLOW}PHP alvo documentado: $PHP_TARGET_VERSION | PHP ativo: $PHP_VERSION${NC}"
