#!/bin/bash

# Script para preparar arquivos para deploy via File Manager (Hostinger)
# Uso: ./scripts/prepare-filemanager-deploy.sh

set -e

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}📦 Preparando arquivos para deploy via File Manager...${NC}"
echo ""

# Diretório de destino
DEPLOY_DIR="deploy-filemanager"
PROJECT_NAME="equidade-vps"

# Limpar diretório anterior se existir
if [ -d "$DEPLOY_DIR" ]; then
    echo -e "${YELLOW}🧹 Limpando diretório anterior...${NC}"
    rm -rf "$DEPLOY_DIR"
fi

# Criar diretório
mkdir -p "$DEPLOY_DIR"
echo -e "${GREEN}✓ Diretório criado: $DEPLOY_DIR${NC}"

# Lista de arquivos/diretórios a copiar
echo -e "${BLUE}📋 Copiando arquivos do projeto...${NC}"

# Copiar estrutura principal
rsync -av \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='.git' \
    --exclude='.env' \
    --exclude='.env.*' \
    --exclude='*.log' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/app/backups/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='bootstrap/cache/*' \
    --exclude='public/build' \
    --exclude='public/hot' \
    --exclude='.DS_Store' \
    --exclude='Thumbs.db' \
    --exclude='*.tmp' \
    --exclude='*.temp' \
    --exclude='deploy-filemanager' \
    --exclude='.idea' \
    --exclude='.vscode' \
    --exclude='.fleet' \
    --exclude='*.zip' \
    --exclude='*.tar.gz' \
    ./ "$DEPLOY_DIR/"

echo -e "${GREEN}✓ Arquivos copiados${NC}"

# Criar estrutura de diretórios necessária
echo -e "${BLUE}📁 Criando estrutura de diretórios...${NC}"
mkdir -p "$DEPLOY_DIR/storage/logs"
mkdir -p "$DEPLOY_DIR/storage/framework/cache/data"
mkdir -p "$DEPLOY_DIR/storage/framework/sessions"
mkdir -p "$DEPLOY_DIR/storage/framework/views"
mkdir -p "$DEPLOY_DIR/storage/app/backups"
mkdir -p "$DEPLOY_DIR/storage/app/public"
mkdir -p "$DEPLOY_DIR/bootstrap/cache"
mkdir -p "$DEPLOY_DIR/public/images"

# Criar arquivos .gitkeep para manter estrutura
touch "$DEPLOY_DIR/storage/logs/.gitkeep"
touch "$DEPLOY_DIR/storage/framework/cache/data/.gitkeep"
touch "$DEPLOY_DIR/storage/framework/sessions/.gitkeep"
touch "$DEPLOY_DIR/storage/framework/views/.gitkeep"
touch "$DEPLOY_DIR/storage/app/backups/.gitkeep"
touch "$DEPLOY_DIR/bootstrap/cache/.gitkeep"
touch "$DEPLOY_DIR/public/images/.gitkeep"

echo -e "${GREEN}✓ Estrutura de diretórios criada${NC}"

# Criar .env.example se não existir
if [ ! -f "$DEPLOY_DIR/.env.example" ]; then
    echo -e "${YELLOW}⚠️  .env.example não encontrado, criando...${NC}"
    cp "$DEPLOY_DIR/.env.example" "$DEPLOY_DIR/.env.example" 2>/dev/null || echo "# Arquivo .env.example será criado no servidor" > "$DEPLOY_DIR/.env.example"
fi

# Criar arquivo de instruções
cat > "$DEPLOY_DIR/INSTRUCOES-DEPLOY.md" << 'EOF'
# 📦 Instruções de Deploy via File Manager - Hostinger

Este pacote contém todos os arquivos necessários para fazer deploy do sistema Equidade via File Manager da Hostinger.

## 📋 Pré-requisitos

- Conta na Hostinger com acesso ao hPanel
- Acesso SSH habilitado (necessário para comandos)
- Banco de dados MySQL criado
- PHP 8.2+ instalado

## 🚀 Passo a Passo

### 1. Upload dos Arquivos

1. Acesse o **hPanel** da Hostinger
2. Vá em **File Manager**
3. Navegue até `public_html` (ou `domains/seu-dominio.com/public_html`)
4. **Faça upload de TODOS os arquivos desta pasta** para o `public_html`
   - Você pode fazer upload de arquivo por arquivo
   - OU fazer upload de um ZIP e extrair no servidor

