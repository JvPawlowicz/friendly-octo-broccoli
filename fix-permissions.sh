#!/bin/bash

# Script para corrigir permissões de arquivos e diretórios
# Execute: chmod +x fix-permissions.sh && ./fix-permissions.sh

echo "🔐 Corrigindo permissões de arquivos e diretórios..."

# Diretório do projeto
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR" || exit 1

# Criar diretórios se não existirem
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Definir permissões para diretórios
echo "📁 Configurando permissões de diretórios..."
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

# Permissões específicas
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Se estiver em produção e tiver acesso ao usuário www-data
if command -v whoami &> /dev/null; then
    CURRENT_USER=$(whoami)
    if [ "$CURRENT_USER" = "root" ] || [ "$CURRENT_USER" = "www-data" ]; then
        echo "👤 Configurando proprietário (www-data)..."
        chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || {
            echo "⚠️  Não foi possível alterar o proprietário. Execute como root ou ajuste manualmente."
        }
    else
        echo "ℹ️  Execute como root para alterar o proprietário para www-data"
    fi
fi

# Verificar se storage/app/public está linkado
if [ ! -L public/storage ]; then
    echo "🔗 Criando link simbólico para storage público..."
    php artisan storage:link 2>/dev/null || {
        echo "⚠️  Não foi possível criar o link simbólico. Execute: php artisan storage:link"
    }
fi

echo "✅ Permissões configuradas com sucesso!"
echo ""
echo "📋 Resumo:"
echo "   - Diretórios: 775"
echo "   - Arquivos: 664"
echo "   - storage/: 775"
echo "   - bootstrap/cache/: 775"

