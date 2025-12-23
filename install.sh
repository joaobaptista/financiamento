#!/bin/bash

# ==============================================================================
# Script de Instalação Automatizada para a Plataforma de Financiamento (Laravel)
# Domínio: origocrowd.com.br
# Servidor Web: Apache
# Banco de Dados: PostgreSQL
# Sistema Operacional: Ubuntu 24.10
# ==============================================================================

# Variáveis de Configuração
DOMAIN="origocrowd.com.br"
PROJECT_DIR="/var/www/$DOMAIN"
DB_NAME="catarse_db"
DB_USER="catarse_user"
PHP_VERSION="8.4" # Versão corrigida para atender ao requisito do Composer

# Cores para o terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

# Função para exibir mensagens de status
status() {
    echo -e "\n${GREEN}==============================================================================${NC}"
    echo -e "${GREEN}>>> $1${NC}"
    echo -e "${GREEN}==============================================================================${NC}\n"
}

# Função para exibir erros
error() {
    echo -e "\n${RED}==============================================================================${NC}"
    echo -e "${RED}!!! ERRO: $1${NC}"
    echo -e "${RED}==============================================================================${NC}\n"
    exit 1
}


# ------------------------------------------------------------------------------
# 1. Verificações Iniciais e Coleta de Dados
# ------------------------------------------------------------------------------

if [ "$EUID" -ne 0 ]; then
    error "Este script deve ser executado como root (sudo)."
fi

status "Coletando informações e gerando senha segura para o banco de dados..."

# Solicitar a senha do banco de dados ao usuário
read -s -p "Digite a senha que deseja usar para o usuário PostgreSQL '$DB_USER': " DB_PASSWORD
echo
if [ -z "$DB_PASSWORD" ]; then
    error "A senha do banco de dados não pode ser vazia."
fi

# ------------------------------------------------------------------------------
# 2. Instalação de Dependências do Sistema
# ------------------------------------------------------------------------------

status "Atualizando o sistema e instalando dependências essenciais (Apache, PHP, PostgreSQL, Git, Composer, Node.js)..."



#Atualizar e instalar pacotes básicos
apt update && apt upgrade -y
apt install -y git curl wget unzip

# Instalar Apache
apt install -y apache2

# Instalar PostgreSQL
apt install -y postgresql postgresql-contrib

# Instalar PHP e extensões necessárias para Laravel
# Nota: A versão do PHP foi alterada para 8.4. Se falhar, você deve adicionar o PPA do Ondrej manualmente.
apt install -y "php$PHP_VERSION" "libapache2-mod-php$PHP_VERSION" "php$PHP_VERSION-cli" "php$PHP_VERSION-pgsql" "php$PHP_VERSION-mbstring" "php$PHP_VERSION-xml" "php$PHP_VERSION-bcmath" "php$PHP_VERSION-zip" "p>

# Instalar Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php' );"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# Instalar Node.js (usando NodeSource para versão recente)
curl -fsSL https://deb.nodesource.com/setup_lts.x | bash -
apt install -y nodejs

# Instalar Certbot para Apache
apt install -y certbot python3-certbot-apache

# ------------------------------------------------------------------------------
# 3. Configuração do PostgreSQL (COM VERIFICAÇÃO DE EXISTÊNCIA )
# ------------------------------------------------------------------------------

status "Configurando o banco de dados PostgreSQL..."

# 1. Criar Usuário (se não existir)
sudo -u postgres psql -tc "SELECT 1 FROM pg_user WHERE usename = '$DB_USER'" | grep -q 1
if [ $? -ne 0 ]; then
    sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASSWORD';" || error "Falha ao criar usuário PostgreSQL."
    echo -e "${YELLOW}Usuário PostgreSQL '$DB_USER' criado com sucesso.${NC}"
else
    echo -e "${YELLOW}Usuário PostgreSQL '$DB_USER' já existe. Pulando criação.${NC}"
fi

# 2. Criar Banco de Dados (se não existir)
sudo -u postgres psql -tc "SELECT 1 FROM pg_database WHERE datname = '$DB_NAME'" | grep -q 1
if [ $? -ne 0 ]; then
    sudo -u postgres psql -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;" || error "Falha ao criar banco de dados PostgreSQL."
    echo -e "${YELLOW}Banco de dados PostgreSQL '$DB_NAME' criado com sucesso.${NC}"
else
    echo -e "${YELLOW}Banco de dados PostgreSQL '$DB_NAME' já existe. Pulando criação.${NC}"
fi

# ------------------------------------------------------------------------------
# 4. Configuração e Deploy da Aplicação
# ------------------------------------------------------------------------------

status "Clonando o repositório e configurando a aplicação Laravel..."

