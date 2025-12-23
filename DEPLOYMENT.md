# 🚀 Guia de Deploy - Catarse Laravel

Este guia fornece instruções para fazer deploy da aplicação Catarse Laravel em diferentes ambientes de produção.

---

## 📋 Índice

- [Pré-requisitos](#pré-requisitos)
- [Preparação para Produção](#preparação-para-produção)
- [Deploy em VPS (Digital Ocean, Linode, AWS EC2)](#deploy-em-vps)
- [Deploy com Laravel Forge](#deploy-com-laravel-forge)
- [Deploy com Docker](#deploy-com-docker)
- [Configurações de Produção](#configurações-de-produção)
- [Monitoramento](#monitoramento)
- [Backup](#backup)
- [Troubleshooting](#troubleshooting)

---

## ✅ Pré-requisitos

### Servidor

- **Ubuntu 22.04 LTS** (recomendado) ou similar
- **PHP 8.2+** com extensões necessárias
- **PostgreSQL 14+**
- **Nginx** ou **Apache**
- **Node.js 18+** e **npm**
- **Composer 2+**
- **Git**
- **SSL Certificate** (Let's Encrypt recomendado)

### Domínio

- Domínio configurado apontando para o servidor
- DNS configurado corretamente

---

## 🔧 Preparação para Produção

### 1. Otimizações

```bash
# Otimizar autoloader
composer install --optimize-autoloader --no-dev

# Cachear configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compilar assets
npm run build
```

### 2. Configurar .env para Produção

```env
APP_NAME="Catarse"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=catarse_production
DB_USERNAME=catarse_user
DB_PASSWORD=senha_segura_aqui

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_username
MAIL_PASSWORD=sua_senha
MAIL_FROM_ADDRESS="noreply@seudominio.com"
MAIL_FROM_NAME="${APP_NAME}"

# Payments
PAYMENTS_DRIVER=mock

# Mercado Pago (se usar em produção)
MERCADOPAGO_ACCESS_TOKEN=
MERCADOPAGO_WEBHOOK_URL=${APP_URL}/api/webhooks/mercadopago
```

### 3. Segurança

```bash
# Gerar nova APP_KEY
php artisan key:generate

# Definir permissões corretas
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🖥️ Deploy em VPS

### Passo 1: Configurar o Servidor

```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar PHP 8.2
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-pgsql php8.2-mbstring php8.2-xml php8.2-curl \
    php8.2-zip php8.2-gd php8.2-redis php8.2-bcmath

# Instalar PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# Instalar Nginx
sudo apt install -y nginx

# Instalar Redis
sudo apt install -y redis-server

# Instalar Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Passo 2: Configurar PostgreSQL

```bash
# Acessar PostgreSQL
sudo -u postgres psql

# Criar banco e usuário
CREATE DATABASE catarse_production;
CREATE USER catarse_user WITH PASSWORD 'senha_segura_aqui';
GRANT ALL PRIVILEGES ON DATABASE catarse_production TO catarse_user;
\q
```

### Passo 3: Clonar e Configurar Aplicação

```bash
# Criar diretório
sudo mkdir -p /var/www/catarse
cd /var/www/catarse

# Clonar repositório
sudo git clone https://github.com/SEU_REPO/catarse-laravel.git .

# Definir permissões
sudo chown -R $USER:www-data /var/www/catarse

# Instalar dependências
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Configurar .env
cp .env.example .env
nano .env  # Editar com configurações de produção

# Gerar chave
php artisan key:generate

# Executar migrations
php artisan migrate --force

# Otimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ajustar permissões finais
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Passo 4: Configurar Nginx

```bash
sudo nano /etc/nginx/sites-available/catarse
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name seudominio.com www.seudominio.com;
    root /var/www/catarse/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Ativar site
sudo ln -s /etc/nginx/sites-available/catarse /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Passo 5: Configurar SSL com Let's Encrypt

```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obter certificado
sudo certbot --nginx -d seudominio.com -d www.seudominio.com

# Renovação automática já está configurada
```

### Passo 6: Configurar Queue Worker

```bash
sudo nano /etc/systemd/system/catarse-worker.service
```

```ini
[Unit]
Description=Catarse Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=3
ExecStart=/usr/bin/php /var/www/catarse/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
# Ativar e iniciar
sudo systemctl enable catarse-worker
sudo systemctl start catarse-worker
sudo systemctl status catarse-worker
```

### Passo 7: Configurar Cron para Scheduler

```bash
sudo crontab -e -u www-data
```

Adicionar:
```
* * * * * cd /var/www/catarse && php artisan schedule:run >> /dev/null 2>&1
```

---

## ⚡ Deploy com Laravel Forge

Laravel Forge simplifica muito o processo de deploy.

### 1. Criar Servidor no Forge

1. Acesse [forge.laravel.com](https://forge.laravel.com)
2. Conecte seu provedor (Digital Ocean, AWS, etc.)
3. Crie um novo servidor
4. Escolha PHP 8.2, PostgreSQL, Redis

### 2. Criar Site

1. Adicione um novo site
2. Configure o domínio
3. Selecione "Git Repository"
4. Conecte seu repositório GitHub/GitLab

### 3. Configurar Ambiente

1. Vá em "Environment"
2. Configure as variáveis `.env`
3. Salve

### 4. Deploy Script

O Forge gera automaticamente um deploy script. Personalize se necessário:

```bash
cd /home/forge/seudominio.com
git pull origin main
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

### 5. SSL

1. Vá em "SSL"
2. Clique em "Let's Encrypt"
3. Ative

### 6. Queue Worker

1. Vá em "Daemons"
2. Adicione: `php artisan queue:work --tries=3`

### 7. Scheduler

Já configurado automaticamente pelo Forge.

---

## 🐳 Deploy com Docker

### Dockerfile

```dockerfile
FROM php:8.2-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar aplicação
COPY . .

# Instalar dependências
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

# Permissões
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

### docker-compose.yml

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: catarse-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - catarse

  nginx:
    image: nginx:alpine
    container_name: catarse-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - catarse

  postgres:
    image: postgres:14
    container_name: catarse-postgres
    restart: unless-stopped
    environment:
      POSTGRES_DB: catarse
      POSTGRES_USER: catarse
      POSTGRES_PASSWORD: secret
    volumes:
      - postgres-data:/var/lib/postgresql/data
    networks:
      - catarse

  redis:
    image: redis:alpine
    container_name: catarse-redis
    restart: unless-stopped
    networks:
      - catarse

networks:
  catarse:
    driver: bridge

volumes:
  postgres-data:
```

### Executar

```bash
docker-compose up -d
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan config:cache
```

---

## ⚙️ Configurações de Produção

### Otimizações de Performance

```bash
# OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0

# Redis como cache
CACHE_STORE=redis
SESSION_DRIVER=redis
```

### Logs

```bash
# Rotação de logs
sudo nano /etc/logrotate.d/catarse
```

```
/var/www/catarse/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
}
```

---

## 📊 Monitoramento

### Laravel Telescope (Desenvolvimento)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Logs

```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Com Laravel Pail
php artisan pail
```

### Uptime Monitoring

- [UptimeRobot](https://uptimerobot.com/)
- [Pingdom](https://www.pingdom.com/)
- [StatusCake](https://www.statuscake.com/)

---

## 💾 Backup

### Backup Automático do Banco

```bash
# Script de backup
sudo nano /usr/local/bin/backup-catarse.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/catarse"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup PostgreSQL
pg_dump -U catarse_user catarse_production > $BACKUP_DIR/db_$DATE.sql

# Backup arquivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/catarse/storage/app

# Manter apenas últimos 7 dias
find $BACKUP_DIR -type f -mtime +7 -delete
```

```bash
# Tornar executável
sudo chmod +x /usr/local/bin/backup-catarse.sh

# Agendar no cron (diariamente às 2h)
sudo crontab -e
```

```
0 2 * * * /usr/local/bin/backup-catarse.sh
```

---

## 🐛 Troubleshooting

### Erro 500

```bash
# Ver logs
tail -f storage/logs/laravel.log

# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Permissões

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Queue não processa

```bash
# Verificar status
sudo systemctl status catarse-worker

# Reiniciar
sudo systemctl restart catarse-worker

# Ver logs
journalctl -u catarse-worker -f
```

### Migrations falham

```bash
# Verificar conexão com banco
php artisan tinker
>>> DB::connection()->getPdo();

# Executar migration específica
php artisan migrate:refresh --path=/database/migrations/2025_12_17_131814_create_campaigns_table.php
```

---

## 📚 Recursos

- [Laravel Deployment Docs](https://laravel.com/docs/deployment)
- [Laravel Forge](https://forge.laravel.com)
- [DigitalOcean Laravel Tutorials](https://www.digitalocean.com/community/tags/laravel)

---

**Bom deploy! 🚀**
