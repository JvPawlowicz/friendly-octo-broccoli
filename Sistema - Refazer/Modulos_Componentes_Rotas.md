# Módulos, Componentes e Rotas – Equidade+ Nova Stack

Documento complementar ao blueprint principal para detalhar cada módulo funcional, componentes frontend/backend envolvidos, rotas (Next.js e tRPC procedures) e integrações. Use como referência de implementação e controle de paridade com o sistema atual.

---

## 1. Autenticação & Sessão
- **Frontend (Next.js)**
  - Páginas: `app/(auth)/login`, `app/(auth)/forgot-password`, `app/(auth)/reset-password`.
  - Componentes (shadcn/ui):
    - `LoginForm` (react-hook-form + zod, feedback de erro).
    - `UnitSelectorModal` (Dialog com lista de unidades disponíveis após login).
    - `SessionProvider` (contexto com dados do usuário, unidade ativa, preferências).
  - Cliente tRPC: `trpc.auth.login`, `trpc.auth.logout`, `trpc.auth.me`.
  - Middleware `middleware.ts` protege rotas internas (verifica cookie JWT).
- **Backend (tRPC)**
  - Router `authRouter` em `packages/api/src/routers/auth.ts`:
    - `auth.login` (mutation)
    - `auth.refresh` (mutation)
    - `auth.logout` (mutation)
    - `auth.forgotPassword` (mutation)
    - `auth.resetPassword` (mutation)
    - `auth.me` (query)
  - Middleware tRPC: `authMiddleware` (verifica JWT), `roleMiddleware` (verifica roles), `unitMiddleware` (injeta unit_id).
  - Serviços:
    - `AuthService` (hash Argon2id, JWT access/refresh).
    - `UnitContextService` (resolve unidade ativa).
- **Requisitos específicos**
  - Rate limit 5 tentativas/min por IP (middleware Next.js ou tRPC).
  - Reset senha com token expira em 30 min.
  - Sessão expira 15 min (access) / 7 dias (refresh).
  - Controle de unidade ativa armazenado em cookie + banco (`user_preferences.default_unit_id`).

---

## 2. Dashboard
- **Frontend**
  - Página: `app/(protected)/dashboard/page.tsx`.
  - Componentes (shadcn/ui):
    - `DashboardLayout`.
    - `KpiCard` (Card customizado), `TrendChart` (Chart.js), `PendingList`, `NotificationCard`.
    - Hooks: `useDashboardData` (wrapper do trpc.dashboard.summary), `useUnitFilter`.
  - Cliente tRPC: `trpc.dashboard.summary`, `trpc.dashboard.trends`, `trpc.dashboard.badges`.
- **Backend (tRPC)**
  - Router `dashboardRouter` em `packages/api/src/routers/dashboard.ts`:
    - `dashboard.summary` (query: unit_id, period)
    - `dashboard.trends` (query: unit_id, period)
    - `dashboard.badges` (query: retorna contadores de pendências)
  - Serviço `DashboardService` agregando dados de `appointments`, `evolutions`, `assessments`, `notifications`.
- **Detalhes**
  - Diferenciar métricas por role (admin vs coordenador vs profissional vs secretária).
  - Cache via TanStack Query (staleTime: 5min) para agregações pesadas.

---

## 3. Agenda Inteligente
- **Frontend**
  - Diretorio: `app/(protected)/agenda`.
  - Componentes (shadcn/ui):
    - `AgendaView` (layout principal).
    - `CalendarGrid` (dia/semana/mês, @dnd-kit/core para drag & drop).
    - `SchedulerToolbar` (filtros, atalhos).
    - `AppointmentModal` (Dialog com formulário).
    - `AppointmentCard`.
    - `AvailabilitySidebar`.
  - Hooks:
    - `useAgendaFilters` (persistência em localStorage).
    - `useAppointmentForm` (react-hook-form + zod).
  - Cliente tRPC: `trpc.appointments.list`, `trpc.appointments.create`, `trpc.appointments.update`, `trpc.appointments.delete`, `trpc.appointments.complete`, `trpc.appointments.checkConflicts`, `trpc.appointments.suggestTimes`.
- **Backend (tRPC)**
  - Router `appointmentsRouter` em `packages/api/src/routers/appointments.ts`:
    - `appointments.list` (query: filtros)
    - `appointments.create` (mutation)
    - `appointments.update` (mutation)
    - `appointments.delete` (mutation)
    - `appointments.updateStatus` (mutation)
    - `appointments.complete` (mutation: cria evolução pendente)
    - `appointments.checkConflicts` (query: valida horário)
    - `appointments.suggestTimes` (query: sugere horários livres)
  - Serviços:
    - `AppointmentsService` (CRUD, validações).
    - `AvailabilityService` (bloqueios, feriados).
  - Eventos:
    - Prisma trigger ou EventEmitter: ao concluir atendimento → cria `Evolution` pendente.
