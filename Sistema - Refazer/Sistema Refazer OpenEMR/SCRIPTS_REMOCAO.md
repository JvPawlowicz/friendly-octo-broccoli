# Scripts de Remoção de Componentes - OpenEMR

## 🗑️ Visão Geral

Este documento contém scripts automatizados para remover componentes desnecessários do OpenEMR de forma segura.

---

## ⚠️ AVISOS IMPORTANTES

1. **SEMPRE fazer backup completo** antes de executar scripts de remoção
2. **Testar em ambiente de desenvolvimento** primeiro
3. **Revisar scripts** antes de executar
4. **Documentar** todas as remoções realizadas

---

## 📋 Script 1: Remover Módulos via Banco de Dados

### `remove-modules-db.sh`

```bash
#!/bin/bash
# remove-modules-db.sh
# Remove módulos do OpenEMR via banco de dados

set -e

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Verificar variáveis de ambiente
if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo -e "${RED}❌ Configure variáveis DB_HOST, DB_NAME, DB_USER, DB_PASS${NC}"
    exit 1
fi

# Módulos a remover
MODULES=(
    "billing"
    "prescriptions"
    "labs"
    "imaging"
    "pharmacy"
    "telemedicine"
    "hl7"
)

echo -e "${YELLOW}⚠️  ATENÇÃO: Este script removerá módulos do banco de dados!${NC}"
read -p "Tem certeza que deseja continuar? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Operação cancelada."
    exit 0
fi

echo -e "${GREEN}🗑️  Removendo módulos do banco de dados...${NC}"

for module in "${MODULES[@]}"; do
    echo "Removendo: $module"
    
    # Remover do registry
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" <<EOF
DELETE FROM registry WHERE directory = '$module';
DELETE FROM globals WHERE gl_name LIKE '${module}_%';
DELETE FROM layout_options WHERE form_id LIKE '${module}_%';
EOF
    
    echo "✅ $module removido do banco de dados"
done

echo -e "${GREEN}✅ Módulos removidos do banco de dados!${NC}"
```

**Uso**:
```bash
export DB_HOST="localhost"
export DB_NAME="openemr"
export DB_USER="root"
export DB_PASS="senha"
chmod +x remove-modules-db.sh
./remove-modules-db.sh
```

---

## 📁 Script 2: Remover Arquivos de Módulos

### `remove-modules-files.sh`

```bash
#!/bin/bash
# remove-modules-files.sh
# Remove arquivos de módulos desativados

set -e

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Verificar se está no diretório do OpenEMR
if [ ! -d "interface" ] || [ ! -d "library" ]; then
    echo -e "${RED}❌ Execute este script no diretório raiz do OpenEMR${NC}"
    exit 1
fi

echo -e "${YELLOW}⚠️  ATENÇÃO: Este script removerá arquivos permanentemente!${NC}"
read -p "Tem certeza que deseja continuar? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Operação cancelada."
    exit 0
fi

# Criar backup
BACKUP_DIR="backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
echo -e "${GREEN}💾 Criando backup em: $BACKUP_DIR${NC}"

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
    # Remover da interface
    if [ -d "interface/$module" ]; then
        echo "Fazendo backup de: interface/$module"
        cp -r "interface/$module" "$BACKUP_DIR/interface_$module" 2>/dev/null || true
        echo "Removendo: interface/$module"
        rm -rf "interface/$module"
        echo "✅ interface/$module removido"
    fi
    
    # Remover da library
    if [ -d "library/classes/$module" ] || [ -d "library/classes/${module^}" ]; then
        MODULE_CLASS=""
        if [ -d "library/classes/$module" ]; then
            MODULE_CLASS="library/classes/$module"
        elif [ -d "library/classes/${module^}" ]; then
            MODULE_CLASS="library/classes/${module^}"
        fi
        
        if [ -n "$MODULE_CLASS" ]; then
            echo "Fazendo backup de: $MODULE_CLASS"
            cp -r "$MODULE_CLASS" "$BACKUP_DIR/library_$module" 2>/dev/null || true
            echo "Removendo: $MODULE_CLASS"
            rm -rf "$MODULE_CLASS"
            echo "✅ $MODULE_CLASS removido"
        fi
    fi
done

echo -e "${GREEN}✅ Arquivos removidos! Backup salvo em: $BACKUP_DIR${NC}"
```

**Uso**:
```bash
cd /caminho/para/openemr
chmod +x remove-modules-files.sh
./remove-modules-files.sh
```

---

## 🧹 Script 3: Limpar Referências no Código

### `clean-code-references.php`

