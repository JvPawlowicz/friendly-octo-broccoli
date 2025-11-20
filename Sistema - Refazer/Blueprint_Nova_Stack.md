@Sistema - Refazer

# Blueprint de Reescrita (Nova Stack 100% Railway)

## 0. Visão Geral
- **Objetivo**: reconstruir o Equidade+ preservando funcionalidades, fluxos, papéis e regras de negócio, porém adotando uma arquitetura moderna full TypeScript hospedada integralmente na Railway.
- **Abordagem**: monorepo simplificado com aplicação Next.js única (frontend + API via tRPC) compartilhando tipos e validações. Deploy contínuo em ambientes `dev`, `staging` e `prod`.
- **Stack proposta**:
  - **Backend**: tRPC (TypeScript RPC) + Next.js API Routes + Prisma ORM + PostgreSQL (Railway).
  - **Frontend App**: Next.js 15 (App Router, React Server Components) + TailwindCSS + shadcn/ui + TanStack Query + Zod.
  - **Painel Admin**: Integrado no mesmo app Next.js com rotas protegidas `/admin/*` usando shadcn/ui + TanStack Table para CRUDs.
  - **Autenticação**: NextAuth.js ou tRPC middleware com JWT (access + refresh) e suporte a roles; tokens armazenados via HttpOnly cookies.
  - **Infraestrutura**: Railway (app Next.js único + banco Postgres) com IaC (Railway templates).

## 1. Módulos e Paridade Funcional
| Módulo Atual | Responsável | Resultado esperado na nova stack |
| --- | --- | --- |
| Dashboard (role-aware) | Frontend + tRPC `dashboard.*` | Cards/KPIs, gráficos, atalhos; dados consumidos via procedures tRPC com type-safety. |
| Agenda Inteligente | Frontend `/agenda` + tRPC `appointments.*` | Visão dia/semana/mês, arrastar soltar, filtros persistentes, auto-criação de evoluções ao concluir. |
| Pacientes | Frontend `/pacientes` + tRPC `patients.*` | CRUD com dados clínicos, documentos, linha do tempo agregando evoluções, avaliações, documentos, notificações. |
| Evoluções | Frontend `/evolucoes` + tRPC `evolutions.*` | Criação automática, editor com autosave, assinatura, revisão por coordenador. |
| Avaliações | Frontend `/avaliacoes` + Admin (templates) + tRPC `assessments.*` | Templates dinâmicos, preenchimento autosave, finalização, timeline. |
| Relatórios | Frontend `/relatorios` + tRPC `reports.*` | Filtros avançados, visualização com gráficos Chart.js (sem exportação). |
| Configurações | Frontend `/configuracoes` + Admin `shadcn/ui` + tRPC `settings.*` | Branding, usuários, unidades, preferências pessoais, backups, feriados, agenda padrão. |
| Notificações internas | Frontend `/notificacoes` + tRPC `notifications.*` | Feed e mural, marcadores lido/não lido. |
| Logs & Auditoria | Painel admin `/admin/logs` + tRPC `audit.*` | Histórico completo, filtros. |
| Backups & Saúde | Painel admin `/admin/backups`, page `/status` | Execução jobs backup, ver status sistema, health checks. |

## 2. Arquitetura Alvo
- **Monorepo (Turborepo)** com workspaces:
  - `apps/web` (Next.js único: frontend + API tRPC + admin)
  - `packages/ui` (componentes shadcn/ui compartilhados)
  - `packages/config` (eslint, tsconfig, tailwind)
  - `packages/api` (tRPC routers, procedures, validações Zod)
  - `packages/db` (Prisma schema, client, migrations)
- **Fluxo geral**:
  1. Usuário acessa `apps/web` (SSR/SR); autenticação via cookies.
  2. Frontend chama procedures tRPC via cliente tipado (type-safety end-to-end).
  3. Admin acessa rotas `/admin/*` no mesmo app com permissões adicionais.
  4. Background jobs (backups) executados via cron jobs Next.js ou Railway cron.
