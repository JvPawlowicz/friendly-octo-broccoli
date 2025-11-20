# Modelo de Dados & Relacionamentos – Equidade+ Nova Stack

## 1. Visão Geral
Banco PostgreSQL gerenciado via Prisma. Todos os registros relevantes possuem `unit_id` (escopo), `created_at`, `updated_at`. Esta visão textual substitui um ER diagram formal e pode ser convertida em ferramentas como dbdiagram.io.

---

## 2. Entidades Principais
### 2.1 Users
- Campos: `id (uuid)`, `name`, `email (unique)`, `password_hash`, `role (enum)`, `primary_unit_id`, `status (active/inactive)`, `professional_color`, `created_at`, `updated_at`.
- Relacionamentos:
  - `belongsTo Unit (primary_unit_id)`.
  - `hasMany Appointments (as professional)`.
  - `hasMany Evolutions (as professional)`.
  - `hasMany Assessments (as professional)`.
  - `hasMany Messages (sent/received)`.
  - `hasMany AuditLogs`.
  - `hasOne UserPreference`.
  - `many-to-many Units` via `UserUnit`.

### 2.2 Units
- Campos: `id`, `name`, `slug`, `address`, `timezone`, `phone`, `email`, `settings (jsonb)`, `is_active`.
- Relacionamentos:
  - `hasMany Users`, `Rooms`, `Appointments`, `Patients`, `Evolutions`, `Assessments`, `Notifications`.
  - `hasMany ProfessionalSchedules`.

### 2.3 UserPreferences
- Campos: `id`, `user_id`, `theme`, `agenda_view`, `default_unit_id`, `agenda_duration`, `saved_filters (jsonb)`.
- Relacionamentos: `belongsTo User`, `belongsTo Unit (default_unit_id)`.

### 2.4 Rooms
- Campos: `id`, `unit_id`, `name`, `capacity`, `color`, `is_active`.
- Relacionamentos: `belongsTo Unit`, `hasMany Appointments`.

---

## 3. Agenda e Atendimentos
### Appointments
- Campos: `id`, `unit_id`, `room_id`, `professional_id`, `patient_id`, `start_at`, `end_at`, `status (enum)`, `category_id`, `notes`, `created_by`, `cancelled_by`, `cancelled_reason`.
- Relacionamentos:
  - `belongsTo Unit`, `Room`, `User (professional)`, `Patient`.
  - `hasOne Evolution`.
  - `belongsTo AppointmentCategory`.

### AppointmentCategories
- Campos: `id`, `unit_id`, `name`, `color`.
- Relacionamentos: `hasMany Appointments`.

### ProfessionalSchedules
- Campos: `id`, `user_id`, `unit_id`, `week_day`, `start_time`, `end_time`, `is_blocked`, `notes`.
- Uso: controlar disponibilidade e bloqueios.

---

## 4. Pacientes & Documentos
### Patients
- Campos: `id`, `unit_id`, `name`, `document`, `birthdate`, `gender`, `diagnosis_tags (string[])`, `allergies`, `medications`, `crisis_plan`, `notes`, `status`, `created_at`.
- Relacionamentos:
  - `belongsTo Unit`.
  - `hasMany Appointments`, `Evolutions`, `Assessments`, `PatientDocuments`, `TimelineEvents`.

### PatientDocuments
- Campos: `id`, `patient_id`, `unit_id`, `category`, `file_name`, `file_path`, `uploaded_by`, `uploaded_at`, `description`.
- Relacionamentos: `belongsTo Patient`, `belongsTo Unit`, `belongsTo User (uploaded_by)`.

### PatientTimelineEvents
- Campos: `id`, `patient_id`, `unit_id`, `type (enum: evolution/assessment/document/notification/note)`, `reference_id`, `title`, `meta (jsonb)`, `occurred_at`, `created_at`.
- Uso: exibir histórico consolidado.

### PatientGuardians (opcional)
- Campos: `id`, `patient_id`, `name`, `relationship`, `phone`, `email`, `notes`.

---

## 5. Evoluções
### Evolutions
- Campos: `id`, `appointment_id`, `patient_id`, `unit_id`, `professional_id`, `status (enum: pending/draft/finalized/reviewed/archived)`, `content (jsonb: relato/conduta/objetivos/observacoes)`, `signature_meta (jsonb)`, `finalized_at`, `finalized_by`, `reviewed_at`, `reviewed_by`, `archived_at`.
- Relacionamentos:
  - `belongsTo Appointment`, `Patient`, `Unit`, `User (professional)`.
  - `hasMany EvolutionAddendums`.

