# Mapeamento de Componentes - Sistema Atual → OpenEMR

## 📊 Visão Geral

Este documento mapeia cada componente do sistema atual (Laravel/Filament) para os componentes equivalentes no OpenEMR, facilitando a migração e adaptação.

---

## 🔄 Mapeamento por Módulo

### 1. Autenticação e Usuários

#### Sistema Atual (Laravel)
```
app/Http/Controllers/Auth/
├── AuthenticatedSessionController.php
├── RegisteredUserController.php
└── PasswordResetLinkController.php

app/Models/User.php
app/Http/Middleware/ScopeUnit.php
```

#### OpenEMR Equivalente
```
interface/main/users/
├── user_admin.php          # Gestão de usuários
├── user_settings.php       # Configurações de usuário
└── login.php               # Login

library/classes/User/
├── User.php                # Classe de usuário
└── UserService.php         # Serviços de usuário

library/classes/ACL/
└── ACL.php                 # Sistema de permissões
```

#### Adaptações Necessárias
1. **Sistema de Roles**: OpenEMR usa ACL diferente, precisa adaptar
2. **Unidades**: OpenEMR não tem nativamente, precisa criar
3. **Middleware**: Adaptar `ScopeUnit` para sistema ACL do OpenEMR

**Código de Adaptação**:
```php
// Criar: library/classes/User/UserCustom.php
class UserCustom extends User {
    private $unit_id;
    private $role_custom; // admin, coordenador, profissional, secretaria
    
    public function getUnitId() {
        return $this->unit_id;
    }
    
    public function getRoleCustom() {
        return $this->role_custom;
    }
    
    // Mapear role do OpenEMR para role customizado
    public function mapRole($openemr_role) {
        $mapping = [
            'Administrators' => 'admin',
            'Physicians' => 'profissional',
            'Nurses' => 'profissional',
            'Receptionists' => 'secretaria',
        ];
        return $mapping[$openemr_role] ?? 'profissional';
    }
}
```

---

### 2. Agendamentos

#### Sistema Atual (Laravel)
```
app/Livewire/
├── AgendaBoard.php
├── AgendaView.php
└── FormAtendimento.php

app/Models/Atendimento.php
app/Models/Sala.php
app/Models/BloqueioAgenda.php
```

#### OpenEMR Equivalente
```
interface/main/calendar/
├── index.php               # Interface principal
├── add_edit_event.php      # Adicionar/editar evento
└── find_patient.php        # Buscar paciente

library/classes/Calendar/
├── Calendar.php            # Classe principal
└── CalendarEvent.php       # Classe de eventos
```

#### Adaptações Necessárias
1. **Salas**: OpenEMR usa "facilities", adaptar para "salas"
2. **Bloqueios**: OpenEMR tem sistema de bloqueios, adaptar
3. **Unidades**: Adicionar filtro por unidade

**Código de Adaptação**:
```php
// Modificar: library/classes/Calendar/CalendarCustom.php
class CalendarCustom extends Calendar {
    
    // Adicionar filtro por unidade
    public function getEventsByUnit($unit_id, $start_date, $end_date) {
        $sql = "SELECT * FROM openemr_postcalendar_events 
                WHERE unit_id = ? 
                AND pc_eventDate BETWEEN ? AND ?";
        return sqlStatement($sql, [$unit_id, $start_date, $end_date]);
    }
    
    // Adicionar suporte a salas
    public function getRoomsByUnit($unit_id) {
        $sql = "SELECT * FROM rooms WHERE unit_id = ?";
        return sqlStatement($sql, [$unit_id]);
    }
    
    // Adicionar bloqueios
    public function getBlockedTimes($unit_id, $date) {
        $sql = "SELECT * FROM blocked_times 
                WHERE unit_id = ? AND date = ?";
        return sqlStatement($sql, [$unit_id, $date]);
    }
}
```

---

### 3. Pacientes

#### Sistema Atual (Laravel)
```
app/Livewire/
├── ListaPacientes.php
├── FormPaciente.php
└── ProntuarioView.php

app/Models/Paciente.php
app/Models/Responsavel.php
app/Models/PlanoSaude.php
```

#### OpenEMR Equivalente
```
interface/patient_file/
├── summary/
│   ├── demographics.php    # Dados demográficos
│   └── dashboard.php       # Dashboard do paciente
├── encounter/              # Encontros/atendimentos
└── history/                # Histórico

library/classes/Patient/
├── Patient.php             # Classe principal
└── PatientService.php      # Serviços
```

