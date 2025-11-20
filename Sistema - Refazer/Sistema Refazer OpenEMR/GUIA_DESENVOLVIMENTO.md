# Guia de Desenvolvimento e Adaptação - OpenEMR

## 🎯 Objetivo

Este guia fornece instruções práticas para desenvolver e adaptar o OpenEMR para atender às necessidades do sistema Equidade VPS.

---

## 🛠️ Ambiente de Desenvolvimento

### Pré-requisitos
- PHP 8.0+
- MySQL/MariaDB 10.3+
- Apache/Nginx
- Composer (opcional, para dependências)

### Setup Local

```bash
# 1. Clonar OpenEMR
git clone https://github.com/openemr/openemr.git
cd openemr

# 2. Configurar banco de dados
mysql -u root -p
CREATE DATABASE openemr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 3. Executar instalação
# Acessar http://localhost/openemr e seguir wizard

# 4. Configurar permissões
chmod -R 755 sites/
chmod -R 700 sites/default/documents/
```

---

## 📝 Padrões de Código OpenEMR

### 1. Estrutura de Arquivos PHP

```php
<?php
/**
 * Descrição do arquivo
 * 
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Seu Nome <seu@email.com>
 * @copyright Copyright (c) 2024 OpenEMR Foundation
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("../../globals.php");
require_once("$srcdir/classes/YourClass.php");

use OpenEMR\Common\Acl\AclMain;

// Verificar permissões
if (!AclMain::aclCheckCore('admin', 'users')) {
    die(xlt("Access Denied"));
}

// Seu código aqui
```

### 2. Acessar Banco de Dados

```php
// Usar funções SQL do OpenEMR
$result = sqlStatement("SELECT * FROM patient_data WHERE pid = ?", [$pid]);

// Para múltiplas linhas
$rows = [];
while ($row = sqlFetchArray($result)) {
    $rows[] = $row;
}

// Para uma linha
$row = sqlQuery("SELECT * FROM patient_data WHERE pid = ?", [$pid]);

// Inserir
$id = sqlInsert("INSERT INTO table_name (field1, field2) VALUES (?, ?)", [$value1, $value2]);

// Atualizar
sqlStatement("UPDATE table_name SET field1 = ? WHERE id = ?", [$value1, $id]);

// Deletar
sqlStatement("DELETE FROM table_name WHERE id = ?", [$id]);
```

### 3. Tradução e Internacionalização

```php
// Usar função xlt() para tradução
echo xlt("Hello World");

// Com parâmetros
echo xlt("Patient: %s", $patient_name);

// Para JavaScript
echo xlj("Hello World");
```

### 4. Segurança e Validação

```php
// Sanitizar entrada
$input = $_POST['input'];
$sanitized = text($input); // Remove HTML tags

// Para exibição
echo text($value); // Escapa HTML

// Para URLs
$url = attr($url); // Escapa para atributos HTML

// Validar permissões
if (!AclMain::aclCheckCore('patients', 'demo')) {
    die(xlt("Access Denied"));
}
```

---

## 🔧 Criando Componentes Customizados

### 1. Criar Classe Customizada

```php
<?php
// library/classes/Custom/UnitFilter.php

namespace OpenEMR\Custom;

class UnitFilter {
    
    /**
     * Aplicar filtro de unidade em query
     */
    public static function apply($user, $table_alias = '') {
        // Admin bypassa filtro
        if ($user->role === 'admin') {
            return '';
        }
        
        $alias = $table_alias ? $table_alias . '.' : '';
        return " AND {$alias}unit_id = " . (int)$user->unit_id;
    }
    
    /**
     * Verificar se usuário tem acesso à unidade
     */
    public static function hasAccess($user, $unit_id) {
        if ($user->role === 'admin') {
            return true;
        }
        
        return $user->unit_id == $unit_id;
    }
}
```

### 2. Criar Formulário Customizado