- **Regras específicas**
  - Validação de sobreposição (`WHERE start_at < :end AND end_at > :start`).
  - Duração padrão configurável (env + `system_settings`).
  - Bloqueios por calendário (salas/unidade).
  - Drag & drop (procedure recebe novo `startAt`, `endAt`, valida antes de confirmar).

---

## 4. Pacientes & Prontuário
- **Frontend**
  - Diretório: `app/(protected)/pacientes`.
  - Páginas:
    - `/pacientes/page.tsx` (lista).
    - `/pacientes/[id]/page.tsx` (detalhe).
  - Componentes:
    - `PatientTable`, `PatientFilters`.
    - `PatientProfileHeader`, `ClinicalAlerts`.
    - `PatientInfoForm`, `GuardianList`.
    - `DocumentUploader`, `DocumentGallery`.
    - `Timeline` (compartilhado).
  - Rotas Next:
    - `GET /app/api/patients`.
    - `POST /app/api/patients`.
    - `PUT /app/api/patients/[id]`.
    - `POST /app/api/patients/[id]/documents`.
    - `DELETE /app/api/patients/[id]/documents/[docId]`.
    - `GET /app/api/patients/[id]/timeline`.
- **Backend**
  - `PatientsModule`, `DocumentsModule`, `TimelineModule`.
  - Endpoints:
    - `GET /api/v1/patients`.
    - `POST /api/v1/patients`.
    - `GET /api/v1/patients/:id`.
    - `PUT /api/v1/patients/:id`.
    - `POST /api/v1/patients/:id/documents`.
    - `DELETE /api/v1/patients/:id/documents/:docId`.
    - `GET /api/v1/patients/:id/timeline`.
  - Armazenamento:
    - Upload via S3 (prefixo `unit_{id}/patients/{patientId}/docs`).
- **Regras específicas**
  - Secretária tem acesso limitado (sem campos clínicos sensíveis, policy filtra).
  - Tags/diagnósticos como array (`diagnosis_tags`).
  - Timeline agrupa eventos (evolução, avaliação, documento, notificação).
  - Auditoria de downloads em `audit_logs`.

---

## 5. Evoluções
- **Frontend**
  - Diretório: `app/(protected)/evolucoes`.
  - Componentes:
    - `EvolutionsTabs` (pendentes, minhas, unidade, revisadas).
    - `EvolutionTable`.
    - `EvolutionEditor` (rich text com autosave).
    - `SignatureModal`.
    - `ReviewModal`.
  - Cliente tRPC: `trpc.evolutions.list`, `trpc.evolutions.getById`, `trpc.evolutions.autosave`, `trpc.evolutions.finalize`, `trpc.evolutions.review`.
- **Backend (tRPC)**
  - Router `evolutionsRouter` em `packages/api/src/routers/evolutions.ts`:
    - `evolutions.list` (query: status filter)
    - `evolutions.getById` (query)
    - `evolutions.autosave` (mutation)
    - `evolutions.finalize` (mutation)
    - `evolutions.review` (mutation)
  - Eventos:
    - Prisma trigger ou EventEmitter: `EvolutionFinalizedEvent` → triggers timeline + notificações.
- **Regras específicas**
  - Autosave a cada 10s (apenas se mudanças detectadas).
  - Finalização exige texto de confirmação + hora/dia.
  - Review disponível para coordenador/admin.
  - Addendums sob demanda (`POST /evolutions/:id/addendum`).

---

## 6. Avaliações
- **Frontend**
  - Diretório: `app/(protected)/avaliacoes`.
  - Componentes:
    - `AssessmentList`, `AssessmentStatusBadge`.
    - `AssessmentForm` (renderiza campos dinamicamente).
    - `TemplateBuilder` (Admin) com drag & drop (`dnd-kit`).
    - `TemplatePreview`.
  - Cliente tRPC: `trpc.assessmentTemplates.list`, `trpc.assessmentTemplates.create`, `trpc.assessmentTemplates.update`, `trpc.assessments.list`, `trpc.assessments.create`, `trpc.assessments.finalize`.
- **Backend (tRPC)**
  - Router `assessmentTemplatesRouter` em `packages/api/src/routers/assessment-templates.ts`:
    - `assessmentTemplates.list` (query)
    - `assessmentTemplates.create` (mutation, admin only)
    - `assessmentTemplates.update` (mutation, admin only)
  - Router `assessmentsRouter` em `packages/api/src/routers/assessments.ts`:
    - `assessments.list` (query)
    - `assessments.create` (mutation)
    - `assessments.getById` (query)
    - `assessments.finalize` (mutation)
    - `assessments.review` (mutation)