#### Adaptações Necessárias
1. **Responsáveis**: OpenEMR tem "guardians", adaptar
2. **Planos de Saúde**: OpenEMR tem "insurance", adaptar
3. **Unidades**: Adicionar filtro por unidade

**Código de Adaptação**:
```php
// Modificar: library/classes/Patient/PatientCustom.php
class PatientCustom extends Patient {
    
    // Adicionar filtro por unidade
    public function getPatientsByUnit($unit_id) {
        $sql = "SELECT * FROM patient_data WHERE unit_id = ?";
        return sqlStatement($sql, [$unit_id]);
    }
    
    // Adaptar responsáveis (guardians)
    public function getResponsaveis($patient_id) {
        $sql = "SELECT * FROM patient_guardians WHERE pid = ?";
        return sqlStatement($sql, [$patient_id]);
    }
    
    // Adaptar planos de saúde (insurance)
    public function getPlanosSaude($patient_id) {
        $sql = "SELECT * FROM insurance_data WHERE pid = ?";
        return sqlStatement($sql, [$patient_id]);
    }
}
```

---

### 4. Evoluções

#### Sistema Atual (Laravel)
```
app/Livewire/
├── FormEvolucao.php
└── PainelEvolucoes.php

app/Models/Evolucao.php
app/Events/EvolucaoPendenteCriada.php
```

#### OpenEMR Equivalente
```
interface/forms/
├── clinical_notes/         # Notas clínicas
│   ├── form.php           # Formulário de evolução
│   └── list.php           # Lista de evoluções
└── CustomFormHandler.php  # Handler customizado

library/classes/Forms/
├── ClinicalNote.php       # Classe de nota clínica
└── FormHandler.php        # Handler de formulários
```

#### Adaptações Necessárias
1. **Sistema de Revisão**: Criar do zero (OpenEMR não tem nativamente)
2. **Evoluções Pendentes**: Adaptar sistema de status
3. **Templates**: OpenEMR tem templates, adaptar

**Código de Adaptação**:
```php
// Criar: library/classes/Forms/EvolutionCustom.php
class EvolutionCustom extends ClinicalNote {
    const STATUS_PENDENTE = 'pendente';
    const STATUS_FINALIZADA = 'finalizada';
    const STATUS_REVISADA = 'revisada';
    
    // Criar evolução
    public function createEvolution($patient_id, $professional_id, $content) {
        $data = [
            'pid' => $patient_id,
            'provider_id' => $professional_id,
            'note' => $content,
            'status' => self::STATUS_PENDENTE,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return sqlInsert('evolutions', $data);
    }
    
    // Revisar evolução (coordenador/admin)
    public function reviewEvolution($evolution_id, $reviewer_id, $comments) {
        $data = [
            'status' => self::STATUS_REVISADA,
            'reviewer_id' => $reviewer_id,
            'review_comments' => $comments,
            'reviewed_at' => date('Y-m-d H:i:s')
        ];
        return sqlUpdate('evolutions', $data, ['id' => $evolution_id]);
    }
    
    // Finalizar evolução
    public function finalizeEvolution($evolution_id) {
        $data = [
            'status' => self::STATUS_FINALIZADA,
            'finalized_at' => date('Y-m-d H:i:s')
        ];
        return sqlUpdate('evolutions', $data, ['id' => $evolution_id]);
    }
}
```

---

### 5. Avaliações

#### Sistema Atual (Laravel)
```
app/Livewire/
├── AplicarAvaliacao.php
├── AvaliacoesUnidade.php
└── MinhasAvaliacoes.php

app/Models/
├── Avaliacao.php
├── AvaliacaoTemplate.php
├── AvaliacaoPergunta.php
└── AvaliacaoResposta.php
```

#### OpenEMR Equivalente
```
interface/forms/
├── custom/                # Formulários customizados
└── FormBuilder.php        # Construtor de formulários

library/classes/Forms/
├── CustomForm.php         # Formulário customizado
└── FormTemplate.php       # Template de formulário
```

#### Adaptações Necessárias
1. **Sistema de Templates**: OpenEMR tem, adaptar estrutura
2. **Perguntas e Respostas**: Criar estrutura customizada
3. **Revisão**: Adicionar sistema de revisão

