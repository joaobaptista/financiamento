# 🚀 Origo - Plataforma de Crowdfunding

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

Uma plataforma moderna de crowdfunding construída com **Laravel 12**, **PostgreSQL** e **Bootstrap 5**. Este projeto permite que criadores lancem campanhas de financiamento coletivo, definam recompensas e recebam apoios de apoiadores.

---

## 📋 Índice

- [Funcionalidades](#-funcionalidades)
- [Documentação](#-documentação)
- [Requisitos](#-requisitos)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Executando o Projeto](#-executando-o-projeto)
- [Desenvolvimento Backend](#-desenvolvimento-backend)
- [Desenvolvimento Frontend](#-desenvolvimento-frontend)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Pagamentos (Mercado Pago)](#-pagamentos-mercado-pago)
- [Testes](#-testes)
- [Troubleshooting](#-troubleshooting)
- [Contribuindo](#-contribuindo)

---

## 📚 Documentação

- Arquitetura: `docs/architecture.md`
- Backend: `docs/backend.md`
- Frontend: `docs/frontend.md`
- Banco de dados: `docs/database.md`

## ✨ Funcionalidades

### 🔐 Autenticação
- Registro e login de usuários
- Gerenciamento de perfil
- Proteção de rotas autenticadas

### 📋 Campanhas
- **CRUD completo** de campanhas
- Publicação de campanhas (draft → published)
- Listagem pública de campanhas ativas
- Página de detalhes com informações completas
- Sistema de slugs para URLs amigáveis
- Upload de imagens de campanhas

### 🎁 Recompensas (Rewards)
- Criação de múltiplos níveis de recompensa
- Gestão de quantidade disponível
- Descrição detalhada e valores personalizados

### 💰 Apoios (Pledges)
- Sistema de apoio a campanhas
- Pagamento simulado (mock para MVP)
- Confirmação automática de pagamento
- Rastreamento de apoiadores por campanha

### 💳 Pagamentos (Mercado Pago)
- Guia de integração (PIX + webhook + env vars): `docs/payments/mercadopago.md`

### 📊 Dashboard do Criador
- Visão geral de todas as campanhas criadas
- Estatísticas em tempo real:
  - Total arrecadado
  - Número de apoiadores
  - Progresso da meta
- Detalhes individuais de cada campanha

---

## 🛠️ Requisitos

Antes de começar, certifique-se de ter instalado:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x e **npm** >= 9.x
- **PostgreSQL** >= 14.x
- **Git**

### Verificando as versões instaladas

```bash
php -v
composer --version
node -v
npm -v
psql --version
```

---

## 📦 Instalação

### 1. Clone o repositório

```bash
git clone <URL_DO_REPOSITORIO_PRIVADO>
cd origo
```

### 2. Instale as dependências do PHP

```bash
composer install
```

### 3. Instale as dependências do Node.js

```bash
npm install
```

---

## ⚙️ Configuração

### 1. Configure o arquivo de ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

### 2. Gere a chave da aplicação

```bash
php artisan key:generate
```

### 3. Configure o banco de dados PostgreSQL

Edite o arquivo `.env` e configure as credenciais do PostgreSQL:

```env
APP_NAME="Origo"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=origo_db
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 4. Crie o banco de dados

Acesse o PostgreSQL e crie o banco de dados:

```bash
psql -U postgres
```

Dentro do console do PostgreSQL:

```sql
CREATE DATABASE origo_db;
\q
```

### 5. Execute as migrations

```bash
php artisan migrate
```

### 6. (Opcional) Popule o banco com dados de teste

```bash
php artisan db:seed
```

---

## 🚀 Executando o Projeto

### Opção 1: Executar tudo de uma vez (Recomendado)

Use o script `composer dev` que executa simultaneamente:
- Servidor Laravel
- Fila de jobs
- Logs em tempo real
- Vite dev server (hot reload)

```bash
composer dev
```

Acesse a aplicação em: **http://localhost:8000**

### Opção 2: Executar manualmente em terminais separados

**Terminal 1 - Servidor Laravel:**
```bash
php artisan serve
```

**Terminal 2 - Vite (Frontend):**
```bash
npm run dev
```

**Terminal 3 - Fila de Jobs (opcional):**
```bash
php artisan queue:work
```

---

## 🔧 Desenvolvimento Backend

### Estrutura de Diretórios

```
app/
├── Actions/              # Ações de negócio (PublishCampaign, CreatePledge, etc)
├── Domain/              # Lógica de domínio
├── Http/
│   ├── Controllers/     # Controladores
│   ├── Middleware/      # Middlewares customizados
│   └── Requests/        # Form Requests para validação
├── Models/              # Models Eloquent
└── Services/            # Serviços reutilizáveis
```

### Criando um novo Model com Migration

```bash
php artisan make:model NomeDoModel -m
```

### Criando um Controller

```bash
php artisan make:controller NomeController
```

### Criando uma Action

```bash
php artisan make:class Actions/NomeDaAction
```

### Criando uma Migration

```bash
php artisan make:migration create_nome_table
```

### Executando Migrations

```bash
# Executar todas as migrations pendentes
php artisan migrate

# Reverter a última migration
php artisan migrate:rollback

# Resetar todas as migrations e executar novamente
php artisan migrate:fresh

# Resetar e popular com seeders
php artisan migrate:fresh --seed
```

### Criando Seeders

```bash
php artisan make:seeder NomeSeeder
```

### Rotas

As rotas estão organizadas em:
- `routes/web.php` - Rotas web principais
- `routes/auth.php` - Rotas de autenticação (Laravel Breeze)

### Boas Práticas Backend

1. **Use Actions** para lógica de negócio complexa
2. **Form Requests** para validação de dados
3. **Eloquent Relationships** para relacionamentos entre models
4. **Migrations** para todas as alterações no banco de dados
5. **Seeders** para dados de teste
6. **Queues** para processos demorados

---

## 🎨 Desenvolvimento Frontend

### Estrutura de Views

```
resources/
├── views/
│   ├── layouts/          # Layouts principais
│   │   ├── app.blade.php
│   │   └── guest.blade.php
│   ├── components/       # Componentes reutilizáveis
│   ├── campaigns/        # Views de campanhas
│   ├── dashboard/        # Views do dashboard
│   ├── auth/            # Views de autenticação
│   └── home.blade.php   # Página inicial
└── css/
    └── app.css          # Estilos customizados
```

### Tecnologias Frontend

- **Blade Templates** - Motor de templates do Laravel
- **Bootstrap 5** - Framework CSS
- **Alpine.js** - JavaScript reativo
- **Vite** - Build tool e hot reload

### Compilando Assets

```bash
# Desenvolvimento (com hot reload)
npm run dev

# Produção (minificado)
npm run build
```

### Criando um novo Componente Blade

```bash
php artisan make:component NomeDoComponente
```

Isso criará:
- `app/View/Components/NomeDoComponente.php` (classe)
- `resources/views/components/nome-do-componente.blade.php` (view)

### Usando Componentes

```blade
<x-nome-do-componente :prop="$valor" />
```

### Adicionando CSS Customizado

Edite `resources/css/app.css` e o Vite irá recompilar automaticamente.

### Adicionando JavaScript

Edite `resources/js/app.js` para adicionar lógica JavaScript customizada.

### Boas Práticas Frontend

1. **Componentes reutilizáveis** para elementos comuns
2. **Layouts** para estrutura consistente
3. **Alpine.js** para interatividade simples
4. **Bootstrap utilities** em vez de CSS customizado quando possível
5. **Vite** sempre rodando durante desenvolvimento para hot reload

---

## 📁 Estrutura do Projeto

```
origo/
├── app/                    # Código da aplicação
│   ├── Actions/           # Ações de negócio
│   ├── Domain/            # Lógica de domínio
│   ├── Http/              # Controllers, Middleware, Requests
│   ├── Models/            # Models Eloquent
│   └── Services/          # Serviços
├── bootstrap/             # Arquivos de inicialização
├── config/                # Arquivos de configuração
├── database/              # Migrations, Seeders, Factories
│   ├── migrations/
│   └── seeders/
├── public/                # Arquivos públicos (index.php, assets compilados)
├── resources/             # Views, CSS, JS
│   ├── css/
│   ├── js/
│   └── views/
├── routes/                # Definição de rotas
│   ├── web.php
│   └── auth.php
├── storage/               # Arquivos gerados (logs, cache, uploads)
├── tests/                 # Testes automatizados
├── .env                   # Variáveis de ambiente (não versionado)
├── .env.example           # Exemplo de variáveis de ambiente
├── composer.json          # Dependências PHP
├── package.json           # Dependências Node.js
└── vite.config.js         # Configuração do Vite
```

---

## 🧪 Testes

### Executando todos os testes

```bash
php artisan test
```

ou

```bash
composer test
```

### Executando testes específicos

```bash
php artisan test --filter NomeDoTeste
```

### Criando novos testes

```bash
# Teste de Feature
php artisan make:test NomeDoTesteTest

# Teste de Unit
php artisan make:test NomeDoTesteTest --unit
```

---

## 🐛 Troubleshooting

### Erro: "No application encryption key has been specified"

```bash
php artisan key:generate
```

### Erro: "SQLSTATE[HY000] [1045] Access denied for user"

Verifique as credenciais do banco de dados no arquivo `.env`.

### Erro: "Class 'App\Models\Campaign' not found"

Execute:
```bash
composer dump-autoload
```

### Vite não está recarregando automaticamente

1. Pare o servidor Vite (Ctrl+C)
2. Limpe o cache: `npm run build`
3. Reinicie: `npm run dev`

### Permissões de storage

Se houver erros de permissão:

```bash
chmod -R 775 storage bootstrap/cache
```

### Limpar caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Recriar o banco de dados do zero

```bash
php artisan migrate:fresh --seed
```

---

## 🤝 Contribuindo

### Workflow de Desenvolvimento

1. **Crie uma branch** para sua feature:
   ```bash
   git checkout -b feature/nome-da-feature
   ```

2. **Faça suas alterações** seguindo as boas práticas

3. **Commit suas mudanças**:
   ```bash
   git add .
   git commit -m "feat: descrição da feature"
   ```

4. **Push para o repositório**:
   ```bash
   git push origin feature/nome-da-feature
   ```

5. **Abra um Pull Request**

### Convenções de Commit

Seguimos o padrão [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Documentação
- `style:` Formatação de código
- `refactor:` Refatoração
- `test:` Testes
- `chore:` Tarefas de manutenção

### Code Style

O projeto usa **Laravel Pint** para formatação de código:

```bash
./vendor/bin/pint
```

---

## 📚 Recursos Adicionais

- [Documentação do Laravel](https://laravel.com/docs)
- [Laravel Breeze](https://laravel.com/docs/starter-kits#breeze)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.3/)
- [Alpine.js](https://alpinejs.dev/)
- [Vite](https://vitejs.dev/)

---

## 📄 Licença

Este projeto é proprietário e não é distribuído como open source.

---

## 👥 Autores

Desenvolvido como MVP de plataforma de crowdfunding com Laravel 12.

---

## 🎯 Próximos Passos

- [ ] Integração com gateway de pagamento real (Stripe, PayPal)
- [ ] Sistema de notificações por email
- [ ] Painel administrativo
- [ ] Sistema de comentários em campanhas
- [ ] Upload de múltiplas imagens
- [ ] Sistema de categorias
- [ ] Busca e filtros avançados
- [ ] API REST para mobile
- [ ] Testes automatizados completos
- [ ] Deploy em produção

---

**Dúvidas?** Fale com o time responsável.