- **Comunicação**: tRPC procedures (type-safe) via HTTP. Sem sockets externos; sem polling desnecessário.

## 3. Camadas Técnicas
### 3.1 Backend (tRPC + Next.js API Routes)
- **Routers tRPC** (um por domínio): `auth`, `users`, `units`, `patients`, `appointments`, `evolutions`, `assessments`, `reports`, `settings`, `notifications`, `audit`, `backups`.
- **Prisma**: `schema.prisma` com modelos baseados nas entidades atuais (ver Seção 5).
- **Autorização**: Middleware tRPC para roles + escopo de unidade. Procedures verificam permissões antes de executar. **IMPORTANTE**: Admin tem acesso TOTAL a todas as funcionalidades, sem restrições de unidade ou escopo. Ver `Roles_Permissoes_Detalhadas.md` para detalhes completos.
- **Validação**: Zod schemas compartilhados entre client e server (type-safety automático).
- **Eventos**: EventEmitter simples ou Prisma triggers para ações assíncronas (ex.: criar evolução ao concluir atendimento).
- **Documentação**: tRPC Playground (`/api/trpc-playground`) para testar procedures.

### 3.2 Frontend Web (Next.js App Router)
- **Pastas chave**: `app/(protected)/dashboard`, `agenda`, `pacientes`, `evolucoes`, `avaliacoes`, `relatorios`, `configuracoes`, `admin/*`.
- **Autenticação**: Roteamento protegido via middleware (verifica cookie JWT). Revalidação de sessão server-side.
- **State/Data**: Cliente tRPC tipado + `TanStack Query` para caching; `Zod` para validação client-side.
- **UI**: Tailwind + shadcn/ui (componentes React modernos, acessíveis, customizáveis). Layout persistente com sidebar (clock + selector de unidade).
- **Acessibilidade**: suporte a teclado, ARIA, dark mode toggle, fonte `Inter`.

### 3.3 Painel Admin (Next.js + shadcn/ui)
- **Objetivo**: substituir Filament com CRUDs rápidos usando shadcn/ui + TanStack Table.
- **Módulos**: usuários, unidades, salas, templates de avaliação, configurações de sistema, backups, logs, notificações.
- **Data fetching**: Cliente tRPC tipado com hooks customizados (`useUsers`, `useUnits`, etc.).
- **Theme**: shadcn/ui customizado conforme design system (cores, tipografia).

### 3.4 Jobs & Cron
- **Jobs**:
  - Geração de evolução pendente (síncrono via evento Prisma).
  - Envio de notificações internas (resumo diário via cron).
  - Backup diário (dump Postgres via Railway cron ou Next.js cron route).
- **Schedule**: Railway cron jobs ou Next.js API route com `node-cron` para tarefas agendadas.

## 4. Infraestrutura Railway
- **Serviços**:
  - `equidade-app` (Next.js único) -> build com `next build` (nixpacks detecta automaticamente).
  - `equidade-postgres` (PostgreSQL 16 gerenciado).
- **Ambientes**:
  - `development`: uso local com Railway CLI (`railway run`), banco/dev seeds.
  - `staging`: pré-produção com seed limitada e dados mascarados.
  - `production`: dados reais, backups automáticos.
- **Deploy**:
  - Integração GitHub -> Railway auto deploy por branch (`main` -> prod, `develop` -> staging).
  - Templates Railway para replicar stack (Infra as code).
- **Secrets**: geridos centralmente (JWT secrets, SMTP, DATABASE_URL).
- **Observabilidade**: logs via Railway + integração com Sentry/Axiom; health checks expostos em `/api/health`.

