#!/bin/bash
# Script completo para corrigir problemas após deploy via Git na Hostinger
# Execute após o deploy falhar ou após o clone do repositório

cd ~/domains/sistemagrupoequidade.net/public_html

echo "🔧 Corrigindo problemas de deploy..."

# 1. Corrigir openspout para PHP 8.2
echo "📦 Passo 1: Corrigindo versão do openspout..."
composer require openspout/openspout:^4.23 --no-interaction --update-with-dependencies

# 2. Instalar dependências
echo "📦 Passo 2: Instalando dependências..."
composer install --no-dev --optimize-autoloader

# 3. Corrigir AppServiceProvider se necessário
if grep -q "!in_array('key:generate'" app/Providers/AppServiceProvider.php; then
    echo "✅ AppServiceProvider já está correto"
else
    echo "🔧 Corrigindo AppServiceProvider..."
    # Backup
    cp app/Providers/AppServiceProvider.php app/Providers/AppServiceProvider.php.backup
    # A correção será feita manualmente ou via script específico
fi

# 4. Gerar APP_KEY se não existir
if [ -z "$(grep 'APP_KEY=' .env | cut -d '=' -f2)" ] || [ "$(grep 'APP_KEY=' .env | cut -d '=' -f2)" = "" ]; then
    echo "🔑 Passo 3: Gerando APP_KEY..."
    php artisan key:generate
else
    echo "✅ APP_KEY já configurada"
fi

# 5. Executar migrations
echo "🗄️  Passo 4: Executando migrations..."
php artisan migrate --force

# 6. Executar seeders
echo "🌱 Passo 5: Executando seeders..."
php artisan db:seed --force

# 7. Criar link do storage
echo "🔗 Passo 6: Criando link do storage..."
php artisan storage:link

# 8. Cachear
echo "⚡ Passo 7: Cacheando configurações..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ Deploy corrigido e finalizado!"