- **Regras**
  - Templates versionados (campo `version` + `isActive`).
  - Campos com validação `zod` por tipo (text, number, date, select, checkbox).
  - Profissional só visualiza avaliações próprias (policy).

---

## 7. Relatórios
- **Frontend**
  - Diretório: `app/(protected)/relatorios`.
  - Componentes (shadcn/ui):
    - `ReportTabs` (Clínico, Produtividade, Frequência, Unidade).
    - `ReportFilters`, `SavedFilters`.
    - `ReportChart` (Chart.js), `ReportTable` (TanStack Table).
  - Cliente tRPC: `trpc.reports.frequency`, `trpc.reports.productivity`, `trpc.reports.clinical`.
- **Backend (tRPC)**
  - Router `reportsRouter` em `packages/api/src/routers/reports.ts`:
    - `reports.frequency` (query: unitId, from, to, patientId)
    - `reports.productivity` (query: unitId, from, to, professionalId)
    - `reports.clinical` (query: unitId, from, to)
- **Tipos suportados (inicialmente)**
  - `productivity`: atendimentos por profissional.
  - `frequency`: presenças vs faltas por paciente.
  - `clinical`: evoluções/avaliações finalizadas no período.
  - `unit-overview`: volume total por unidade.

---

## 8. Configurações & Sistema
- **Frontend**
  - Diretório: `app/(protected)/configuracoes`.
  - Abas e componentes:
    - `ProfileForm`, `PasswordForm`.
    - `PreferencesForm` (tema, agenda view, unidade padrão, cor).
    - `AgendaSettings` (duração padrão, bloqueios, feriados).
    - `UnitManagement` (lista + CRUD).
    - `UserManagement` (admin) – link para painel admin.
    - `SystemBranding` (logo, cores, status, mensagem).
    - `BackupManager`.
  - Rotas Next:
    - `GET /app/api/settings/profile`.
    - `PUT /app/api/settings/profile`.
    - `PUT /app/api/settings/password`.
    - `GET /app/api/settings/preferences`.
    - `PUT /app/api/settings/preferences`.
    - `GET /app/api/settings/system`.
    - `PUT /app/api/settings/system`.
    - `GET /app/api/settings/agenda`.
    - `PUT /app/api/settings/agenda`.
    - `GET /app/api/settings/backups`.
    - `POST /app/api/settings/backups/run`.
- **Backend**
  - `SettingsModule`, `UnitsModule`, `UsersModule`, `BackupsModule`.
  - Endpoints conforme rotas acima (prefira prefixos `settings/...`).
- **Worker**
  - Job `BackupJob` (daily) -> `backup_records`.
- **Regras**
  - Alterações de branding limpam cache e notificam websockets/polling.
  - Preferências atualizam `user_preferences` e refletem em UI (tema).
  - Backups só executados por admin; restore exige confirmação + auditoria.

---

## 9. Notificações
- **Frontend**
  - Diretório: `app/(protected)/notificacoes`.
  - Componentes (shadcn/ui):
    - `NotificationFeed`, `NotificationBell` (Badge com contador), `NotificationCard`.
    - `NotificationComposer` (admin/coordenador, Dialog).
  - Cliente tRPC: `trpc.notifications.list`, `trpc.notifications.create`, `trpc.notifications.markAsRead`.
- **Backend (tRPC)**
  - Router `notificationsRouter` em `packages/api/src/routers/notifications.ts`:
    - `notifications.list` (query: filtros por unidade, tipo, lido/não lido)
    - `notifications.create` (mutation, admin only)
    - `notifications.markAsRead` (mutation)

---

## 10. Logs & Auditoria
- **Frontend**
  - Tela (admin) em `app/(protected)/configuracoes/logs`.
  - Componentes (shadcn/ui): `AuditLogTable` (TanStack Table), `AuditFilters`.
  - Cliente tRPC: `trpc.audit.list`.
- **Backend (tRPC)**
  - Router `auditRouter` em `packages/api/src/routers/audit.ts`:
    - `audit.list` (query: filtros por data, usuário, ação, entidade)
  - Observers registram mudanças em modelos críticos (Pacientes, Evoluções, Avaliações, Configurações).
  - Logs de login em tabela separada `login_logs`.

---

## 11. Painel Admin (shadcn/ui + TanStack Table)
- **Recursos (rotas `/admin/*`)**
  - `/admin/users` → CRUD com TanStack Table, reset senha, impersonate (tRPC: `trpc.users.list`, `trpc.users.create`, etc.).
  - `/admin/units` → CRUD, salas, feriados (tRPC: `trpc.units.*`).
  - `/admin/assessment-templates` → builder com shadcn/ui Form.
  - `/admin/system-settings` → branding, status (tRPC: `trpc.settings.*`).
  - `/admin/backups` → listar, baixar, restaurar (tRPC: `trpc.backups.*`).
  - `/admin/audit-logs` → viewer com TanStack Table (tRPC: `trpc.audit.list`).
  - `/admin/notifications` → mural global (tRPC: `trpc.notifications.*`).
