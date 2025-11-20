# Estrutura de Componentes OpenEMR - Guia Completo

## 📁 Estrutura de Diretórios do OpenEMR

```
openemr/
├── interface/                    # Interface do usuário (FRONTEND)
│   ├── main/                     # Interface principal
│   │   ├── calendar/             # ✅ MANTER - Sistema de agendamentos
│   │   ├── users/               # ✅ MANTER - Gestão de usuários
│   │   ├── navigation.php       # 🔧 CUSTOMIZAR - Menu principal
│   │   └── menu_data.php        # 🔧 CUSTOMIZAR - Dados do menu
│   ├── patient_file/            # ✅ MANTER - Prontuário do paciente
│   │   ├── summary/             # Resumo do paciente
│   │   ├── encounter/           # Encontros/atendimentos
│   │   └── history/             # Histórico
│   ├── forms/                   # ✅ MANTER - Formulários clínicos
│   │   ├── clinical_notes/      # Notas clínicas (evoluções)
│   │   └── [outros_forms]/      # Outros formulários
│   ├── billing/                 # ❌ REMOVER - Faturamento
│   ├── prescriptions/           # ❌ REMOVER - Prescrições
│   ├── labs/                    # ❌ REMOVER - Laboratórios
│   ├── imaging/                 # ❌ REMOVER - Imagens médicas
│   ├── pharmacy/                # ❌ REMOVER - Farmácia
│   ├── telemedicine/            # ❌ REMOVER - Telemedicina
│   └── filemanager/            # ✅ MANTER - Gestão de documentos
│
├── library/                     # Bibliotecas e classes (BACKEND)
│   ├── classes/                 # Classes principais
│   │   ├── Calendar/           # ✅ MANTER - Classes de agendamento
│   │   ├── Patient/            # ✅ MANTER - Classes de paciente
│   │   ├── Encounter/           # ✅ MANTER - Classes de encontro
│   │   ├── Forms/              # ✅ MANTER - Classes de formulários
│   │   ├── ACL/                # ✅ MANTER - Sistema de permissões
│   │   ├── Billing/            # ❌ REMOVER - Classes de faturamento
│   │   └── [outras]/           # Avaliar caso a caso
│   ├── sql/                    # Scripts SQL
│   └── vendors/                # Bibliotecas de terceiros
│
├── sites/                      # Configurações por site
│   └── default/                # Site padrão
│       ├── config.php          # ⚠️ CRÍTICO - Configurações
│       ├── documents/          # ✅ MANTER - Documentos dos pacientes
│       └── sqlconf.php         # ⚠️ CRÍTICO - Configurações de BD
│
├── sql/                        # Scripts SQL de instalação
│   ├── ippf_upgrade.php        # Scripts de upgrade
│   └── [versões]/              # Scripts por versão
│
└── documents/                  # Documentos gerais (pode remover se não usar)
```

---

## ✅ Componentes a MANTER e Utilizar

### 1. Sistema de Agendamentos (`interface/main/calendar/`)

**Arquivos Principais**:
- `interface/main/calendar/index.php` - Interface principal da agenda
- `interface/main/calendar/add_edit_event.php` - Adicionar/editar eventos
- `library/classes/Calendar/Calendar.php` - Classe principal
- `library/classes/Calendar/CalendarEvent.php` - Classe de eventos

**Funcionalidades**:
- ✅ Visualização de agenda (dia/semana/mês)
- ✅ Criação de agendamentos
- ✅ Edição de agendamentos
- ✅ Cancelamento de agendamentos
- ✅ Bloqueios de horário
- ✅ Recorrência de eventos

**Como Adaptar**:
```php
// Adicionar campo unit_id aos eventos
// Em library/classes/Calendar/CalendarEvent.php
class CalendarEvent {
    private $unit_id; // Adicionar este campo
    
    // Adicionar filtro por unidade nas queries
    public function getEventsByUnit($unit_id) {
        // Implementar filtro
    }
}
```

