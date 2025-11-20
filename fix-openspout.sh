#!/bin/bash
# Script para corrigir versão do openspout no servidor Hostinger
# Execute após o deploy via Git

echo "🔧 Corrigindo versão do openspout para PHP 8.2..."

# Forçar versão compatível
composer require openspout/openspout:^4.23 --no-interaction --update-with-dependencies

if [ $? -eq 0 ]; then
    echo "✅ openspout corrigido para versão ^4.23"
    echo "📦 Reinstalando dependências..."
    composer install --no-dev --optimize-autoloader
    echo "✅ Dependências reinstaladas!"
else
    echo "❌ Erro ao corrigir openspout"
    exit 1
fi