```php
<?php
/**
 * clean-code-references.php
 * Remove referências a módulos removidos no código
 */

require_once("globals.php");

$modules_to_remove = [
    'billing',
    'prescriptions',
    'labs',
    'imaging',
    'pharmacy',
    'telemedicine',
];

$directories_to_scan = [
    'interface/main/',
    'interface/patient_file/',
    'library/classes/',
];

function removeModuleReferences($file, $modules) {
    $content = file_get_contents($file);
    $original_content = $content;
    $modified = false;
    
    foreach ($modules as $module) {
        // Remover includes/requires
        $patterns = [
            "/require.*['\"]\.\.\/.*{$module}.*['\"];/i",
            "/include.*['\"]\.\.\/.*{$module}.*['\"];/i",
            "/require_once.*['\"]\.\.\/.*{$module}.*['\"];/i",
            "/include_once.*['\"]\.\.\/.*{$module}.*['\"];/i",
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, '', $content);
                $modified = true;
            }
        }
        
        // Remover links de menu
        $menu_pattern = "/.*{$module}.*menu.*/i";
        if (preg_match($menu_pattern, $content)) {
            $content = preg_replace($menu_pattern, '', $content);
            $modified = true;
        }
    }
    
    if ($modified && $content !== $original_content) {
        file_put_contents($file, $content);
        echo "Modificado: $file\n";
        return true;
    }
    
    return false;
}

function scanDirectory($dir, $modules) {
    $files = glob($dir . '*.php');
    $modified_count = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            if (removeModuleReferences($file, $modules)) {
                $modified_count++;
            }
        }
    }
    
    // Recursivo para subdiretórios
    $subdirs = glob($dir . '*/', GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
        $modified_count += scanDirectory($subdir, $modules);
    }
    
    return $modified_count;
}

echo "Limpando referências a módulos removidos...\n";

$total_modified = 0;
foreach ($directories_to_scan as $dir) {
    if (is_dir($dir)) {
        $total_modified += scanDirectory($dir, $modules_to_remove);
    }
}

echo "Total de arquivos modificados: $total_modified\n";
echo "Limpeza concluída!\n";
```

**Uso**:
```bash
php clean-code-references.php
```

---

## 🗄️ Script 4: Limpar Tabelas do Banco de Dados

### `clean-database-tables.sh`

```bash
#!/bin/bash
# clean-database-tables.sh
# Remove tabelas relacionadas a módulos removidos

set -e

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Verificar variáveis
if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo -e "${RED}❌ Configure variáveis DB_HOST, DB_NAME, DB_USER, DB_PASS${NC}"
    exit 1
fi

echo -e "${YELLOW}⚠️  ATENÇÃO: Este script removerá tabelas do banco de dados!${NC}"
read -p "Tem certeza que deseja continuar? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Operação cancelada."
    exit 0
fi

# Tabelas relacionadas a módulos removidos
TABLES=(
    "billing"
    "prescriptions"
    "prescription_track"
    "labs"
    "lab_results"
    "imaging"
    "pharmacy"
    "telemedicine"
)

echo -e "${GREEN}🗑️  Removendo tabelas...${NC}"

for table in "${TABLES[@]}"; do
    # Verificar se tabela existe
    TABLE_EXISTS=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "SHOW TABLES LIKE '$table'" | grep -c "$table" || true)
    
    if [ "$TABLE_EXISTS" -gt 0 ]; then
        echo "Removendo tabela: $table"
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" <<EOF
DROP TABLE IF EXISTS $table;
EOF
        echo "✅ Tabela $table removida"
    else
        echo "ℹ️  Tabela $table não existe, pulando"
    fi
done

echo -e "${GREEN}✅ Limpeza de tabelas concluída!${NC}"
```

**Uso**:
```bash
export DB_HOST="localhost"
export DB_NAME="openemr"
export DB_USER="root"
export DB_PASS="senha"
chmod +x clean-database-tables.sh
./clean-database-tables.sh
```

---

## 🔍 Script 5: Verificar Dependências

### `check-dependencies.php`

```php
<?php
/**
 * check-dependencies.php
 * Verifica dependências antes de remover módulos
 */

require_once("globals.php");

$modules_to_check = [
    'billing',
    'prescriptions',
    'labs',
    'imaging',
    'pharmacy',
    'telemedicine',
];

function checkDependencies($module) {
    $dependencies = [];
    
    // Verificar no banco de dados
    $sql = "SELECT * FROM registry WHERE directory = ?";
    $result = sqlQuery($sql, [$module]);
    
    if ($result) {
        $dependencies['database'] = true;
    }
    
    // Verificar arquivos
    $files = [
        "interface/$module",
        "library/classes/$module",
        "library/classes/" . ucfirst($module),
    ];
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            $dependencies['files'][] = $file;
        }
    }
    
    // Verificar referências no código
    $code_references = [];
    $dirs_to_scan = ['interface/main/', 'library/classes/'];
    
    foreach ($dirs_to_scan as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '**/*.php', GLOB_BRACE);
            foreach ($files as $file) {
                $content = file_get_contents($file);
                if (stripos($content, $module) !== false) {
                    $code_references[] = $file;
                }
            }
        }
    }
    
    if (!empty($code_references)) {
        $dependencies['code_references'] = $code_references;
    }
    
    return $dependencies;
}

echo "Verificando dependências...\n\n";

foreach ($modules_to_check as $module) {
    echo "Módulo: $module\n";
    $deps = checkDependencies($module);
    
    if (isset($deps['database'])) {
        echo "  ⚠️  Registrado no banco de dados\n";
    }
    
    if (isset($deps['files'])) {
        echo "  ⚠️  Arquivos encontrados:\n";
        foreach ($deps['files'] as $file) {
            echo "      - $file\n";
        }
    }
    
    if (isset($deps['code_references'])) {
        echo "  ⚠️  Referências no código:\n";
        foreach (array_slice($deps['code_references'], 0, 5) as $file) {
            echo "      - $file\n";
        }
        if (count($deps['code_references']) > 5) {
            echo "      ... e mais " . (count($deps['code_references']) - 5) . " arquivos\n";
        }
    }
    
    if (empty($deps)) {
        echo "  ✅ Nenhuma dependência encontrada\n";
    }
    
    echo "\n";
}
```