## 5. Modelo de Dados (Prisma)
Resumo das principais tabelas (campos simplificados):
- `User`: id, name, email, password_hash, role(enum), primary_unit_id, professional_color, status.
- `UserUnit`: (pivot) user_id, unit_id, role_override (opcional).
- `Unit`: id, name, address, timezone, settings(json), is_active.
- `Room`: id, unit_id, name, capacity.
- `Patient`: id, unit_id, name, birthdate, documents(json metadata), diagnosis_tags, allergies, medications, crisis_plan, notes, guardian_contacts(json).
- `Appointment`: id, unit_id, room_id, professional_id, patient_id, start_at, end_at, status(enum), category_id, notes, created_by, cancelled_by.
- `AppointmentCategory`: id, unit_id, name, color.
- `Evolution`: id, appointment_id, patient_id, professional_id, unit_id, status(enum), content(json), signature_meta(json), finalized_at, reviewed_at, reviewed_by.
- `EvolutionAddendum`: id, evolution_id, author_id, body, created_at.
- `AssessmentTemplate`: id, unit_id|null, name, version, fields(json schema), category, is_active.
- `Assessment`: id, template_id, patient_id, professional_id, unit_id, status, responses(json), signature_meta, finalized_at, reviewed_at.
- `PatientTimelineEvent`: id, patient_id, type(enum), reference_id, unit_id, occurred_at, meta(json).
- `Notification`: id, unit_id|null, title, body, type(enum), created_by, published_at, expires_at.
- `NotificationUser`: pivot com flags de leitura.
- `AuditLog`: id, user_id, action, entity_type, entity_id, payload(json), ip, user_agent, created_at.
- `SystemSetting`: id, key, value (json); ex.: system_name, status, colors.
- `BackupRecord`: id, filename, size_bytes, created_at, created_by, location.
- `UserPreference`: id, user_id, theme, agenda_view, default_unit_id, agenda_duration.

Indices críticos: `(unit_id, start_at)` em agendamentos, `(patient_id, created_at)` em timeline, `(unit_id, role)` em usuários, FTS em pacientes.

## 6. API tRPC (Procedures Principais)
- `auth.login`, `auth.refresh`, `auth.logout`, `auth.me`.
- `dashboard.summary` (unit_id, period) -> KPIs por perfil.
- `patients.list`, `patients.create`, `patients.update`, `patients.delete`, `patients.getById`, `patients.getDocuments`, `patients.getTimeline`.
- `appointments.list`, `appointments.create`, `appointments.update`, `appointments.delete`, `appointments.updateStatus`, `appointments.complete` -> dispara criação de evolução.
- `evolutions.list`, `evolutions.create`, `evolutions.update`, `evolutions.autosave`, `evolutions.finalize`, `evolutions.review`.
- `assessments.list`, `assessments.create`, `assessments.update`, `assessments.finalize`.
- `assessmentTemplates.list`, `assessmentTemplates.create`, `assessmentTemplates.update` (admin only).
- `reports.frequency`, `reports.productivity`, `reports.clinical` (filtros: período, unidade, profissional, paciente).
- `notifications.list`, `notifications.create`, `notifications.markAsRead`.
- `settings.getSystem`, `settings.updateSystem`, `settings.getProfile`, `settings.updateProfile`.
- `audit.list`, `backups.list`, `backups.run`, `backups.restore`.
- `health.check`, `status.get`.

Todas as procedures com type-safety automático via tRPC. Cliente tipado disponível no frontend.

## 7. Painel Admin (shadcn/ui + TanStack Table)
- **Recursos** (rotas `/admin/*`):
  - `users`: CRUD com tabela shadcn/ui, reset de senha, impersonate.
  - `units`: CRUD, salas, feriados e bloqueios (nested resources).
  - `assessment-templates`: builder com JSON schema + preview.
  - `system-settings`: branding, cores, mensagens, status.
  - `backups`: listar, baixar, restaurar.
  - `audit-logs`: tabela com filtros avançados (data, usuário, ação).
  - `notifications`: criar/editar, agendar publicação.
- **Permissões**: somente role `admin` acessa. Middleware Next.js verifica role antes de renderizar.
- **Componentes**: TanStack Table para listagens, shadcn/ui Form para CRUDs, DataTable customizado.