**Código de Adaptação**:
```php
// Criar: library/classes/Forms/AssessmentCustom.php
class AssessmentCustom extends CustomForm {
    
    // Criar avaliação a partir de template
    public function createFromTemplate($template_id, $patient_id, $professional_id) {
        $template = $this->getTemplate($template_id);
        $assessment_id = $this->createAssessment($patient_id, $professional_id);
        
        // Criar perguntas e respostas
        foreach ($template->questions as $question) {
            $this->createQuestion($assessment_id, $question);
        }
        
        return $assessment_id;
    }
    
    // Salvar respostas
    public function saveAnswers($assessment_id, $answers) {
        foreach ($answers as $question_id => $answer) {
            $data = [
                'assessment_id' => $assessment_id,
                'question_id' => $question_id,
                'answer' => $answer,
                'answered_at' => date('Y-m-d H:i:s')
            ];
            sqlInsert('assessment_answers', $data);
        }
    }
}
```

---

### 6. Documentos

#### Sistema Atual (Laravel)
```
app/Http/Controllers/DocumentoController.php
app/Models/Documento.php
```

#### OpenEMR Equivalente
```
interface/filemanager/
├── index.php              # Interface de documentos
└── upload.php             # Upload de documentos

library/classes/Document/
└── Document.php           # Classe de documentos
```

#### Adaptações Necessárias
1. **Estrutura de Pastas**: OpenEMR usa `sites/default/documents/`
2. **Permissões**: Adaptar sistema de permissões
3. **Categorização**: OpenEMR tem categorias, adaptar

**Código de Adaptação**:
```php
// Modificar: library/classes/Document/DocumentCustom.php
class DocumentCustom extends Document {
    
    // Upload de documento
    public function uploadDocument($patient_id, $file, $category, $unit_id) {
        $upload_dir = $GLOBALS['OE_SITE_DIR'] . "/documents/{$patient_id}/";
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $filename = $this->generateFilename($file);
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $data = [
                'pid' => $patient_id,
                'unit_id' => $unit_id,
                'category' => $category,
                'filename' => $filename,
                'filepath' => $filepath,
                'uploaded_by' => $_SESSION['authUserID'],
                'uploaded_at' => date('Y-m-d H:i:s')
            ];
            return sqlInsert('documents', $data);
        }
        
        return false;
    }
}
```

---

### 7. Relatórios

#### Sistema Atual (Laravel)
```
app/Livewire/
├── RelatorioFrequencia.php
└── RelatorioProdutividade.php

app/Services/DashboardService.php
```

#### OpenEMR Equivalente
```
interface/reports/
├── custom_report.php      # Relatórios customizados
└── report_template.php    # Templates de relatórios

library/classes/Report/
└── Report.php             # Classe de relatórios
```

#### Adaptações Necessárias
1. **Relatórios Customizados**: Criar relatórios específicos
2. **Filtros por Unidade**: Adicionar filtros
3. **Exportação**: OpenEMR tem, adaptar formatos

**Código de Adaptação**:
```php
// Criar: library/classes/Report/ReportCustom.php
class ReportCustom extends Report {
    
    // Relatório de frequência
    public function getFrequencyReport($unit_id, $start_date, $end_date) {
        $sql = "SELECT 
                    DATE(pc_eventDate) as date,
                    COUNT(*) as total_appointments,
                    COUNT(CASE WHEN pc_apptStatus = 'completed' THEN 1 END) as completed
                FROM openemr_postcalendar_events
                WHERE unit_id = ?
                AND pc_eventDate BETWEEN ? AND ?
                GROUP BY DATE(pc_eventDate)";
        
        return sqlStatement($sql, [$unit_id, $start_date, $end_date]);
    }
    
    // Relatório de produtividade
    public function getProductivityReport($unit_id, $professional_id, $start_date, $end_date) {
        $sql = "SELECT 
                    u.fname, u.lname,
                    COUNT(DISTINCT e.id) as total_evolutions,
                    COUNT(DISTINCT a.id) as total_assessments
                FROM users u
                LEFT JOIN evolutions e ON e.professional_id = u.id
                LEFT JOIN assessments a ON a.professional_id = u.id
                WHERE u.unit_id = ?
                AND (e.created_at BETWEEN ? AND ? OR a.created_at BETWEEN ? AND ?)
                GROUP BY u.id";
        
        return sqlStatement($sql, [$unit_id, $start_date, $end_date, $start_date, $end_date]);
    }
}
```