**Uso**:
```bash
php check-dependencies.php
```

---

## 🔄 Script 6: Remoção Completa (Todos os Passos)

### `remove-modules-complete.sh`

```bash
#!/bin/bash
# remove-modules-complete.sh
# Executa remoção completa de módulos (todos os passos)

set -e

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}⚠️  REMOÇÃO COMPLETA DE MÓDULOS${NC}"
echo -e "${YELLOW}Este script executará todos os passos de remoção${NC}"
echo ""
read -p "Tem certeza que deseja continuar? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Operação cancelada."
    exit 0
fi

# Passo 1: Verificar dependências
echo -e "${GREEN}📋 Passo 1: Verificando dependências...${NC}"
php check-dependencies.php

read -p "Continuar com a remoção? (yes/no): " continue
if [ "$continue" != "yes" ]; then
    echo "Operação cancelada."
    exit 0
fi

# Passo 2: Fazer backup
echo -e "${GREEN}💾 Passo 2: Fazendo backup...${NC}"
BACKUP_DIR="backup_complete_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Backup do banco
if [ -n "$DB_NAME" ]; then
    mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_DIR/database.sql"
    echo "✅ Backup do banco de dados criado"
fi

# Backup de arquivos
cp -r interface "$BACKUP_DIR/interface" 2>/dev/null || true
cp -r library "$BACKUP_DIR/library" 2>/dev/null || true
echo "✅ Backup de arquivos criado"

# Passo 3: Remover do banco de dados
echo -e "${GREEN}🗄️  Passo 3: Removendo do banco de dados...${NC}"
./remove-modules-db.sh

# Passo 4: Remover arquivos
echo -e "${GREEN}📁 Passo 4: Removendo arquivos...${NC}"
./remove-modules-files.sh

# Passo 5: Limpar referências no código
echo -e "${GREEN}🧹 Passo 5: Limpando referências no código...${NC}"
php clean-code-references.php

# Passo 6: Limpar tabelas (opcional)
read -p "Remover tabelas relacionadas? (yes/no): " remove_tables
if [ "$remove_tables" == "yes" ]; then
    echo -e "${GREEN}🗑️  Passo 6: Removendo tabelas...${NC}"
    ./clean-database-tables.sh
fi

echo -e "${GREEN}✅ Remoção completa concluída!${NC}"
echo -e "${YELLOW}📝 Backup salvo em: $BACKUP_DIR${NC}"
echo -e "${YELLOW}⚠️  Teste o sistema antes de usar em produção!${NC}"
```

**Uso**:
```bash
export DB_HOST="localhost"
export DB_NAME="openemr"
export DB_USER="root"
export DB_PASS="senha"
chmod +x remove-modules-complete.sh
./remove-modules-complete.sh
```

---

## 📝 Checklist de Remoção

Antes de executar os scripts:

- [ ] Backup completo do banco de dados
- [ ] Backup de todos os arquivos
- [ ] Ambiente de desenvolvimento configurado
- [ ] Dependências verificadas
- [ ] Scripts revisados
- [ ] Testes planejados

Após executar os scripts:

- [ ] Sistema ainda funciona
- [ ] Funcionalidades essenciais intactas
- [ ] Sem erros no log
- [ ] Menu atualizado
- [ ] Permissões funcionando
- [ ] Testes realizados

---

## ⚠️ Troubleshooting

### Problema: Erro ao remover do banco

**Solução**: Verificar se módulo está realmente desativado primeiro:
```sql
SELECT * FROM registry WHERE directory = 'billing';
```

### Problema: Arquivos não encontrados

**Solução**: Verificar se está no diretório correto:
```bash
pwd
ls -la interface/
```

### Problema: Referências ainda existem

**Solução**: Executar script de limpeza novamente:
```bash
php clean-code-references.php
```

---

## 🔗 Scripts Relacionados

- `setup-openemr.sh` - Setup inicial
- `backup-database.sh` - Backup do banco
- `configure-openemr.sh` - Configuração pós-remoção

---

## 📚 Notas Finais

1. **Sempre testar** em ambiente de desenvolvimento primeiro
2. **Documentar** todas as remoções realizadas
3. **Manter backups** por pelo menos 30 dias
4. **Revisar logs** após remoção
5. **Validar funcionalidades** essenciais

