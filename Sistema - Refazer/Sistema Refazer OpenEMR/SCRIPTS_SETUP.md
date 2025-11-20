# Scripts de Setup e Configuração

## 📋 Visão Geral

Este documento contém scripts úteis para setup, configuração e manutenção do OpenEMR customizado.

## 🚀 Scripts de Inicialização

### 1. `setup-openemr.sh`

Script principal de setup inicial do OpenEMR.

```bash
#!/bin/bash
# setup-openemr.sh
# Script de setup inicial do OpenEMR

set -e

echo "🚀 Configurando OpenEMR..."

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Verificar se está no diretório correto
if [ ! -f "interface/main/main.php" ]; then
    echo -e "${RED}❌ Erro: Execute este script no diretório raiz do OpenEMR${NC}"
    exit 1
fi

# Criar diretórios necessários
echo -e "${GREEN}📁 Criando diretórios...${NC}"
mkdir -p sites/default/documents
mkdir -p sites/default/documents/cache
mkdir -p sites/default/documents/temp
mkdir -p sites/default/documents/edi
mkdir -p sites/default/documents/era
mkdir -p sites/default/documents/letter_templates
mkdir -p sites/default/documents/onsite_portal_documents
mkdir -p sites/default/documents/procedure_results

# Configurar permissões
echo -e "${GREEN}🔐 Configurando permissões...${NC}"
chmod -R 755 sites/
chmod -R 700 sites/default/documents
chown -R www-data:www-data sites/ 2>/dev/null || echo "Nota: Ajuste permissões manualmente se necessário"

# Verificar variáveis de ambiente
echo -e "${GREEN}🔍 Verificando variáveis de ambiente...${NC}"
if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo -e "${YELLOW}⚠️  Variáveis de banco de dados não configuradas${NC}"
    echo "Configure: DB_HOST, DB_NAME, DB_USER, DB_PASS"
fi

echo -e "${GREEN}✅ Setup concluído!${NC}"
```

---

### 2. `disable-modules.sh`

Script para desativar módulos via banco de dados.

```bash
#!/bin/bash
# disable-modules.sh
# Desativa módulos do OpenEMR via banco de dados

set -e

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

# Verificar variáveis de banco
if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo -e "${RED}❌ Configure variáveis DB_HOST, DB_NAME, DB_USER, DB_PASS${NC}"
    exit 1
fi

# Módulos a desativar
MODULES=(
    "billing"
    "prescriptions"
    "labs"
    "imaging"
    "pharmacy"
    "telemedicine"
)

echo -e "${GREEN}🗑️  Desativando módulos...${NC}"

for module in "${MODULES[@]}"; do
    echo "Desativando: $module"
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" <<EOF
UPDATE registry SET state = 0 WHERE directory = '$module';
DELETE FROM globals WHERE gl_name LIKE '${module}_%';
EOF
    echo "✅ $module desativado"
done

echo -e "${GREEN}✅ Módulos desativados com sucesso!${NC}"
```

---

### 3. `remove-modules.sh`

Script para remover arquivos de módulos desativados.

```bash
#!/bin/bash
# remove-modules.sh
# Remove arquivos de módulos desativados (APENAS APÓS TESTES)

set -e

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}⚠️  ATENÇÃO: Este script remove arquivos permanentemente!${NC}"
read -p "Tem certeza que deseja continuar? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Operação cancelada."
    exit 0
fi

# Fazer backup primeiro
echo -e "${GREEN}💾 Criando backup...${NC}"
BACKUP_DIR="backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Módulos a remover
MODULES=(
    "billing"
    "prescriptions"
    "labs"
    "imaging"
    "pharmacy"
    "telemedicine"
)

for module in "${MODULES[@]}"; do
    if [ -d "interface/$module" ]; then
        echo "Fazendo backup de: interface/$module"
        cp -r "interface/$module" "$BACKUP_DIR/$module"
        echo "Removendo: interface/$module"
        rm -rf "interface/$module"
        echo "✅ $module removido"
    fi
done

echo -e "${GREEN}✅ Módulos removidos! Backup salvo em: $BACKUP_DIR${NC}"
```

---

## 🗄️ Scripts de Banco de Dados

### 4. `setup-database.sh`

Script para configurar banco de dados inicial.

```bash
#!/bin/bash
# setup-database.sh
# Configura banco de dados do OpenEMR

set -e

# Verificar variáveis
if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo "❌ Configure variáveis DB_HOST, DB_NAME, DB_USER, DB_PASS"
    exit 1
fi

echo "🗄️  Configurando banco de dados..."

# Criar tabela de unidades (se não existir)
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" <<EOF
-- Criar tabela de unidades
CREATE TABLE IF NOT EXISTS units (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(255),
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Adicionar unit_id aos usuários (se não existir)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS unit_id INT,
ADD INDEX IF NOT EXISTS idx_unit_id (unit_id);

-- Adicionar unit_id aos appointments (se não existir)
ALTER TABLE openemr_postcalendar_events 
ADD COLUMN IF NOT EXISTS unit_id INT,
ADD INDEX IF NOT EXISTS idx_unit_id (unit_id);

-- Adicionar unit_id aos patients (se não existir)
ALTER TABLE patient_data 
ADD COLUMN IF NOT EXISTS unit_id INT,
ADD INDEX IF NOT EXISTS idx_unit_id (unit_id);
EOF

echo "✅ Banco de dados configurado!"
```