---

### 8. Dashboard

#### Sistema Atual (Laravel)
```
app/Livewire/
├── DashboardAdmin.php
├── DashboardCoordenador.php
└── DashboardSecretaria.php

app/Services/DashboardService.php
```

#### OpenEMR Equivalente
```
interface/main/dashboard.php
library/classes/Dashboard/
└── Dashboard.php          # Classe de dashboard
```

#### Adaptações Necessárias
1. **Widgets Customizados**: Criar widgets específicos
2. **Filtros por Role**: Adaptar dados por role
3. **Filtros por Unidade**: Adicionar filtros

**Código de Adaptação**:
```php
// Criar: library/classes/Dashboard/DashboardCustom.php
class DashboardCustom extends Dashboard {
    
    public function getDashboardData($user) {
        $data = [];
        
        // Admin vê todas as unidades
        if ($user->role === 'admin') {
            $data['appointments_today'] = $this->getAppointmentsCount(null, date('Y-m-d'));
            $data['patients_total'] = $this->getPatientsCount(null);
            $data['evolutions_pending'] = $this->getEvolutionsPending(null);
        } else {
            // Outros roles filtram por unidade
            $data['appointments_today'] = $this->getAppointmentsCount($user->unit_id, date('Y-m-d'));
            $data['patients_total'] = $this->getPatientsCount($user->unit_id);
            $data['evolutions_pending'] = $this->getEvolutionsPending($user->unit_id);
        }
        
        // Profissional vê apenas seus próprios
        if ($user->role === 'profissional') {
            $data['my_appointments_today'] = $this->getMyAppointmentsCount($user->id, date('Y-m-d'));
            $data['my_evolutions_pending'] = $this->getMyEvolutionsPending($user->id);
        }
        
        return $data;
    }
}
```

---

## 📋 Tabela de Mapeamento Completo

| Sistema Atual | OpenEMR | Status | Prioridade |
|--------------|---------|--------|------------|
| `User.php` | `library/classes/User/User.php` | ✅ Adaptar | CRÍTICA |
| `ScopeUnit.php` | `library/classes/ACL/ACL.php` | 🔧 Criar | CRÍTICA |
| `Atendimento.php` | `library/classes/Calendar/CalendarEvent.php` | ✅ Adaptar | CRÍTICA |
| `AgendaBoard.php` | `interface/main/calendar/index.php` | ✅ Adaptar | CRÍTICA |
| `Paciente.php` | `library/classes/Patient/Patient.php` | ✅ Adaptar | CRÍTICA |
| `ListaPacientes.php` | `interface/patient_file/summary/demographics.php` | ✅ Adaptar | CRÍTICA |
| `Evolucao.php` | `library/classes/Forms/ClinicalNote.php` | 🔧 Criar | CRÍTICA |
| `FormEvolucao.php` | `interface/forms/clinical_notes/form.php` | 🔧 Criar | CRÍTICA |
| `Avaliacao.php` | `library/classes/Forms/CustomForm.php` | 🔧 Criar | MÉDIA |
| `Documento.php` | `library/classes/Document/Document.php` | ✅ Adaptar | MÉDIA |
| `RelatorioFrequencia.php` | `interface/reports/custom_report.php` | 🔧 Criar | BAIXA |
| `DashboardService.php` | `library/classes/Dashboard/Dashboard.php` | 🔧 Criar | MÉDIA |

**Legenda**:
- ✅ Adaptar: Componente existe, precisa adaptar
- 🔧 Criar: Componente não existe, precisa criar
- ❌ Remover: Componente não será usado

---

## 🚀 Estratégia de Migração

### Fase 1: Componentes Base
1. Usuários e Permissões
2. Sistema de Unidades
3. Agendamentos básicos

### Fase 2: Componentes Principais
4. Pacientes
5. Evoluções
6. Documentos

### Fase 3: Componentes Secundários
7. Avaliações
8. Relatórios
9. Dashboard

---

## 📝 Notas Importantes

1. **Sempre manter compatibilidade** com upgrades do OpenEMR
2. **Documentar todas as customizações**
3. **Testar cada componente** isoladamente
4. **Fazer backup** antes de cada alteração
5. **Seguir padrões** do OpenEMR para facilitar manutenção

