#!/bin/bash

# Script de Deploy para Produção - Equidade Plus
# Uso: ./deploy.sh

set -e  # Parar em caso de erro

# Configurar log de deploy
LOG_FILE="storage/logs/deploy.log"
mkdir -p "$(dirname "$LOG_FILE")"

# Função para log
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "🚀 Iniciando deploy do Equidade Plus..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar se está no diretório correto
if [ ! -f "artisan" ]; then
    log "${RED}❌ Erro: Execute este script no diretório raiz do projeto Laravel${NC}"
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
    log "${RED}❌ ERRO CRÍTICO: APP_DEBUG está como 'true' em produção!${NC}"
    log "${YELLOW}Altere para APP_DEBUG=false no arquivo .env${NC}"
    exit 1
fi

# Verificar se APP_ENV está como production
if ! grep -q "APP_ENV=production" .env; then
    log "${YELLOW}⚠️  AVISO: APP_ENV não está configurado como 'production'${NC}"
fi

log "${GREEN}✓ Verificações iniciais concluídas${NC}"

# Atualizar código (se usando Git)
if [ -d ".git" ]; then
    log "${GREEN}📥 Atualizando código do repositório...${NC}"
    git pull origin main || git pull origin master || log "${YELLOW}⚠️  Não foi possível fazer pull do Git${NC}"
    log "Branch atual: $(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo 'N/A')"
    log "Último commit: $(git log -1 --pretty=format:'%h - %s' 2>/dev/null || echo 'N/A')"
fi

# Instalar/Atualizar dependências do Composer
log "${GREEN}📦 Instalando dependências do Composer...${NC}"
composer install --optimize-autoloader --no-dev --no-interaction || {
    log "${RED}❌ Erro ao instalar dependências do Composer${NC}"
    exit 1
}

# Instalar/Atualizar dependências do NPM
log "${GREEN}📦 Instalando dependências do NPM...${NC}"
if command -v npm &> /dev/null; then
    npm ci || {
        log "${RED}❌ Erro ao instalar dependências do NPM${NC}"
        exit 1
    }
else
    log "${YELLOW}⚠️  NPM não encontrado, pulando instalação de dependências frontend${NC}"
fi

# Compilar assets
log "${GREEN}🔨 Compilando assets de produção...${NC}"
if command -v npm &> /dev/null; then
    npm run build || {
        log "${RED}❌ Erro ao compilar assets${NC}"
        exit 1
    }
    log "${GREEN}🧽 Removendo dependências de desenvolvimento do NPM...${NC}"
    npm prune --production 2>/dev/null || true
else
    log "${YELLOW}⚠️  NPM não encontrado, pulando build de assets${NC}"
fi

# Executar migrations
log "${GREEN}🗄️  Executando migrations...${NC}"
php artisan migrate --force || {
    log "${RED}❌ Erro ao executar migrations${NC}"
    exit 1
}

# Limpar caches antigos
log "${GREEN}🧹 Limpando caches...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan event:clear

# Cachear para produção
log "${GREEN}⚡ Cacheando configurações para produção...${NC}"
php artisan config:cache || {
    log "${RED}❌ Erro ao cachear configurações${NC}"
    exit 1
}
php artisan route:cache || {
    log "${YELLOW}⚠️  Aviso: Erro ao cachear rotas${NC}"
}
php artisan view:cache || {
    log "${YELLOW}⚠️  Aviso: Erro ao cachear views${NC}"
}
php artisan event:cache || {
    log "${YELLOW}⚠️  Aviso: Erro ao cachear eventos${NC}"
}

# Criar link simbólico do storage (se não existir)
if [ ! -L "public/storage" ]; then
    log "${GREEN}🔗 Criando link simbólico do storage...${NC}"
    php artisan storage:link || log "${YELLOW}⚠️  Link do storage já existe ou erro ao criar${NC}"
fi

# Verificar permissões
log "${GREEN}🔐 Verificando permissões...${NC}"
chmod -R 775 storage bootstrap/cache 2>/dev/null || log "${YELLOW}⚠️  Aviso: Não foi possível ajustar permissões${NC}"
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || log "${YELLOW}⚠️  Nota: Ajuste as permissões manualmente se necessário${NC}"

# Reiniciar serviços (se Supervisor estiver instalado)
if command -v supervisorctl &> /dev/null; then
    log "${GREEN}🔄 Reiniciando workers do Supervisor...${NC}"
    sudo supervisorctl restart laravel-worker:* 2>/dev/null || log "${YELLOW}Workers não configurados${NC}"
    sudo supervisorctl restart laravel-reverb:* 2>/dev/null || log "${YELLOW}Reverb não configurado${NC}"
fi

# Recarregar PHP-FPM (se estiver instalado)
if command -v systemctl &> /dev/null; then
    if systemctl is-active --quiet php*-fpm; then
        log "${GREEN}🔄 Recarregando PHP-FPM...${NC}"
        sudo systemctl reload php*-fpm 2>/dev/null || log "${YELLOW}PHP-FPM não encontrado${NC}"
    fi
fi

log "${GREEN}✅ Deploy concluído com sucesso!${NC}"
log "${YELLOW}📝 Próximos passos:${NC}"
log "   1. Verifique os logs: tail -f storage/logs/laravel.log"
log "   2. Teste a aplicação no navegador"
log "   3. Verifique se os workers estão rodando"
log "   4. Verifique se o Reverb está rodando (se aplicável)"
log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

