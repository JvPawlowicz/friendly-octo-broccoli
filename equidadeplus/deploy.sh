#!/bin/bash

# Script de Deploy para Produção - Equidade Plus
# Uso: ./deploy.sh

set -e  # Parar em caso de erro

echo "🚀 Iniciando deploy do Equidade Plus..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar se está no diretório correto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Erro: Execute este script no diretório raiz do projeto Laravel${NC}"
    exit 1
fi

# Verificar se .env existe
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}⚠️  Arquivo .env não encontrado. Copiando .env.example...${NC}"
    cp .env.example .env
    echo -e "${RED}❌ IMPORTANTE: Configure o arquivo .env antes de continuar!${NC}"
    exit 1
fi

# Verificar se APP_DEBUG está false
if grep -q "APP_DEBUG=true" .env; then
    echo -e "${RED}❌ ERRO CRÍTICO: APP_DEBUG está como 'true' em produção!${NC}"
    echo -e "${YELLOW}Altere para APP_DEBUG=false no arquivo .env${NC}"
    exit 1
fi

# Verificar se APP_ENV está como production
if ! grep -q "APP_ENV=production" .env; then
    echo -e "${YELLOW}⚠️  AVISO: APP_ENV não está configurado como 'production'${NC}"
fi

echo -e "${GREEN}✓ Verificações iniciais concluídas${NC}"

# Atualizar código (se usando Git)
if [ -d ".git" ]; then
    echo -e "${GREEN}📥 Atualizando código do repositório...${NC}"
    git pull origin main || git pull origin master
fi

# Instalar/Atualizar dependências do Composer
echo -e "${GREEN}📦 Instalando dependências do Composer...${NC}"
composer install --optimize-autoloader --no-dev --no-interaction

# Instalar/Atualizar dependências do NPM
echo -e "${GREEN}📦 Instalando dependências do NPM...${NC}"
npm ci

# Compilar assets
echo -e "${GREEN}🔨 Compilando assets de produção...${NC}"
npm run build
echo -e "${GREEN}🧽 Removendo dependências de desenvolvimento do NPM...${NC}"
npm prune --production

# Executar migrations
echo -e "${GREEN}🗄️  Executando migrations...${NC}"
php artisan migrate --force

# Limpar caches antigos
echo -e "${GREEN}🧹 Limpando caches...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan event:clear

# Cachear para produção
echo -e "${GREEN}⚡ Cacheando configurações para produção...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Criar link simbólico do storage (se não existir)
if [ ! -L "public/storage" ]; then
    echo -e "${GREEN}🔗 Criando link simbólico do storage...${NC}"
    php artisan storage:link
fi

# Verificar permissões
echo -e "${GREEN}🔐 Verificando permissões...${NC}"
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || echo "Nota: Ajuste as permissões manualmente se necessário"

# Reiniciar serviços (se Supervisor estiver instalado)
if command -v supervisorctl &> /dev/null; then
    echo -e "${GREEN}🔄 Reiniciando workers do Supervisor...${NC}"
    sudo supervisorctl restart laravel-worker:* 2>/dev/null || echo "Workers não configurados"
    sudo supervisorctl restart laravel-reverb:* 2>/dev/null || echo "Reverb não configurado"
fi

# Recarregar PHP-FPM (se estiver instalado)
if command -v systemctl &> /dev/null; then
    if systemctl is-active --quiet php*-fpm; then
        echo -e "${GREEN}🔄 Recarregando PHP-FPM...${NC}"
        sudo systemctl reload php*-fpm 2>/dev/null || echo "PHP-FPM não encontrado"
    fi
fi

echo -e "${GREEN}✅ Deploy concluído com sucesso!${NC}"
echo -e "${YELLOW}📝 Próximos passos:${NC}"
echo "   1. Verifique os logs: tail -f storage/logs/laravel.log"
echo "   2. Teste a aplicação no navegador"
echo "   3. Verifique se os workers estão rodando"
echo "   4. Verifique se o Reverb está rodando (se aplicável)"

