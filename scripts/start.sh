#!/bin/bash

set -euo pipefail

echo ">> Preparando diretórios de cache e storage"
mkdir -p storage/app/public
mkdir -p storage/app/private
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

if [ ! -L public/storage ]; then
  echo ">> Criando link simbólico public/storage"
  php artisan storage:link
fi

echo ">> Ajustando permissões"
chmod -R 775 storage bootstrap/cache || true

echo ">> Executando migrations (idempotente)"
php artisan migrate --force

echo ">> Otimizando caches"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo ">> Iniciando servidor Laravel"
exec php artisan serve --host 0.0.0.0 --port "${PORT:-3000}"