### 2. Configurar .env

1. No File Manager, localize o arquivo `.env.example`
2. Renomeie para `.env`
3. Edite o arquivo `.env` e configure:
   - `APP_URL=https://seu-dominio.com`
   - `DB_DATABASE=nome_do_banco`
   - `DB_USERNAME=usuario_banco`
   - `DB_PASSWORD=senha_banco`
   - Outras configurações necessárias

### 3. Acessar via SSH (Obrigatório)

Você precisará acessar via SSH para executar comandos. No hPanel:
1. Vá em **SSH Access**
2. Copie as credenciais SSH
3. Conecte via terminal:
   ```bash
   ssh usuario@seu-dominio.com
   ```

### 4. Instalar Dependências

No servidor, via SSH:

```bash
cd ~/domains/seu-dominio.com/public_html
# OU
cd ~/public_html

# Instalar dependências PHP
composer install --no-dev --optimize-autoloader

# Instalar dependências Node (se disponível)
npm install
npm run build
```

### 5. Configurar Aplicação

```bash
# Gerar chave da aplicação
php artisan key:generate

# Executar migrations
php artisan migrate --force

# Executar seeders (apenas na primeira instalação)
php artisan db:seed --force
```

### 6. Configurar Permissões

```bash
# Dar permissões de escrita
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Criar Link Simbólico do Storage

```bash
php artisan storage:link
```

### 8. Otimizar para Produção

```bash
# Cachear configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otimizar autoloader
composer dump-autoload --optimize
```

### 9. Configurar Cron Job

No hPanel, vá em **Cron Jobs** e adicione:

```
* * * * * cd /caminho/para/public_html && php artisan schedule:run >> /dev/null 2>&1
```

Substitua `/caminho/para/public_html` pelo caminho completo do seu projeto.

### 10. Testar

1. Acesse: `https://seu-dominio.com`
2. Verifique o health check: `https://seu-dominio.com/up`
3. Faça login com:
   - **Admin**: admin@equidade.test / Admin123!
   - **Coordenador**: coordenacao@equidade.test / Coordenador123!

⚠️ **IMPORTANTE**: Altere as senhas padrão após o primeiro login!

## 🔄 Atualizações Futuras

Para atualizar o sistema no futuro:

1. Faça upload dos novos arquivos via File Manager
2. Via SSH, execute:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   php artisan migrate --force
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 🆘 Problemas Comuns

### Erro 500
- Verifique permissões: `chmod -R 755 storage bootstrap/cache`
- Verifique logs: `storage/logs/laravel.log`
- Limpe cache: `php artisan optimize:clear`

### Erro de Permissão
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Banco de Dados não Conecta
- Verifique credenciais no `.env`
- Verifique se o banco existe no hPanel

## 📞 Suporte

Consulte a documentação completa em: `docs/deploy/hostinger.md`
EOF

echo -e "${GREEN}✓ Arquivo de instruções criado${NC}"

# Criar arquivo .htaccess na raiz (se não existir)
if [ ! -f "$DEPLOY_DIR/.htaccess" ]; then
    cat > "$DEPLOY_DIR/.htaccess" << 'HTACCESS'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Proteger arquivos sensíveis
<FilesMatch "^(\.env|\.git|composer\.(json|lock)|package\.(json|lock))$">
    Order allow,deny
    Deny from all
</FilesMatch>
HTACCESS
    echo -e "${GREEN}✓ .htaccess criado${NC}"
fi

# Criar checklist de deploy
cat > "$DEPLOY_DIR/CHECKLIST-DEPLOY.md" << 'EOF'
# ✅ Checklist de Deploy

Use este checklist para garantir que tudo foi configurado corretamente:

## Antes do Upload
- [ ] Todos os arquivos estão na pasta `deploy-filemanager`
- [ ] Arquivo `.env.example` está presente
- [ ] Estrutura de diretórios está completa

## Após Upload
- [ ] Todos os arquivos foram enviados para `public_html`
- [ ] Arquivo `.env` foi criado e configurado
- [ ] Permissões foram ajustadas (755 para storage e bootstrap/cache)

