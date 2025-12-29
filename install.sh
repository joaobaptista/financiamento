#!/bin/bash

set -e

# ==============================================================================
# Script de Instalação Automatizada - Plataforma de Financiamento (Laravel)
# ==============================================================================
# Descrição: Instala e configura automaticamente o projeto Laravel com todas
#            as dependências necessárias conforme documentado no README.md
# Execução: DEVE ser executado como ROOT (sudo)
# Diretório Alvo: /var/www/origocrowd.com.br
# ==============================================================================

# ==============================================================================
# Variáveis de Configuração
# ==============================================================================

REPO_URL="https://github.com/joaobaptista/financiamento.git"
DOMAIN="origocrowd.com.br"
PROJECT_DIR="/var/www/$DOMAIN"
DB_NAME="origo_db"
DB_USER="postgres"
DB_PASSWORD=""

# Requisitos de versão (conforme README.md)
PHP_MIN_VERSION="8.2"
COMPOSER_MIN_VERSION="2.0"
NODE_MIN_VERSION="18"
NPM_MIN_VERSION="9"
POSTGRES_MIN_VERSION="14"

# ==============================================================================
# Cores
# ==============================================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ==============================================================================
# Funções
# ==============================================================================

status() {
    echo ""
    echo -e "${GREEN}========================================${NC}"
    echo -e "${GREEN}✓ $1${NC}"
    echo -e "${GREEN}========================================${NC}"
    echo ""
}

info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

error() {
    echo -e "${RED}✗ ERRO: $1${NC}"
    exit 1
}

# ==============================================================================
# 1. Verificações Iniciais
# ==============================================================================

status "Verificando requisitos do sistema"

# Verificar se está rodando como root
if [ "$EUID" -ne 0 ]; then
    error "Este script DEVE ser executado como root. Use: sudo ./install.sh"
fi

# Identificar o usuário real que chamou o sudo
REAL_USER=${SUDO_USER:-$USER}
REAL_HOME=$(getent passwd "$REAL_USER" | cut -d: -f6)

info "Executando como root. Usuário original: $REAL_USER"
info "Diretório de instalação: $PROJECT_DIR"

# Verificar sistema operacional
if ! command -v apt &> /dev/null; then
    error "Este script requer um sistema baseado em Debian/Ubuntu com apt"
fi

# ==============================================================================
# 2. Instalação de Dependências do Sistema
# ==============================================================================

status "Atualizando sistema e instalando dependências básicas"

apt update
apt install -y git curl wget unzip software-properties-common

# ==============================================================================
# 3. Instalação do PHP >= 8.2
# ==============================================================================

status "Instalando PHP >= 8.2"

# Adicionar repositório PHP
add-apt-repository ppa:ondrej/php -y
apt update

# Instalar PHP 8.2 ou superior
apt install -y \
    php8.2 \
    php8.2-cli \
    php8.2-fpm \
    php8.2-pgsql \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-zip \
    php8.2-curl \
    php8.2-gd \
    php8.2-intl

# Verificar versão do PHP
PHP_VERSION=$(php -r "echo PHP_VERSION;")
info "PHP instalado: versão $PHP_VERSION"

# ==============================================================================
# 4. Instalação do Composer >= 2.0
# ==============================================================================

status "Instalando Composer >= 2.0"

# Verificar se Composer já está instalado
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | grep -oP '\d+\.\d+\.\d+' | head -1)
    info "Composer já instalado: versão $COMPOSER_VERSION"
else
    # Instalar Composer
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --quiet
    mv composer.phar /usr/local/bin/composer
    rm composer-setup.php
    
    COMPOSER_VERSION=$(composer --version | grep -oP '\d+\.\d+\.\d+' | head -1)
    info "Composer instalado: versão $COMPOSER_VERSION"
fi

# ==============================================================================
# 5. Instalação do Node.js >= 18.x e npm >= 9.x
# ==============================================================================

status "Instalando Node.js >= 18.x e npm >= 9.x"