## 8. Design System e UX
- **Referência**: reutilizar princípios do blueprint atual (cores, tipografia, componentes) convertidos para Tailwind.
- **Componentes compartilhados** (`packages/ui` baseado em shadcn/ui):
  - `Sidebar`, `UnitSwitcher`, `Clock`, `KpiCard`, `DataTable` (TanStack Table), `Timeline`, `StatusBadge`, `Dialog` (modal), `Toast`, `Form` (react-hook-form + zod).
- **Interações**:
  - Autosave nas telas de evolução e avaliação (via server actions + optimistic UI).
  - Command palette (`cmd+k`) com busca global (TanStack Query + API).
  - Acessibilidade WCAG 2.2: contrastes, foco, aria.

## 9. Segurança e Compliance
- **Autenticação**: JWT (HS256 ou RS256); refresh token rotacionado; expiração de access 15min, refresh 7d.
- **Autorização**: RBAC + escopo por unidade; middleware tRPC que injeta `unit_id` ativo; queries sempre filtradas.
- **Proteção**: CSRF (cookies same-site), rate limiting (Next.js middleware ou tRPC middleware) em procedures sensíveis, validação de uploads (MIME, tamanho) com storage segregado por unidade.
- **LGPD**: princípios de minimização, logs de consentimento, campos sensíveis criptografados quando aplicável (ex.: observações internas).
- **Auditoria**: logs imutáveis com IP/UA; admins podem exportar.

## 10. Observabilidade e Operações
- **Logs**: Pino + transporte para Railway logs (JSON). Níveis: info, warn, error.
- **Tracing**: OpenTelemetry opcional (otlp -> Railway metrics ou Axiom).
- **Health checks**: `GET /api/health` (db), `GET /api/status` (utilizado na landing).
- **Alertas**: integração com Railway alerts (latência, erros, consumo de recursos).
- **Backups**: job diário via Railway cron (pg_dump + assets). Retenção configurável (ex.: 14 dias). Upload opcional para bucket S3 compatível (Wasabi/Backblaze) via credenciais Railway.

## 11. Pipeline CI/CD
- **GitHub Actions**:
  - `lint-and-test`: roda `pnpm lint`, `pnpm test` (unit + e2e) em PR.
  - `build-preview`: gera builds Next.js (vercel-like) e smoke tests.
  - `deploy-staging`: ao merge em `develop` -> Railway staging.
  - `deploy-prod`: ao tag `v*` -> Railway prod.
- **Qualidade**:
  - SonarCloud opcional para análise.
  - Husky pre-commit com lint-staged (format, lint, typecheck).

## 12. Estratégia de Migração de Dados
1. **Inventário**: extrair schema atual (MySQL) -> mapear para novo Postgres (diferenças de tipos, enums, JSON).
2. **Scripts**: preparar migração (ex.: `scripts/migrate_appointments.sql`) rodando em etapa isolada.
3. **ETL**:
   - Exportar dados atuais para CSV/SQL.
   - Converter/normalizar (ex.: tags -> arrays).
   - Importar na nova base Postgres via Prisma `db seed` customizado.
4. **Documentos/arquivos**: migrar storage atual para estrutura `unit_{id}` no novo bucket ou storage Railway.
5. **Validação**: rodar checklist com contagem de registros, amostras, timeline completa.
6. **Cutover**: modo manutenção no sistema antigo, snapshot final, importar, validar, liberar novo app.

## 13. Testes
- **Unit (API)**: services, guards, pipes, DTOs.
- **Integration (API)**: endpoints com `supertest`, cobrindo fluxos principais (login, agenda->evolução, avaliações).
- **E2E (Web)**: Playwright com cenários por papel (Admin, Coordenador, Profissional, Secretária).
- **Contract Tests**: Zod schemas compartilhados garantem compatibilidade front-back.
- **Performance**: k6 opcional para endpoints críticos (agenda, dashboard).

