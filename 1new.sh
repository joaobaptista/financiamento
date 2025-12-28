#!/bin/bash

# ==========================
# CONFIGURAÇÕES
# ==========================
REPO_URL="https://github.com/joaobaptista/financiamento.git"
DOMAIN="origocrowd.com.br"
PROJECT_DIR="/var/www/$DOMAIN"
BRANCH="main"

echo "🚀 Iniciando setup/update do projeto"

# ==========================
# VERIFICA SE DIRETÓRIO EXISTE
# ==========================
if [ ! -d "$PROJECT_DIR" ]; then
  echo "📁 Criando diretório $PROJECT_DIR"
  mkdir -p "$PROJECT_DIR"
fi

cd "$PROJECT_DIR" || exit 1

# ==========================
# CLONE OU UPDATE
# ==========================
if [ ! -d ".git" ]; then
  echo "📥 Repositório não encontrado. Clonando..."
  git clone "$REPO_URL" .
else
  echo "🔄 Repositório encontrado. Atualizando..."
  git fetch origin
  git checkout $BRANCH
  git pull origin $BRANCH
fi

# ==========================
# PERMISSÕES (Laravel)
# ==========================
echo "🔐 Ajustando permissões"
chown -R deploy:www-data "$PROJECT_DIR"
chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"

# ==========================
# EXECUTAR INSTALL.SH
# ==========================
if [ -f "install.sh" ]; then
  echo "⚙️ Executando install.sh"
  chmod +x install.sh
  ./install.sh
else
  echo "⚠️ install.sh não encontrado"
fi

echo "✅ Processo finalizado com sucesso!"