# Verificar se Node.js já está instalado
if command -v node &> /dev/null; then
    NODE_VERSION=$(node -v | grep -oP '\d+' | head -1)
    if [ "$NODE_VERSION" -ge 18 ]; then
        info "Node.js já instalado: versão $(node -v)"
        info "npm já instalado: versão $(npm -v)"
    else
        warning "Node.js versão $(node -v) é menor que 18.x. Atualizando..."
        curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
        apt install -y nodejs
    fi
else
    # Instalar Node.js 18.x LTS
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
    apt install -y nodejs
    
    info "Node.js instalado: versão $(node -v)"
    info "npm instalado: versão $(npm -v)"
fi

# ==============================================================================
# 6. Instalação do PostgreSQL >= 14.x
# ==============================================================================

status "Instalando PostgreSQL >= 14.x"

# Instalar PostgreSQL
apt install -y postgresql postgresql-contrib

# Verificar versão do PostgreSQL
POSTGRES_VERSION=$(psql --version | grep -oP '\d+' | head -1)
info "PostgreSQL instalado: versão $(psql --version)"

# Iniciar serviço PostgreSQL
systemctl start postgresql
systemctl enable postgresql

# ==============================================================================
# 7. Verificação de Versões Instaladas
# ==============================================================================

status "Verificando versões instaladas"

echo "Requisitos (conforme README.md):"
echo "  - PHP >= 8.2"
echo "  - Composer >= 2.0"
echo "  - Node.js >= 18.x e npm >= 9.x"
echo "  - PostgreSQL >= 14.x"
echo "  - Git"
echo ""
echo "Versões instaladas:"
php -v | head -1
composer --version
node -v
npm -v
psql --version
git --version

# ==============================================================================
# 8. Preparação do Diretório e Clone do Repositório
# ==============================================================================

status "Preparando diretório e clonando repositório"

# Criar diretório /var/www se não existir
mkdir -p /var/www

if [ -d "$PROJECT_DIR" ]; then
    if [ "$(ls -A "$PROJECT_DIR")" ]; then
        warning "O diretório $PROJECT_DIR já existe e não está vazio."
        if [ -d "$PROJECT_DIR/.git" ]; then
            info "Repositório git detectado. Atualizando..."
            cd "$PROJECT_DIR"
            git pull origin main || warning "Não foi possível atualizar o repositório"
        else
            warning "Diretório existe mas não é um repositório git."
            info "Fazendo backup do conteúdo atual e clonando novamente..."
            mv "$PROJECT_DIR" "${PROJECT_DIR}_backup_$(date +%Y%m%d_%H%M%S)"
            git clone "$REPO_URL" "$PROJECT_DIR"
            cd "$PROJECT_DIR"
        fi
    else
        info "Diretório $PROJECT_DIR está vazio. Clonando..."
        git clone "$REPO_URL" "$PROJECT_DIR"
        cd "$PROJECT_DIR"
    fi
else
    info "Clonando repositório para $PROJECT_DIR"
    git clone "$REPO_URL" "$PROJECT_DIR"
    cd "$PROJECT_DIR"
fi

# ==============================================================================
# 9. Instalação de Dependências do PHP
# ==============================================================================

status "Instalando dependências do PHP (composer install)"

# Executar como o usuário real para evitar problemas de permissão no cache do composer
sudo -u "$REAL_USER" composer install

# ==============================================================================
# 10. Instalação de Dependências do Node.js
# ==============================================================================

status "Instalando dependências do Node.js (npm install)"

# Executar como o usuário real
sudo -u "$REAL_USER" npm install

# ==============================================================================
# 11. Configuração do Ambiente
# ==============================================================================

status "Configurando arquivo de ambiente (.env)"

# Copiar .env.example para .env
if [ ! -f .env ]; then
    cp .env.example .env
    chown "$REAL_USER":"$REAL_USER" .env
    info "Arquivo .env criado a partir de .env.example"
else
    warning "Arquivo .env já existe. Não será sobrescrito."
fi

# ==============================================================================
# 12. Geração da Chave da Aplicação
# ==============================================================================