## 14. Roadmap de Implementação
1. **Fundação**: setup monorepo, dependências, lint, format, CI básico.
2. **Modelo de dados**: Prisma schema, migrations, seeds (roles, admin, unidade).
3. **Autenticação & RBAC**: tRPC procedures login/refresh, middleware de unidade, policies.
4. **Layouts base**: Next.js layout com sidebar, header, switcher de unidade, dark mode.
5. **Dashboard**: procedures tRPC agregadores + UI de cards/gráficos (shadcn/ui).
6. **Agenda**: CRUD completo + drag/drop + validações + criação automática de evolução.
7. **Pacientes**: CRUD, documentos, timeline (apoiado em events).
8. **Evoluções**: evento de conclusão, editor autosave, finalização.
9. **Avaliações**: templates (admin) + preenchimento (profissional).
10. **Relatórios**: procedures agregados + visualização com gráficos.
11. **Configurações**: perfil, preferências, branding, unidades, usuários.
12. **Notificações**: feed, mural, marcadores lido/não lido.
13. **Logs & Backups**: auditoria, viewer, triggers de backup.
14. **Testes & Hardening**: ampliar cobertura, smoke tests e observabilidade.
15. **Migração & Go-Live**: scripts de dados, validação, switch final.

## 15. Pendências e Decisões (ADRs Sugeridas)
- Estratégia de armazenamento de arquivos (Railway volume vs S3 externo) (ADR-002).
- Formato de timeline (events normalizados vs materialized view) (ADR-003).
- Estratégia de jobs assíncronos (Railway cron vs Next.js cron route) (ADR-006).

## 16. Próximos Passos Imediatos
1. Validar stack proposta com a equipe (ajustes em ferramentas preferidas).
2. Criar repositório monorepo com estrutura base e documentos (`README`, `CONTRIBUTING`, ADR template).
3. Configurar projeto Railway (serviços e variáveis) + conectar repositório.
4. Iniciar implementação seguindo roadmap (etapa 1 → 3).

---

> Este blueprint deve ser mantido em sincronismo com decisões futuras; sempre que uma escolha arquitetural for feita, registrar ADR e atualizar seções relevantes.

## 17. Detalhamento por Módulo (User Stories + Critérios)
### 17.1 Dashboard
- **User stories principais**
  - Como `admin`, quero visualizar KPIs agregados de todas as unidades para tomar decisões rápidas.
  - Como `coordenador`, quero ver pendências da minha unidade para priorizar ações.
  - Como `profissional`, quero saber minhas evoluções pendentes assim que entro no sistema.
- **Critérios de aceite**
  1. Cards atualizam com filtros de unidade/período.
  2. Gráficos exibem dados com fallback de "sem dados".
  3. Links levam para rotas correspondentes (agenda, evoluções, relatórios).
  4. Loading state com skeleton enquanto API responde.
- **Endpoints envolvidos**
  - `GET /dashboard/summary`
  - `GET /dashboard/trends`
  - `GET /dashboard/pending`

### 17.2 Agenda
- **User stories**
  - Secretária agenda atendimentos com poucos cliques e evita sobreposições.
  - Profissional arrasta atendimento para outro horário mantendo duração.
  - Admin altera duração padrão por unidade.
- **Critérios**
  - Modal pré-preenche horário; respeita bloqueios/profissional indisponível.
  - Drag & drop respeita validação server-side (erro visual caso conflito).
  - Concluir atendimento dispara evento que cria evolução pendente.
- **Endpoints**
  - `GET /appointments`
  - `POST /appointments`
  - `PATCH /appointments/:id/status`
  - `POST /appointments/:id/complete`
- **Eventos**
  - `AppointmentCompleted` -> `CreatePendingEvolution`

### 17.3 Pacientes
- **User stories**
  - Secretária cria cadastro mínimo com informações obrigatórias.
  - Coordenador anexa documento PDF com laudo.
  - Profissional consulta linha do tempo consolidada.
- **Critérios**
  - Validação de campos sensíveis (mascarar dados em logs).
  - Upload multi-arquivos com progresso.
  - Timeline exibe eventos ordenados por data com filtros por tipo.