- **Autorização**
  - Rota Next `/admin/*` protegida por middleware que verifica role admin.
  - Middleware tRPC verifica permissões em cada procedure.

---

## 12. Rotas Next.js (Resumo)
| Área | Caminho | Proteção |
| --- | --- | --- |
| Login | `/login` | pública |
| Recuperar senha | `/forgot-password`, `/reset-password` | pública |
| Dashboard | `/dashboard` | `auth`, `unit` |
| Agenda | `/agenda` | `auth`, `unit`, permissões agenda |
| Pacientes | `/pacientes`, `/pacientes/[id]` | `auth`, `policy patient` |
| Evoluções | `/evolucoes` | `auth`, `policy evolutions` |
| Avaliações | `/avaliacoes` | `auth`, `policy assessments` |
| Relatórios | `/relatorios` | `auth`, `permission view-reports` |
| Notificações | `/notificacoes` | `auth`, permission |
| Configurações | `/configuracoes/*` | `auth`, role-specific |
| Painel Admin | `/admin/*` | `auth`, role admin |

---

## 13. Rotas tRPC (Resumo)
- Endpoint base: `/api/trpc`.
- Agrupamento por router:
  - `auth`, `users`, `units`, `appointments`, `evolutions`, `assessments`, `patients`, `reports`, `settings`, `notifications`, `audit`, `backups`, `health`.
- Type-safety automático via cliente tRPC tipado.

---

## 14. Componentes Compartilhados (Pacote `packages/ui`)
- `Button`, `Input`, `Select`, `Textarea`, `DatePicker`.
- `FormField` (label, hint, error).
- `Modal`, `Drawer`.
- `Card`, `KpiCard`, `StatBadge`.
- `Table` + `Pagination`.
- `Timeline`, `Tag`, `Avatar`.
- `ToastProvider`.
- `ThemeSwitcher`.
- `Sidebar`, `Header`, `UserMenu`, `UnitSwitcher`, `Clock`.
- Observação: componentes seguem design system (cores, tipografia, espaçamentos).

---

## 15. Hooks e Utils Compartilhados
- `useAuth`, `useSession`, `useUnit`.
- `useCommandPalette`.
- `usePolling`.
- Utils: `formatDate`, `formatCurrency`, `parseDuration`, `sanitizeHtml`.
- Zod schemas em `packages/schemas` (ex.: `AppointmentSchema`, `EvolutionSchema`).

---

## 16. Integrações e Serviços Externos
- **Storage Railway ou S3 Wasabi/Backblaze**: documentos, backups.
- **SMTP**: emails reset senha, notificações (Mailgun/Postmark opcional).
- **Sentry/Axiom**: monitoramento.
- **Railway**: Postgres, Redis, deploy apps.
- Sem outros serviços externos (mantendo requisito de autonomia).

---

## 17. Permissões (RBAC + Policies)
- **Roles**: `admin`, `coordenador`, `profissional`, `secretaria`.
- **Permissions extras** (Spatie-like equivalente):
  - `view-reports`, `manage-users`, `manage-settings`, `manage-templates`, `manage-backups`, `audit`.
- **Policies**:
  - `PatientPolicy`, `AppointmentPolicy`, `EvolutionPolicy`, `AssessmentPolicy`, `DocumentPolicy`, `NotificationPolicy`.
- **Escopo de unidade**: middleware injeta `unitId` a partir da sessão; Admin ignora.

---

## 18. Eventos & Jobs
- Eventos principais:
  - `AppointmentCompleted` → `CreatePendingEvolution`.
  - `EvolutionFinalized` → `AddTimelineEntry`, `NotifyCoordinator`.
  - `AssessmentFinalized` → `AddTimelineEntry`.
  - `BackupCompleted` → `LogBackup`.
- Jobs (Railway cron ou Next.js cron route):
  - `BackupJob` (diário).
  - `NotificationDigestJob` (resumo diário opcional).

---

## 19. Jobs e Cron
- Railway cron jobs ou Next.js API route `/api/cron/*` (protegida com secret).
- Jobs: backup diário, notificações digest.

---

## 20. Pendências / Próximos Detalhamentos
- Desenhar UI builder template (componentes e JSON final).
- Mapear rotas de onboarding/ajuda (FAQ interna).
- Especificar endpoints para logs de login (LGPD).

---

> Atualize este documento sempre que novas rotas, componentes ou módulos forem adicionados. Ele serve como base para implementação e revisão de paridade com a versão anterior.