```php
<?php
// interface/forms/evolution_form.php

require_once("../../globals.php");
require_once("$srcdir/classes/Custom/EvolutionCustom.php");

use OpenEMR\Custom\EvolutionCustom;

// Verificar permissões
if (!AclMain::aclCheckCore('patients', 'encounters')) {
    die(xlt("Access Denied"));
}

$pid = $_GET['pid'] ?? null;
$encounter_id = $_GET['encounter'] ?? null;

if ($_POST['submit']) {
    $evolution = new EvolutionCustom();
    $evolution_id = $evolution->createEvolution(
        $pid,
        $_SESSION['authUserID'],
        $_POST['content']
    );
    
    if ($evolution_id) {
        echo "<script>alert('" . xlt("Evolution created successfully") . "');</script>";
        header("Location: evolution_list.php?pid=" . attr($pid));
        exit;
    }
}
?>

<form method="post">
    <textarea name="content" rows="10" cols="80" required></textarea>
    <input type="submit" name="submit" value="<?php echo xlt("Save"); ?>">
</form>
```

### 3. Criar API Endpoint

```php
<?php
// interface/api/custom_api.php

require_once("../../globals.php");
require_once("$srcdir/classes/Custom/UnitFilter.php");

use OpenEMR\Custom\UnitFilter;

header('Content-Type: application/json');

// Verificar autenticação
if (!isset($_SESSION['authUserID'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user = new stdClass();
$user->role = $_SESSION['authUserRole'];
$user->unit_id = $_SESSION['authUserUnitId'] ?? null;

// Exemplo: Listar agendamentos
if ($_GET['action'] === 'get_appointments') {
    $date = $_GET['date'] ?? date('Y-m-d');
    
    $sql = "SELECT * FROM openemr_postcalendar_events 
            WHERE pc_eventDate = ?" . UnitFilter::apply($user);
    
    $result = sqlStatement($sql, [$date]);
    $appointments = [];
    
    while ($row = sqlFetchArray($result)) {
        $appointments[] = $row;
    }
    
    echo json_encode($appointments);
    exit;
}
```

---

## 🗄️ Modificando Banco de Dados

### 1. Adicionar Campos

```sql
-- Adicionar unit_id aos appointments
ALTER TABLE openemr_postcalendar_events 
ADD COLUMN unit_id INT DEFAULT NULL,
ADD INDEX idx_unit_id (unit_id);

-- Adicionar unit_id aos patients
ALTER TABLE patient_data 
ADD COLUMN unit_id INT DEFAULT NULL,
ADD INDEX idx_unit_id (unit_id);

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
```

### 2. Criar Tabelas Customizadas

```sql
-- Tabela de evoluções customizada
CREATE TABLE IF NOT EXISTS evolutions_custom (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pid INT NOT NULL,
    encounter_id INT,
    professional_id INT NOT NULL,
    unit_id INT NOT NULL,
    content TEXT NOT NULL,
    status ENUM('pendente', 'finalizada', 'revisada') DEFAULT 'pendente',
    reviewer_id INT DEFAULT NULL,
    review_comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    finalized_at TIMESTAMP NULL,
    reviewed_at TIMESTAMP NULL,
    INDEX idx_pid (pid),
    INDEX idx_professional (professional_id),
    INDEX idx_unit (unit_id),
    INDEX idx_status (status),
    FOREIGN KEY (pid) REFERENCES patient_data(pid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Script de Migração

```php
<?php
// sql/custom_migrations.php

require_once("../../globals.php");

function runCustomMigrations() {
    // Adicionar unit_id aos usuários
    $sql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS unit_id INT";
    sqlStatement($sql);
    
    // Criar tabela de unidades
    $sql = "CREATE TABLE IF NOT EXISTS units (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    sqlStatement($sql);
    
    echo "Migrations completed successfully\n";
}

runCustomMigrations();
```

---

## 🔐 Implementando Sistema de Permissões

### 1. Criar Roles Customizados

```php
<?php
// library/classes/Custom/RolesCustom.php

namespace OpenEMR\Custom;

class RolesCustom {
    const ROLE_ADMIN = 'admin';
    const ROLE_COORDENADOR = 'coordenador';
    const ROLE_PROFISSIONAL = 'profissional';
    const ROLE_SECRETARIA = 'secretaria';
    