### EvolutionAddendums
- Campos: `id`, `evolution_id`, `author_id`, `content`, `created_at`.
- Relacionamentos: `belongsTo Evolution`, `belongsTo User`.

---

## 6. Avaliações
### AssessmentTemplates
- Campos: `id`, `unit_id (nullable para global)`, `name`, `slug`, `version`, `fields (jsonb)`, `category`, `is_active`, `created_by`.
- Relacionamentos: `hasMany Assessments`.

### Assessments
- Campos: `id`, `patient_id`, `unit_id`, `professional_id`, `template_id`, `status (enum: draft/finalized/reviewed)`, `responses (jsonb)`, `signature_meta`, `finalized_at`, `reviewed_at`, `reviewed_by`.
- Relacionamentos: `belongsTo Patient`, `Unit`, `User`, `AssessmentTemplate`.

---

## 7. Relatórios & Exportações
### ReportExports
- Campos: `id`, `unit_id`, `requested_by`, `type`, `filters (jsonb)`, `status (pending/completed/error)`, `file_path`, `expires_at`, `created_at`.
- Relacionamentos: `belongsTo User`, `Unit`.

---

## 8. Comunicação
### Messages
- Campos: `id`, `unit_id (nullable, para DM)`, `thread_id (opcional)`, `sender_id`, `receiver_id (nullable)`, `content`, `attachments (jsonb)`, `role_visibility (jsonb)`, `created_at`, `read_at`.
- Relacionamentos: `belongsTo User (sender)`, `belongsTo Unit`.

### Notifications
- Campos: `id`, `unit_id (nullable para global)`, `title`, `body`, `type (enum: info/warning/alert)`, `published_at`, `expires_at`, `created_by`.
- Relacionamentos: `belongsTo User (creator)`, `Unit`.

### NotificationUser (pivot)
- Campos: `id`, `notification_id`, `user_id`, `read_at`.

---

## 9. Auditoria & Backups
### AuditLogs
- Campos: `id`, `user_id`, `unit_id`, `action`, `entity_type`, `entity_id`, `description`, `payload (jsonb)`, `ip_address`, `user_agent`, `created_at`.
- Relacionamentos: `belongsTo User`, `Unit`.

### LoginLogs
- Campos: `id`, `user_id`, `ip_address`, `user_agent`, `result (success/failure)`, `created_at`.

### BackupRecords
- Campos: `id`, `unit_id (opcional)`, `file_path`, `size_bytes`, `created_by`, `created_at`, `status`, `checksum`.

---

## 10. Configurações
### SystemSettings
- Campos: `id`, `key`, `value (jsonb)`.
- Exemplos: `system_name`, `logo_url`, `primary_color`, `status`, `status_message`.

### FeatureFlags (opcional)
- Campos: `id`, `name`, `value (jsonb)`, `description`.

---

## 11. Índices Recomendados
- `users`: `email (unique)`, `(role, unit_id)`.
- `appointments`: `(unit_id, start_at)`, `(professional_id, start_at)`, `(patient_id, start_at)`.
- `evolutions`: `(unit_id, status)`, `(patient_id, finalized_at)`.
- `assessments`: `(patient_id, finalized_at)`, `(template_id, status)`.
- `messages`: `(unit_id, created_at)`, `(receiver_id, read_at)`.
- `notifications`: `(unit_id, published_at)`.
- `audit_logs`: `(entity_type, entity_id, created_at)`, `(user_id, created_at)`.
- `report_exports`: `(requested_by, created_at)`.

---

## 12. Estratégias de Retenção
- `audit_logs` e `login_logs`: manter 12 meses, arquivar após.
- `report_exports`: remover arquivos após `expires_at`.
- `messages`: opcionalmente arquivar conversas antigas (configurável).

---

## 13. Referências Cruzadas
- Cada módulo no blueprint referencia as tabelas correspondentes:
  - Agenda → `appointments`, `professional_schedules`, `rooms`.
  - Evoluções → `evolutions`, `evolution_addendums`, `patient_timeline_events`.
  - Avaliações → `assessment_templates`, `assessments`.
  - Pacientes → `patients`, `patient_documents`, `timeline`.
  - Relatórios → usa agregações sobre todas as anteriores.

---

> Atualize este documento sempre que o `schema.prisma` for alterado. Manter coerência entre documentação e banco evita divergências durante migração e desenvolvimento.