- **Endpoints**
  - `POST /patients`, `PUT /patients/:id`
  - `POST /patients/:id/documents`
  - `GET /patients/:id/timeline`

### 17.4 Evoluções
- **User stories**
  - Profissional salva rascunho automático a cada X segundos.
  - Coordenador revisa e marca evolução como revisada.
- **Critérios**
  - Autosave não altera status final.
  - Finalização exige confirmação e registra timestamp/assinatura.
  - Revisão notifica autor (notificação interna).
- **Procedures tRPC**
  - `evolutions.list` (status filter)
  - `evolutions.autosave`
  - `evolutions.finalize`
  - `evolutions.review`

### 17.5 Avaliações
- **User stories**
  - Admin cria template com campos customizados (builder).
  - Profissional inicia avaliação e finaliza com assinatura.
  - Coordenador revisa e adiciona comentário interno.
- **Critérios**
  - Versionamento de template: avaliações antigas mantêm versão original.
  - Campos obrigatórios validados client + server.
  - Exportação PDF inclui cabeçalho da unidade.
- **Endpoints**
  - `POST /assessment-templates`
  - `POST /assessments`
  - `POST /assessments/:id/finalize`
  - `POST /assessments/:id/review`

### 17.6 Relatórios
- **User stories**
  - Coordenador visualiza relatório de frequência com gráficos.
  - Admin salva filtro favorito (ex.: "Produtividade semanal").
- **Critérios**
  - Filtros salvos por usuário armazenados em `user_preferences`.
  - Procedures retornam dados agregados e detalhados.
  - Visualização com gráficos Chart.js (sem exportação).
- **Procedures tRPC**
  - `reports.frequency`
  - `reports.productivity`
  - `reports.clinical`

### 17.7 Configurações
- **Submódulos**: Perfil, Preferências, Agenda, Usuários, Unidades, Sistema, Backups.
- **Critérios**
  - Alterar branding reflete imediatamente (cache invalidado).
  - Preferências persistem e influenciam UI (ex.: visão padrão agenda).
  - Logs de alterações armazenados em `audit_logs`.

### 17.8 Notificações
- **User stories**
  - Admin envia comunicado para todos os usuários da unidade.
  - Usuários visualizam notificações não lidas no feed.
- **Critérios**
  - Feed carrega paginado.
  - Notificações suportam tipos (aviso, lembrete, alerta clínico).
  - Marcação de lido atualiza em tempo real.

### 17.9 Logs & Backups
- **User stories**
  - Admin visualiza trilha de auditoria filtrando por entidade.
  - Admin executa backup manual e baixa arquivo ZIP.
- **Critérios**
  - Logs imutáveis (sem update/delete via API).
  - Backup registra operação em `backup_records`.
  - Restore exige confirmação dupla e cria log.

## 18. Contratos de API (Exemplos Simplificados)
### 18.1 Login
```
POST /api/v1/auth/login
Request:
{
  "email": "admin@equidade.test",
  "password": "Senha123!",
  "unitId": "uuid-da-unidade" // opcional, define unidade ativa
}
Response 200:
{
  "user": {
    "id": "uuid",
    "name": "Admin",
    "role": "admin",
    "units": [
      {"id": "uuid1", "name": "Unidade Central"},
      {"id": "uuid2", "name": "Unidade Norte"}
    ],
    "preferences": {...}
  }
}
Cookies: `access_token` (HttpOnly, SameSite=Lax), `refresh_token`.
```

### 18.2 Criar Agendamento
```
POST /api/v1/appointments
{
  "unitId": "uuid",
  "roomId": "uuid",
  "professionalId": "uuid",
  "patientId": "uuid",
  "startAt": "2025-12-12T14:00:00-03:00",
  "endAt": "2025-12-12T15:00:00-03:00",
  "status": "scheduled",
  "notes": "Primeira sessão",
  "categoryId": "uuid"
}
Response 201:
{
  "id": "uuid",
  "conflicts": [],
  "createdAt": "ISO",
  "createdBy": "uuid"
}
422: retorna lista de conflitos com horários sugeridos.
```