# Clonar o repositório
if [ -d "$PROJECT_DIR" ]; then
    echo -e "${YELLOW}O diretório $PROJECT_DIR já existe. Pulando clonagem e assumindo que o código está atualizado.${NC}"
    cd "$PROJECT_DIR" || error "Falha ao entrar no diretório do projeto."
else
    git clone https://github.com/joaobaptista/financiamento.git "$PROJECT_DIR" || error "Falha ao clonar o repositório."
    cd "$PROJECT_DIR" || error "Falha ao entrar no diretório do projeto."
fi

# Configurar .env (apenas se não existir ou se for a primeira vez )
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Atualizar .env com as configurações
sed -i "s/DB_CONNECTION=sqlite/DB_CONNECTION=pgsql/" .env # CORREÇÃO: Força o driver PostgreSQL
sed -i "s/DB_DATABASE=catarse_db/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=seu_usuario/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=sua_senha/DB_PASSWORD=$DB_PASSWORD/" .env
sed -i "s|APP_URL=http://localhost:8000|APP_URL=https://$DOMAIN|" .env
sed -i "s/APP_ENV=local/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" .env

# Instalar dependências PHP e Node.js
composer install --no-dev --optimize-autoloader || error "Falha ao instalar dependências PHP (Composer ). Verifique a versão do PHP."
npm install || error "Falha ao instalar dependências Node.js (npm)."
npm run build || error "Falha ao compilar assets (Vite)."

# Gerar chave da aplicação e rodar migrations
php artisan key:generate
# CORREÇÃO: Força o uso do driver pgsql para evitar erro de driver sqlite
php artisan migrate --force --database=pgsql || error "Falha ao rodar migrations do banco de dados."




# Configurar permissões
chown -R www-data:www-data "$PROJECT_DIR"
chmod -R 775 "$PROJECT_DIR/storage"
chmod -R 775 "$PROJECT_DIR/bootstrap/cache"

# ------------------------------------------------------------------------------
# 5. Configuração do Apache Virtual Host
# ------------------------------------------------------------------------------

status "Configurando o Virtual Host do Apache para $DOMAIN..."

VHOST_CONF="/etc/apache2/sites-available/$DOMAIN.conf"

# Conteúdo do Virtual Host (apenas se não existir)
if [ ! -f "$VHOST_CONF" ]; then
cat <<EOF > "$VHOST_CONF"
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $PROJECT_DIR/public

    <Directory $PROJECT_DIR/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/$DOMAIN-error.log
    CustomLog \${APACHE_LOG_DIR}/$DOMAIN-access.log combined
</VirtualHost>
EOF

# Habilitar o Virtual Host e módulos necessários
a2ensite "$DOMAIN.conf"
a2enmod rewrite
a2enmod ssl
a2enmod headers
a2enmod env

# Reiniciar Apache para aplicar as configurações
systemctl restart apache2
fi





# ------------------------------------------------------------------------------
# 6. Configuração do SSL com Certbot
# ------------------------------------------------------------------------------

status "Configurando o certificado SSL com Certbot (Let's Encrypt)..."

# O Certbot tentará obter o certificado e configurar o redirecionamento HTTP para HTTPS
# IMPORTANTE: O domínio deve estar apontando para o IP do servidor para que isso funcione.
certbot --apache -d "$DOMAIN" -d "www.$DOMAIN" --non-interactive --agree-tos --email "seu_email@exemplo.com" --redirect || {
    echo -e "${YELLOW}Aviso: A configuração do Certbot falhou. Isso geralmente ocorre se o domínio não estiver apontando corretamente para o servidor.${NC}"
    echo -e "${YELLOW}A aplicação deve estar acessível via HTTP, mas não HTTPS. Você pode tentar rodar 'sudo certbot --apache' manualmente mais tarde.${NC}"
}

# ------------------------------------------------------------------------------
# 7. Finalização
# ------------------------------------------------------------------------------

status "Instalação Concluída!"

echo -e "${GREEN}A plataforma foi instalada com sucesso em: ${NC}"
echo -e "${YELLOW}https://$DOMAIN${NC}"
echo -e "\n${YELLOW}Detalhes do Banco de Dados (Guarde esta informação! ):${NC}"
echo -e "  - Usuário: $DB_USER"
echo -e "  - Senha: $DB_PASSWORD"
echo -e "  - Banco: $DB_NAME"
echo -e "\n${YELLOW}Próximos Passos:${NC}"
echo "1. Se o Certbot falhou, verifique se o domínio $DOMAIN está apontando para o IP da sua VPS e execute 'sudo certbot --apache' novamente."
echo "2. Acesse a URL e finalize a configuração da plataforma (como a criação do primeiro usuário administrador)."
echo "3. Edite o arquivo .env para configurar serviços externos (e-mail, etc.)."
echo "4. Considere configurar um supervisor para o queue:work (fila de jobs) para produção."

# Fim do script
exit 