    /**
     * Mapear role do OpenEMR para role customizado
     */
    public static function mapRole($openemr_role) {
        $mapping = [
            'Administrators' => self::ROLE_ADMIN,
            'Physicians' => self::ROLE_PROFISSIONAL,
            'Nurses' => self::ROLE_PROFISSIONAL,
            'Receptionists' => self::ROLE_SECRETARIA,
        ];
        
        return $mapping[$openemr_role] ?? self::ROLE_PROFISSIONAL;
    }
    
    /**
     * Verificar permissão
     */
    public static function hasPermission($user_role, $permission) {
        $permissions = [
            self::ROLE_ADMIN => [
                'all' => true, // Admin tem acesso total
            ],
            self::ROLE_COORDENADOR => [
                'appointments.view' => true,
                'appointments.create' => true,
                'appointments.edit' => true,
                'patients.view' => true,
                'patients.create' => true,
                'evolutions.view' => true,
                'evolutions.create' => true,
                'evolutions.review' => true,
                'reports.view' => true,
            ],
            self::ROLE_PROFISSIONAL => [
                'appointments.view_own' => true,
                'appointments.create_own' => true,
                'patients.view' => true,
                'evolutions.view_own' => true,
                'evolutions.create_own' => true,
            ],
            self::ROLE_SECRETARIA => [
                'appointments.view' => true,
                'appointments.create' => true,
                'appointments.edit' => true,
                'patients.view' => true,
                'patients.create' => true,
                'patients.edit' => true,
            ],
        ];
        
        // Admin tem acesso total
        if ($user_role === self::ROLE_ADMIN) {
            return true;
        }
        
        return $permissions[$user_role][$permission] ?? false;
    }
}
```

### 2. Middleware de Permissões

```php
<?php
// library/classes/Custom/PermissionMiddleware.php

namespace OpenEMR\Custom;

class PermissionMiddleware {
    
