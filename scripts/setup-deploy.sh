#!/bin/bash

# Script de Setup Inicial para Deploy Automático
# Uso: ./scripts/setup-deploy.sh

set -e

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 Configurando Deploy Automático para Hostinger${NC}"
echo ""

# Verificar se está no diretório correto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Erro: Execute este script no diretório raiz do projeto${NC}"
    exit 1
fi

# 1. Gerar token de deploy
echo -e "${GREEN}1. Gerando token de deploy...${NC}"
DEPLOY_TOKEN=$(php -r "echo bin2hex(random_bytes(32));")
echo -e "${YELLOW}Token gerado: ${DEPLOY_TOKEN}${NC}"
echo ""

# 2. Adicionar ao .env
echo -e "${GREEN}2. Adicionando DEPLOY_TOKEN ao .env...${NC}"
if [ -f ".env" ]; then
    # Remover linha antiga se existir
    sed -i.bak '/^DEPLOY_TOKEN=/d' .env
    
    # Adicionar novo token
    echo "" >> .env
    echo "# Token para deploy automático via webhook" >> .env
    echo "DEPLOY_TOKEN=${DEPLOY_TOKEN}" >> .env
    echo -e "${GREEN}✓ Token adicionado ao .env${NC}"
else
    echo -e "${RED}❌ Arquivo .env não encontrado!${NC}"
    exit 1
fi

# 3. Tornar deploy.sh executável
echo -e "${GREEN}3. Configurando permissões do deploy.sh...${NC}"
chmod +x deploy.sh
echo -e "${GREEN}✓ deploy.sh está executável${NC}"

# 4. Verificar se routes/deploy.php existe
echo -e "${GREEN}4. Verificando rotas de deploy...${NC}"
if [ -f "routes/deploy.php" ]; then
    echo -e "${GREEN}✓ Rota de deploy configurada${NC}"
else
    echo -e "${RED}❌ routes/deploy.php não encontrado!${NC}"
    exit 1
fi

# 5. Verificar se DeployController existe
echo -e "${GREEN}5. Verificando DeployController...${NC}"
if [ -f "app/Http/Controllers/DeployController.php" ]; then
    echo -e "${GREEN}✓ DeployController encontrado${NC}"
else
    echo -e "${RED}❌ DeployController não encontrado!${NC}"
    exit 1
fi

# 6. Testar rota (se aplicação estiver rodando)
echo -e "${GREEN}6. Testando configuração...${NC}"
php artisan route:list | grep -q "deploy.webhook" && echo -e "${GREEN}✓ Rota registrada corretamente${NC}" || echo -e "${YELLOW}⚠️  Rota não encontrada (execute: php artisan route:cache)${NC}"

echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ Setup concluído com sucesso!${NC}"
echo ""
echo -e "${YELLOW}📋 Próximos passos:${NC}"
echo ""
echo -e "${BLUE}1. Configure o webhook no seu repositório Git:${NC}"
echo "   - URL: https://seu-dominio.com/deploy"
echo "   - Content-Type: application/json"
echo "   - Event: Push"
echo "   - Header customizado:"
echo "     * Name: X-Deploy-Token"
echo "     * Value: ${DEPLOY_TOKEN}"
echo ""
echo -e "${BLUE}2. Teste o deploy manualmente:${NC}"
echo "   curl -X POST https://seu-dominio.com/deploy \\"
echo "     -H 'X-Deploy-Token: ${DEPLOY_TOKEN}' \\"
echo "     -H 'Content-Type: application/json'"
echo ""
echo -e "${BLUE}3. Verifique os logs:${NC}"
echo "   tail -f storage/logs/deploy.log"
echo ""
echo -e "${YELLOW}⚠️  IMPORTANTE: Guarde este token em local seguro!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