### 18.3 Autosave Evolução
```
POST /api/v1/evolutions/{id}/autosave
{
  "content": {
    "relato": "...",
    "conduta": "...",
    "objetivos": "...",
    "observacoesInternas": "..."
  }
}
Response 200: {"status": "draft_saved", "savedAt": "ISO"}
```

### 18.4 Visualizar Relatório
```
trpc.reports.productivity.query({
  unitId: "uuid",
  from: "2025-11-01",
  to: "2025-11-30"
})
Response:
{
  summary: { total: 150, average: 5.2 },
  byProfessional: [...],
  chartData: [...]
}
```

## 19. Mapa de Telas e Fluxos (Texto)
- `Login` → verificação status sistema (online/manutenção).
- `Seleção de unidade` (se >1) → grava em sessão/cookie.
- `Dashboard` com cards por role.
- `Agenda`:
  - Tabs: Dia, Semana, Mês, Lista.
  - Ações: Criar, editar, arrastar, concluir.
- `Pacientes`:
  - Lista (filtros: nome, tag, unidade).
  - Detalhe: Dados gerais, Documentos, Timeline, Avaliações.
- `Evoluções`:
  - Pendências, Finalizadas, Revisadas.
  - Editor RTE com autosave.
- `Avaliações`:
  - Templates (admin) → Form builder.
  - Preenchimento (profissional) → listagem por paciente.
- `Relatórios`:
  - Tipos: Clínico, Produtividade, Frequência, Unidade.
  - Visualização com gráficos.
- `Notificações`:
  - Feed, mural, filtros por tipo.
- `Configurações`:
  - Perfil, Preferências, Agenda, Unidades, Usuários, Sistema, Backups.
- `Painel Admin` (shadcn/ui):
  - CRUDs, logs, backups, templates.

## 20. Requisitos Não Funcionais
- **Desempenho**
  - Dashboard responde em <1500ms com dados consolidados.
  - Agenda suporta 200 agendamentos/dia/unidade sem degradação.
  - Relatórios renderizam em <2000ms.
- **Escalabilidade**
  - Instâncias Next.js stateless (escalar horizontal).
  - Jobs pesados (backups) via Railway cron.
- **Disponibilidade**
  - Meta 99.5% uptime.
  - Health checks expondo dependências críticas (DB).
- **Observabilidade**
  - Logs estruturados, tracing opcional, dashboards de métricas.
- **Segurança**
  - Password hashing Argon2id.
  - Forçar senha forte + expiração opcional.
  - MFA futura (ADR).
- **Acessibilidade**
  - WCAG 2.2 AA.
  - Teclas de atalho, readers-friendly.

## 21. Matriz de Testes (Resumo)
| Módulo | Tipo de teste | Cenários |
| --- | --- | --- |
| Autenticação | Unit + Integration | Login com credenciais válidas/inválidas, troca de unidade, refresh token inválido. |
| Agenda | E2E + Integration | Criar, editar, mover, conflito, bloqueio, concluir, gerar evolução. |
| Pacientes | Unit + E2E | CRUD, upload documento inválido, timeline correta. |
| Evoluções | E2E | Autosave, finalizar, revisão, PDF, addendum. |
| Avaliações | Unit + E2E | Template versioning, preenchimento, finalização, revisão. |
| Relatórios | Integration | Filtros, agregações, export CSV/PDF. |
| Configurações | Unit | Atualizar branding, preferências, logs. |
| Chat/Notificações | Integration | Polling, marca como lido, broadcast por unidade. |
| Backups/Logs | Integration | Executar backup, baixar, restore negado sem permissão. |

## 22. Plano de Dados & Scripts
- **Migrations**: versionadas via Prisma (`prisma/migrations`).
- **Seeds**:
  - `seed-roles.ts`: cria roles e permissões default.
  - `seed-admin.ts`: cria usuário admin e unidade principal.
  - `seed-demo.ts`: (somente dev/staging) gera pacientes, agendamentos, evoluções fake.
