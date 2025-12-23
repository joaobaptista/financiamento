#!/bin/bash

# ==========================
# Variáveis de Configuração
# ==========================
DOMAIN="origocrowd.com.br"
PROJECT_DIR="/var/www/$DOMAIN"
DB_NAME="origo_db"
DB_USER="origo_user"
PHP_VERSION="8.4"

echo "🚀 Iniciando deploy do projeto $DOMAIN"

cd $PROJECT_DIR || exit 1

echo "📥 Atualizando código..."
git pull origin main

echo "📦 Instalando dependências PHP..."
/usr/bin/php$PHP_VERSION /usr/local/bin/composer install \
  --no-dev \
  --optimize-autoloader \
  --no-interaction

echo "🧹 Limpando caches..."
/usr/bin/php$PHP_VERSION artisan config:clear
/usr/bin/php$PHP_VERSION artisan route:clear
/usr/bin/php$PHP_VERSION artisan view:clear

echo "🗄️ Rodando migrations..."
/usr/bin/php$PHP_VERSION artisan migrate --force

echo "⚡ Otimizando aplicação..."
/usr/bin/php$PHP_VERSION artisan config:cache
/usr/bin/php$PHP_VERSION artisan route:cache
/usr/bin/php$PHP_VERSION artisan view:cache

echo "✅ Deploy finalizado com sucesso!"