**Mapeamento com Sistema Atual**:
- `app/Livewire/AgendaBoard.php` → `interface/main/calendar/index.php`
- `app/Livewire/AgendaView.php` → `interface/main/calendar/index.php`
- `app/Models/Atendimento.php` → `library/classes/Calendar/CalendarEvent.php`

---

### 2. Sistema de Pacientes (`interface/patient_file/`)

**Arquivos Principais**:
- `interface/patient_file/summary/demographics.php` - Dados demográficos
- `interface/patient_file/summary/dashboard.php` - Dashboard do paciente
- `interface/patient_file/history/encounters.php` - Histórico de encontros
- `library/classes/Patient/Patient.php` - Classe principal

**Funcionalidades**:
- ✅ Cadastro completo de pacientes
- ✅ Prontuário eletrônico
- ✅ Histórico de atendimentos
- ✅ Timeline de eventos
- ✅ Upload de documentos

**Como Adaptar**:
```php
// Adicionar campo unit_id na tabela patient_data
ALTER TABLE patient_data ADD COLUMN unit_id INT;

// Modificar classe Patient
class Patient {
    private $unit_id;
    
    public function getPatientsByUnit($unit_id) {
        // Implementar filtro
    }
}
```

**Mapeamento com Sistema Atual**:
- `app/Livewire/ListaPacientes.php` → `interface/patient_file/summary/demographics.php`
- `app/Livewire/ProntuarioView.php` → `interface/patient_file/summary/dashboard.php`
- `app/Models/Paciente.php` → `library/classes/Patient/Patient.php`

---

### 3. Sistema de Formulários Clínicos (`interface/forms/`)

**Arquivos Principais**:
- `interface/forms/clinical_notes/` - Notas clínicas (evoluções)
- `interface/forms/CustomFormHandler.php` - Handler de formulários
- `library/classes/Forms/` - Classes de formulários

**Funcionalidades**:
- ✅ Criação de evoluções
- ✅ Templates de formulários
- ✅ Avaliações customizadas
- ✅ Histórico de formulários

**Como Adaptar**:
```php
// Criar formulário customizado para evoluções
// Em interface/forms/evolution_form.php
class EvolutionForm extends CustomFormHandler {
    // Implementar lógica de evolução
    // Adicionar campo unit_id
    // Adicionar sistema de revisão
}
```

**Mapeamento com Sistema Atual**:
- `app/Livewire/FormEvolucao.php` → `interface/forms/clinical_notes/`
- `app/Livewire/PainelEvolucoes.php` → `interface/forms/clinical_notes/list.php`
- `app/Models/Evolucao.php` → `library/classes/Forms/ClinicalNote.php`

---

### 4. Sistema de Permissões ACL (`library/classes/ACL/`)

**Arquivos Principais**:
- `library/classes/ACL/ACL.php` - Classe principal de ACL
- `interface/main/users/user_admin.php` - Interface de gestão de usuários
- `library/classes/User/User.php` - Classe de usuário

**Funcionalidades**:
- ✅ Sistema de roles
- ✅ Permissões granulares
- ✅ Grupos de usuários
- ✅ Controle de acesso

**Como Adaptar**:
```php
// Criar roles customizados
// Em library/classes/ACL/ACLCustom.php
class ACLCustom extends ACL {
    const ROLE_ADMIN = 'admin';
    const ROLE_COORDENADOR = 'coordenador';
    const ROLE_PROFISSIONAL = 'profissional';
    const ROLE_SECRETARIA = 'secretaria';
    
    // Mapear permissões do sistema atual
    public function mapPermissions($role) {
        // Implementar mapeamento
    }
}
```

**Mapeamento com Sistema Atual**:
- `app/Http/Middleware/ScopeUnit.php` → `library/classes/ACL/ACL.php`
- `app/Policies/*.php` → `library/classes/ACL/ACL.php`
- `app/Models/User.php` → `library/classes/User/User.php`

---

### 5. Sistema de Documentos (`interface/filemanager/`)