status "Gerando chave da aplicação (php artisan key:generate)"

sudo -u "$REAL_USER" php artisan key:generate

# ==============================================================================
# 13. Configuração do Banco de Dados PostgreSQL
# ==============================================================================

status "Configurando banco de dados PostgreSQL"

info "Configurando credenciais do PostgreSQL no .env"

# Solicitar senha do PostgreSQL
read -sp "Digite a senha do usuário PostgreSQL (postgres): " DB_PASSWORD
echo ""

# Criar banco de dados
info "Criando banco de dados $DB_NAME..."
sudo -u postgres psql -tc "SELECT 1 FROM pg_database WHERE datname = '$DB_NAME'" | grep -q 1 || \
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME;"

info "Banco de dados $DB_NAME criado/verificado com sucesso"

# Configurar .env com credenciais do banco
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env
sed -i "s/^DB_HOST=.*/DB_HOST=127.0.0.1/" .env
sed -i "s/^DB_PORT=.*/DB_PORT=5432/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env

# Configurar APP_NAME e APP_URL
sed -i 's/^APP_NAME=.*/APP_NAME="Origo"/' .env
sed -i "s|^APP_URL=.*|APP_URL=http://$DOMAIN|" .env

info "Arquivo .env configurado com sucesso"

# ==============================================================================
# 14. Execução das Migrations e Seeding
# ==============================================================================

status "Configurando banco de dados (Migrations e Seeding)"

read -p "Deseja LIMPAR o banco e popular com dados de teste? (Isso apagará dados existentes) (s/N): " RESET_DB

if [[ "$RESET_DB" =~ ^[Ss]$ ]]; then
    info "Executando php artisan migrate:fresh --seed..."
    sudo -u "$REAL_USER" php artisan migrate:fresh --seed --force
else
    info "Executando apenas php artisan migrate..."
    sudo -u "$REAL_USER" php artisan migrate --force
fi

# ==============================================================================
# 15. Compilação de Assets
# ==============================================================================

status "Compilando assets para desenvolvimento"

info "Executando npm run build..."
sudo -u "$REAL_USER" npm run build

# ==============================================================================
# 16. Configuração de Permissões
# ==============================================================================

status "Configurando permissões finais"

# Definir dono como o usuário real e grupo www-data para o servidor web
chown -R "$REAL_USER":www-data "$PROJECT_DIR"
chmod -R 775 "$PROJECT_DIR/storage"
chmod -R 775 "$PROJECT_DIR/bootstrap/cache"

# ==============================================================================
# 17. Finalização
# ==============================================================================

status "✅ Instalação concluída com sucesso!"

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}🎉 Projeto instalado e configurado!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}📁 Diretório do projeto:${NC} $PROJECT_DIR"
echo -e "${BLUE}📊 Banco de dados:${NC} $DB_NAME"
echo ""
echo -e "${YELLOW}🚀 Para executar o projeto:${NC}"
echo ""
echo -e "  ${GREEN}Opção 1 - Executar tudo de uma vez (Recomendado):${NC}"
echo -e "    cd $PROJECT_DIR"
echo -e "    composer dev"
echo ""
echo -e "  ${GREEN}Opção 2 - Executar manualmente em terminais separados:${NC}"
echo ""
echo -e "    ${BLUE}Terminal 1 - Servidor Laravel:${NC}"
echo -e "      cd $PROJECT_DIR"
echo -e "      php artisan serve"
echo ""
echo -e "    ${BLUE}Terminal 2 - Vite (Frontend):${NC}"
echo -e "      cd $PROJECT_DIR"
echo -e "      npm run dev"
echo ""
echo -e "    ${BLUE}Terminal 3 - Fila de Jobs (opcional):${NC}"
echo -e "      cd $PROJECT_DIR"
echo -e "      php artisan queue:work"
echo ""
echo -e "${GREEN}🌐 Acesse a aplicação em:${NC} ${YELLOW}http://$DOMAIN:8000${NC}"
echo ""