    /**
     * Verificar permissão antes de executar ação
     */
    public static function check($permission, $redirect = true) {
        $user_role = $_SESSION['authUserRole'] ?? null;
        $user_role_custom = RolesCustom::mapRole($user_role);
        
        if (!RolesCustom::hasPermission($user_role_custom, $permission)) {
            if ($redirect) {
                header("Location: ../../main/main_screen.php");
                die(xlt("Access Denied"));
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * Verificar acesso à unidade
     */
    public static function checkUnitAccess($unit_id) {
        $user_role = $_SESSION['authUserRole'] ?? null;
        $user_role_custom = RolesCustom::mapRole($user_role);
        $user_unit_id = $_SESSION['authUserUnitId'] ?? null;
        
        // Admin tem acesso a todas as unidades
        if ($user_role_custom === RolesCustom::ROLE_ADMIN) {
            return true;
        }
        
        return $user_unit_id == $unit_id;
    }
}
```

---

## 🎨 Customizando Interface

### 1. Modificar Menu

```php
<?php
// interface/main/menu_custom.php

function buildCustomMenu($user_role) {
    $menu = [];
    
    // Mapear role
    $role_custom = RolesCustom::mapRole($user_role);
    
    // Menu base
    $menu[] = [
        'label' => xlt('Dashboard'),
        'url' => '../../main/main_screen.php',
        'icon' => 'fa-home'
    ];
    
    // Menu por role
    switch ($role_custom) {
        case RolesCustom::ROLE_ADMIN:
            $menu[] = ['label' => xlt('Agenda'), 'url' => '../../main/calendar/index.php'];
            $menu[] = ['label' => xlt('Pacientes'), 'url' => '../../patient_file/summary/demographics.php'];
            $menu[] = ['label' => xlt('Evoluções'), 'url' => '../../forms/clinical_notes/list.php'];
            $menu[] = ['label' => xlt('Admin'), 'url' => '../../main/users/user_admin.php'];
            break;
            
        case RolesCustom::ROLE_COORDENADOR:
            $menu[] = ['label' => xlt('Agenda'), 'url' => '../../main/calendar/index.php'];
            $menu[] = ['label' => xlt('Pacientes'), 'url' => '../../patient_file/summary/demographics.php'];
            $menu[] = ['label' => xlt('Evoluções'), 'url' => '../../forms/clinical_notes/list.php'];
            // Sem link para Admin
            break;
            
        // ... outros roles
    }
    
    return $menu;
}
```

### 2. Criar Widget de Dashboard

```php
<?php
// interface/main/dashboard_widgets/evolutions_pending.php

require_once("../../../globals.php");
require_once("$srcdir/classes/Custom/UnitFilter.php");

use OpenEMR\Custom\UnitFilter;

$user = new stdClass();
$user->role = $_SESSION['authUserRole'];
$user->unit_id = $_SESSION['authUserUnitId'] ?? null;

// Contar evoluções pendentes
$sql = "SELECT COUNT(*) as count FROM evolutions_custom 
        WHERE status = 'pendente'" . UnitFilter::apply($user);

$result = sqlQuery($sql);
$count = $result['count'] ?? 0;
?>

<div class="widget">
    <h3><?php echo xlt("Pending Evolutions"); ?></h3>
    <div class="widget-content">
        <span class="count"><?php echo text($count); ?></span>
    </div>
</div>
```

---

## 🧪 Testando Componentes

### 1. Teste Unitário Básico

```php
<?php
// tests/Unit/UnitFilterTest.php

require_once("../../library/classes/Custom/UnitFilter.php");

use OpenEMR\Custom\UnitFilter;

// Mock user
$user_admin = new stdClass();
$user_admin->role = 'admin';
$user_admin->unit_id = null;

$user_coordenador = new stdClass();
$user_coordenador->role = 'coordenador';
$user_coordenador->unit_id = 1;

// Teste: Admin não aplica filtro
$filter = UnitFilter::apply($user_admin);
assert($filter === '', "Admin should not have unit filter");

// Teste: Coordenador aplica filtro
$filter = UnitFilter::apply($user_coordenador);
assert(strpos($filter, 'unit_id = 1') !== false, "Coordenador should have unit filter");
```

### 2. Teste de Integração

```php
<?php
// tests/Integration/EvolutionTest.php

require_once("../../library/classes/Custom/EvolutionCustom.php");

use OpenEMR\Custom\EvolutionCustom;

// Criar evolução de teste
$evolution = new EvolutionCustom();
$evolution_id = $evolution->createEvolution(1, 1, "Test evolution");

// Verificar se foi criada
assert($evolution_id > 0, "Evolution should be created");

// Verificar status
$evolution_data = sqlQuery("SELECT * FROM evolutions_custom WHERE id = ?", [$evolution_id]);
assert($evolution_data['status'] === 'pendente', "Evolution should be pending");
```

---

## 📚 Recursos e Referências

### Documentação OpenEMR
- [OpenEMR Wiki](https://www.open-emr.org/wiki/)
- [Developer Documentation](https://www.open-emr.org/wiki/index.php/Developer_Documentation)
- [API Documentation](https://www.open-emr.org/wiki/index.php/OpenEMR_API)

### Código de Referência
- `library/classes/` - Classes principais
- `interface/` - Interfaces de usuário
- `sql/` - Scripts SQL

### Comunidade
- [OpenEMR Forum](https://www.open-emr.org/forum/)
- [GitHub Issues](https://github.com/openemr/openemr/issues)

---

## ⚠️ Boas Práticas

1. **Sempre verificar permissões** antes de executar ações
2. **Usar funções SQL do OpenEMR** (sqlStatement, sqlQuery, etc.)
3. **Sanitizar todas as entradas** do usuário
4. **Usar funções de tradução** (xlt, xlj)
5. **Documentar código** adequadamente
6. **Testar em ambiente de desenvolvimento** antes de produção
7. **Fazer backup** antes de alterações significativas
8. **Seguir padrões** do OpenEMR para facilitar manutenção

---

## 🚀 Próximos Passos

1. Configurar ambiente de desenvolvimento
2. Estudar estrutura do OpenEMR
3. Criar componentes customizados básicos
4. Testar cada componente isoladamente
5. Integrar componentes
6. Testar sistema completo
7. Documentar customizações