**Arquivos Principais**:
- `interface/filemanager/index.php` - Interface de documentos
- `library/classes/Document/Document.php` - Classe de documentos

**Funcionalidades**:
- ✅ Upload de documentos
- ✅ Categorização
- ✅ Download seguro
- ✅ Gestão de permissões

**Mapeamento com Sistema Atual**:
- `app/Http/Controllers/DocumentoController.php` → `interface/filemanager/index.php`
- `app/Models/Documento.php` → `library/classes/Document/Document.php`

---

### 6. Sistema de Relatórios (`interface/reports/`)

**Arquivos Principais**:
- `interface/reports/custom_report.php` - Relatórios customizados
- `library/classes/Report/Report.php` - Classe de relatórios

**Funcionalidades**:
- ✅ Relatórios básicos
- ✅ Relatórios customizados
- ✅ Exportação (PDF, Excel)

**Mapeamento com Sistema Atual**:
- `app/Livewire/RelatorioFrequencia.php` → `interface/reports/`
- `app/Livewire/RelatorioProdutividade.php` → `interface/reports/`

---

## ❌ Componentes a REMOVER

### 1. Sistema de Faturamento (`interface/billing/`)

**Arquivos a Remover**:
```
interface/billing/
library/classes/Billing/
sql/billing_*.sql (avaliar dependências)
```

**Como Remover**:
1. Desativar via interface admin
2. Remover pastas após testes
3. Limpar banco de dados:
```sql
DELETE FROM registry WHERE directory = 'billing';
DELETE FROM globals WHERE gl_name LIKE 'billing_%';
```

---

### 2. Sistema de Prescrições (`interface/prescriptions/`)

**Arquivos a Remover**:
```
interface/prescriptions/
library/classes/Prescription/
```

**Como Remover**:
```sql
DELETE FROM registry WHERE directory = 'prescriptions';
```

---

### 3. Sistema de Laboratórios (`interface/labs/`)

**Arquivos a Remover**:
```
interface/labs/
library/classes/Lab/
```

---

### 4. Sistema de Imagens (`interface/imaging/`)

**Arquivos a Remover**:
```
interface/imaging/
library/classes/Imaging/
```

---

### 5. Sistema de Farmácia (`interface/pharmacy/`)

**Arquivos a Remover**:
```
interface/pharmacy/
library/classes/Pharmacy/
```

---

### 6. Sistema de Telemedicina (`interface/telemedicine/`)

**Arquivos a Remover**:
```
interface/telemedicine/
```

---

## 🔧 Componentes a CUSTOMIZAR

### 1. Menu Principal (`interface/main/navigation.php`)

**O que fazer**:
- Remover links para módulos desativados
- Reorganizar menu por role
- Simplificar navegação

**Exemplo**:
```php
// interface/main/navigation.php
function buildMenu($user_role) {
    $menu = [];
    
    // Menu para Admin
    if ($user_role === 'admin') {
        $menu[] = ['label' => 'Dashboard', 'url' => '/dashboard'];
        $menu[] = ['label' => 'Agenda', 'url' => '/calendar'];
        $menu[] = ['label' => 'Pacientes', 'url' => '/patients'];
        $menu[] = ['label' => 'Evoluções', 'url' => '/evolutions'];
        $menu[] = ['label' => 'Admin', 'url' => '/admin'];
    }
    
    // Menu para Coordenador
    if ($user_role === 'coordenador') {
        $menu[] = ['label' => 'Dashboard', 'url' => '/dashboard'];
        $menu[] = ['label' => 'Agenda', 'url' => '/calendar'];
        $menu[] = ['label' => 'Pacientes', 'url' => '/patients'];
        $menu[] = ['label' => 'Evoluções', 'url' => '/evolutions'];
        // Sem link para Admin
    }
    
    // ... outros roles
    
    return $menu;
}
```

---

### 2. Dashboard (`interface/main/dashboard.php`)

**O que fazer**:
- Personalizar widgets
- Filtrar por unidade
- Adaptar métricas