- **Scripts auxiliares**:
  - `scripts/export-legacy.ts`: conecta no banco antigo (MySQL) e gera CSV/JSON.
  - `scripts/import-legacy.ts`: adapta dados para Postgres.
  - `scripts/verify-migration.ts`: compara contagens entre ambientes.

## 23. Gestão de Projeto e Documentação
- **Documentos essenciais**:
  - `docs/adr/ADR-001-...md` (decisões).
  - `docs/api/openapi.yaml`.
  - `docs/ux/ui-map.md` (capturas, fluxos).
  - `docs/testing/test-plan.md`.
- **Ritos**:
  - Checkpoint semanal para validar progresso do roadmap.
  - QA regressivo a cada milestone (agenda, pacientes, evoluções...).
  - Checklist de release (migrations, jobs, testes).

## 24. Backlog Estendido (Epics → User Stories)
- **Epic A: Foundation**
  - US-A1: Como dev quero rodar monorepo com `pnpm dev` para iniciar rapidamente.
  - US-A2: Como admin quero fazer login e trocar unidade.
- **Epic B: Agenda & Evoluções**
  - US-B1: Secretária agenda consulta sem conflitos.
  - US-B2: Profissional finaliza atendimento que gera evolução.
  - US-B3: Coordenador revisa evolução pendente e marca como revisada.
- **Epic C: Pacientes & Avaliações**
  - US-C1: Secretária anexa laudos no prontuário.
  - US-C2: Profissional cria avaliação baseada em template.
  - US-C3: Admin edita template e mantém versões antigas ativas.
- **Epic D: Relatórios & Configurações**
  - US-D1: Coordenador exporta relatório de frequência.
  - US-D2: Admin ajusta branding e vê efeito instantâneo.
- **Epic E: Comunicação & Auditoria**
  - US-E1: Admin envia notificações para usuários da unidade.
  - US-E2: Admin consulta logs de ações com filtro por data.
- **Epic F: Operações & Infra**
  - US-F1: Admin executa backup manual.
  - US-F2: Sistema gera backup diário automaticamente.

## 25. Riscos e Mitigações
- **Risco**: curva de aprendizado da equipe com NestJS/Next.js.
  - *Mitigação*: pair programming inicial, documentação interna, treinamento rápido.
- **Risco**: Migração de dados complexa (dados clínicos sensíveis).
  - *Mitigação*: rodar dry-run em staging, validar com stakeholders, scripts idempotentes.
- **Risco**: Performance de agenda em unidades grandes.
  - *Mitigação*: indexes, lazy loading, paginação por dia, caching leve.
- **Risco**: Gestão de arquivos (storage).
  - *Mitigação*: ADR sobre uso de bucket S3-like; fallback volumes Railway.

## 26. Roadmap Temporal (Estimativa Macro)
- **Fase 0 (Semana 0-1)**: Setup monorepo, CI/CD, ambientes Railway.
- **Fase 1 (Semana 2-4)**: Autenticação, RBAC, unidades, dashboard básico.
- **Fase 2 (Semana 5-8)**: Agenda completa + evolução integrada.
- **Fase 3 (Semana 9-12)**: Pacientes, documentos, avaliações.
- **Fase 4 (Semana 13-15)**: Relatórios, notificações.
- **Fase 5 (Semana 16-18)**: Configurações avançadas, admin panel, backups.
- **Fase 6 (Semana 19-20)**: Testes E2E, performance, migração staging.
- **Fase 7 (Semana 21)**: Go-live, hypercare, monitoramento reforçado.

## 27. Checklist de Go-Live
1. Todos os testes (unit/integration/E2E) passando no CI.
2. Auditoria LGPD revisada (logs, consentimento, política de retenção).
3. Backups automáticos e manuais validados.
4. Scripts de migração executados com sucesso em staging.
5. Plano de rollback documentado (snapshot banco + restore app anterior).
6. Equipe treinada (admin, coord, prof, secretaria) na nova UI.
7. Monitoramento ativo (alertas Railway configurados).

---