---

### 5. `backup-database.sh`

Script para backup do banco de dados.

```bash
#!/bin/bash
# backup-database.sh
# Faz backup do banco de dados

set -e

# Verificar variáveis
if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo "❌ Configure variáveis DB_HOST, DB_NAME, DB_USER, DB_PASS"
    exit 1
fi

# Criar diretório de backups
BACKUP_DIR="backups"
mkdir -p "$BACKUP_DIR"

# Nome do arquivo de backup
BACKUP_FILE="$BACKUP_DIR/openemr_$(date +%Y%m%d_%H%M%S).sql"

echo "💾 Fazendo backup do banco de dados..."
mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"

# Comprimir
gzip "$BACKUP_FILE"

echo "✅ Backup criado: ${BACKUP_FILE}.gz"

# Manter apenas últimos 7 backups
echo "🧹 Limpando backups antigos..."
ls -t "$BACKUP_DIR"/*.sql.gz | tail -n +8 | xargs rm -f

echo "✅ Limpeza concluída!"
```

---

## 🔧 Scripts de Configuração

### 6. `configure-openemr.sh`

Script para configurar OpenEMR após instalação.

```bash
#!/bin/bash
# configure-openemr.sh
# Configura OpenEMR após instalação inicial

set -e

# Verificar variáveis
if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo "❌ Configure variáveis DB_HOST, DB_NAME, DB_USER, DB_PASS"
    exit 1
fi

echo "⚙️  Configurando OpenEMR..."

# Configurações via SQL
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" <<EOF
-- Desabilitar módulos não utilizados
UPDATE registry SET state = 0 WHERE directory IN ('billing', 'prescriptions', 'labs', 'imaging', 'pharmacy', 'telemedicine');

-- Configurar timezone
UPDATE globals SET gl_value = 'America/Sao_Paulo' WHERE gl_name = 'time_zone';

-- Configurar locale
UPDATE globals SET gl_value = 'pt_BR' WHERE gl_name = 'language_default';

-- Configurar upload max
UPDATE globals SET gl_value = '50M' WHERE gl_name = 'upload_max_filesize';
EOF

echo "✅ Configuração concluída!"
```

---

## 🧹 Scripts de Limpeza

### 7. `clean-cache.sh`

Script para limpar cache do OpenEMR.

```bash
#!/bin/bash
# clean-cache.sh
# Limpa cache do OpenEMR

set -e

echo "🧹 Limpando cache..."

# Limpar cache de documentos
rm -rf sites/*/documents/cache/*
rm -rf sites/*/documents/temp/*

# Limpar cache PHP (se existir)
if [ -d "sites/*/documents/php_cache" ]; then
    rm -rf sites/*/documents/php_cache/*
fi

echo "✅ Cache limpo!"
```

---

## 🚀 Scripts de Deploy (Railway)

### 8. `railway-start.sh`

Script de inicialização para Railway.

```bash
#!/bin/bash
# railway-start.sh
# Script de inicialização para Railway

set -e

echo "🚀 Iniciando OpenEMR no Railway..."

# Executar setup se necessário
if [ ! -f "sites/default/config.php" ]; then
    echo "📋 Executando setup inicial..."
    ./setup-openemr.sh
fi

# Configurar permissões
chmod -R 755 sites/
chmod -R 700 sites/default/documents

# Iniciar Apache
echo "🌐 Iniciando Apache..."
apache2-foreground
```

---

### 9. `railway-healthcheck.sh`

Script de healthcheck para Railway.

```bash
#!/bin/bash
# railway-healthcheck.sh
# Healthcheck para Railway

# Verificar se Apache está rodando
if ! pgrep -x "apache2" > /dev/null; then
    echo "❌ Apache não está rodando"
    exit 1
fi

# Verificar se pode conectar ao banco
if [ -n "$DB_HOST" ] && [ -n "$DB_NAME" ] && [ -n "$DB_USER" ]; then
    if ! mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1" "$DB_NAME" > /dev/null 2>&1; then
        echo "❌ Não consegue conectar ao banco de dados"
        exit 1
    fi
fi

# Verificar se arquivos essenciais existem
if [ ! -f "interface/main/main.php" ]; then
    echo "❌ Arquivos essenciais não encontrados"
    exit 1
fi

echo "✅ Sistema saudável"
exit 0
```

---

## 📝 Uso dos Scripts

### Executar Scripts

```bash
# Dar permissão de execução
chmod +x *.sh

# Executar script
./setup-openemr.sh
```

### Configurar Variáveis de Ambiente

```bash
# Criar arquivo .env ou exportar variáveis
export DB_HOST="localhost"
export DB_NAME="openemr"
export DB_USER="root"
export DB_PASS="senha"
```

### Executar no Railway

No `nixpacks.toml` ou `Dockerfile`, configure para executar scripts:

```toml
[start]
cmd = "./railway-start.sh"
```

---

## ⚠️ Avisos

1. **Sempre faça backup** antes de executar scripts que modificam dados
2. **Teste em ambiente de desenvolvimento** primeiro
3. **Revise os scripts** antes de executar
4. **Mantenha logs** de execução

---

## 📚 Scripts Adicionais

Para scripts mais específicos, consulte:
- [OpenEMR Scripts Repository](https://github.com/openemr/openemr/tree/master/scripts)
- [Railway Scripts Examples](https://docs.railway.app/guides/scripts)