**Exemplo**:
```php
// interface/main/dashboard.php
function getDashboardData($user) {
    $data = [];
    
    // Admin vê todas as unidades
    if ($user->role === 'admin') {
        $data['appointments_today'] = getAppointmentsCount(null);
        $data['patients_total'] = getPatientsCount(null);
    } else {
        // Outros roles filtram por unidade
        $data['appointments_today'] = getAppointmentsCount($user->unit_id);
        $data['patients_total'] = getPatientsCount($user->unit_id);
    }
    
    return $data;
}
```

---

### 3. Sistema de Unidades

**O que fazer**:
- Adicionar suporte a unidades (não existe nativamente)
- Criar tabela `units`
- Adicionar `unit_id` nas tabelas relevantes
- Criar middleware de filtro

**Implementação**:
```sql
-- Criar tabela de unidades
CREATE TABLE units (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(255),
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Adicionar unit_id aos usuários
ALTER TABLE users ADD COLUMN unit_id INT;
ALTER TABLE users ADD INDEX idx_unit_id (unit_id);

-- Adicionar unit_id aos appointments
ALTER TABLE openemr_postcalendar_events ADD COLUMN unit_id INT;
ALTER TABLE openemr_postcalendar_events ADD INDEX idx_unit_id (unit_id);

-- Adicionar unit_id aos patients
ALTER TABLE patient_data ADD COLUMN unit_id INT;
ALTER TABLE patient_data ADD INDEX idx_unit_id (unit_id);
```

```php
// library/classes/Unit/UnitFilter.php
class UnitFilter {
    public static function apply($query, $user) {
        // Admin bypassa filtro
        if ($user->role === 'admin') {
            return $query;
        }
        
        // Outros roles filtram por unidade
        return $query->where('unit_id', $user->unit_id);
    }
}
```

---

## 📋 Checklist de Componentes

### Componentes Críticos (NÃO REMOVER)
- [ ] `interface/main/calendar/` - Agendamentos
- [ ] `interface/patient_file/` - Pacientes
- [ ] `interface/forms/` - Formulários clínicos
- [ ] `library/classes/ACL/` - Permissões
- [ ] `library/classes/User/` - Usuários
- [ ] `sites/default/config.php` - Configurações
- [ ] `sites/default/sqlconf.php` - Configurações BD

### Componentes a Remover
- [ ] `interface/billing/` - Faturamento
- [ ] `interface/prescriptions/` - Prescrições
- [ ] `interface/labs/` - Laboratórios
- [ ] `interface/imaging/` - Imagens
- [ ] `interface/pharmacy/` - Farmácia
- [ ] `interface/telemedicine/` - Telemedicina

### Componentes a Customizar
- [ ] `interface/main/navigation.php` - Menu
- [ ] `interface/main/dashboard.php` - Dashboard
- [ ] Sistema de unidades (criar do zero)
- [ ] Roles e permissões (adaptar)

---

## 🔗 Dependências entre Componentes

### Agendamentos depende de:
- ✅ Usuários (profissionais)
- ✅ Pacientes
- ✅ Salas (criar se não existir)
- ❌ Billing (remover dependência)

### Pacientes depende de:
- ✅ Usuários (criado por)
- ✅ Documentos
- ❌ Billing (remover dependência)

### Evoluções depende de:
- ✅ Pacientes
- ✅ Agendamentos
- ✅ Usuários (profissionais)
- ❌ Prescriptions (remover dependência)

---

## 📝 Notas Importantes

1. **Sempre fazer backup** antes de remover componentes
2. **Testar dependências** antes de remover
3. **Documentar todas as alterações**
4. **Manter compatibilidade** com upgrades do OpenEMR
5. **Revisar código** antes de remover para evitar quebrar funcionalidades

---

## 🚀 Próximos Passos

1. Revisar esta estrutura
2. Identificar componentes específicos a manter/remover
3. Criar scripts de remoção automatizada
4. Testar em ambiente de desenvolvimento
5. Documentar customizações realizadas