## Configuração
- [ ] Dependências instaladas (`composer install`)
- [ ] Assets compilados (`npm run build`)
- [ ] Chave gerada (`php artisan key:generate`)
- [ ] Migrations executadas (`php artisan migrate`)
- [ ] Seeders executados (`php artisan db:seed`)
- [ ] Link do storage criado (`php artisan storage:link`)
- [ ] Caches criados (`php artisan config:cache`)

## Testes
- [ ] Site acessível em `https://seu-dominio.com`
- [ ] Health check funciona: `https://seu-dominio.com/up`
- [ ] Login funciona com usuários padrão
- [ ] Dashboard carrega corretamente
- [ ] Agenda funciona
- [ ] Sem erros nos logs

## Segurança
- [ ] Senhas padrão foram alteradas
- [ ] SSL/HTTPS está ativo
- [ ] `.env` não está acessível publicamente
- [ ] Arquivos sensíveis estão protegidos

## Produção
- [ ] Cron job configurado
- [ ] Backup automático configurado
- [ ] Logs sendo monitorados
- [ ] Sentry configurado (opcional)
EOF

echo -e "${GREEN}✓ Checklist criado${NC}"

# Criar script de pós-instalação
cat > "$DEPLOY_DIR/post-install.sh" << 'EOF'
#!/bin/bash

# Script de Pós-Instalação - Execute após fazer upload dos arquivos
# Uso: bash post-install.sh

echo "🚀 Configurando sistema Equidade..."

# Verificar se está no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório raiz do projeto Laravel"
    exit 1
fi

# Verificar .env
if [ ! -f ".env" ]; then
    echo "⚠️  Arquivo .env não encontrado. Copiando .env.example..."
    cp .env.example .env
    echo "❌ IMPORTANTE: Configure o arquivo .env antes de continuar!"
    exit 1
fi

# Instalar dependências
echo "📦 Instalando dependências do Composer..."
composer install --no-dev --optimize-autoloader

# Instalar dependências Node (se disponível)
if command -v npm &> /dev/null; then
    echo "📦 Instalando dependências do NPM..."
    npm install
    npm run build
else
    echo "⚠️  NPM não encontrado, pulando build de assets"
fi

# Gerar chave
echo "🔑 Gerando chave da aplicação..."
php artisan key:generate

# Executar migrations
echo "🗄️  Executando migrations..."
php artisan migrate --force

# Executar seeders
echo "🌱 Executando seeders..."
php artisan db:seed --force

# Criar link do storage
echo "🔗 Criando link do storage..."
php artisan storage:link

# Configurar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || echo "⚠️  Ajuste permissões manualmente"

# Cachear
echo "⚡ Cacheando configurações..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Configuração concluída!"
echo ""
echo "📝 Próximos passos:"
echo "   1. Configure o cron job no hPanel"
echo "   2. Teste o sistema: https://seu-dominio.com"
echo "   3. Altere as senhas padrão"
EOF

chmod +x "$DEPLOY_DIR/post-install.sh"
echo -e "${GREEN}✓ Script de pós-instalação criado${NC}"

# Criar ZIP (opcional)
echo ""
read -p "Deseja criar um arquivo ZIP para facilitar o upload? (s/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[SsYy]$ ]]; then
    echo -e "${BLUE}📦 Criando arquivo ZIP...${NC}"
    cd "$DEPLOY_DIR"
    zip -r "../${PROJECT_NAME}-filemanager-deploy.zip" . \
        -x "*.git*" \
        -x "*.DS_Store" \
        -x "*.log" \
        > /dev/null 2>&1
    cd ..
    echo -e "${GREEN}✓ ZIP criado: ${PROJECT_NAME}-filemanager-deploy.zip${NC}"
fi

# Resumo
echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ Preparação concluída!${NC}"
echo ""
echo -e "${BLUE}📁 Arquivos prontos em: ${DEPLOY_DIR}/${NC}"
echo ""
echo -e "${YELLOW}📋 Próximos passos:${NC}"
echo "   1. Acesse o File Manager no hPanel"
echo "   2. Faça upload de TODOS os arquivos da pasta '${DEPLOY_DIR}'"
echo "   3. Siga as instruções em: ${DEPLOY_DIR}/INSTRUCOES-DEPLOY.md"
echo "   4. Execute o script: bash post-install.sh (via SSH)"
echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

