# Cronograma Detalhado – Reescrita Equidade+

## Visão Geral
- Horizonte estimado: 6 meses (26 semanas) até go-live.
- Equipe: 1 tech lead, 2 dev backend, 2 dev frontend, 1 QA, 1 designer, 1 PM.
- Metodologia: Scrum quinzenal + checkpoints mensais com stakeholders clínicos.

## Fases e Marcos
| Fase | Semanas | Objetivos principais | Entregáveis |
| --- | --- | --- | --- |
| 0. Preparação | 0–1 | Setup repositório, CI/CD, ADRs iniciais | Monorepo, pipelines, documentação base |
| 1. Fundação | 2–4 | Autenticação, RBAC, unidades, dashboard inicial | Auth completa, seeds, layout base |
| 2. Agenda & Evoluções | 5–8 | Agenda smart + fluxo evolução completo | Agenda CRUD, eventos, evolução editor |
| 3. Pacientes & Avaliações | 9–12 | Prontuário, documentos, avaliações templated | Perfil paciente, timeline, builder avaliação |
| 4. Relatórios & Configs | 13–15 | Relatórios, notificações, branding, backups | Relatórios export, config sistema, admin panel MVP |
| 5. Comunicação & Auditoria | 16–18 | Chat, mural, logs, auditoria | Chat polling, notificações, visualizador audit |
| 6. Qualidade & Migração | 19–22 | Testes completos, performance, migração staging | Suite E2E, scripts migração, dry-run |
| 7. Go-live & Hypercare | 23–26 | Migração produção, monitoramento, ajustes | Cutover executado, suporte pós-lançamento |

## Roadmap Quinzenal
### Sprint 0 (Semana 0-1)
- Criar repositório, configurar Turborepo, lint, Husky.
- Subir ambientes Railway (dev/staging placeholders).
- Escrever ADRs iniciais (admin, storage, PDF, auth).
- Produzir blueprint final (feito) + plano de testes/dados (feito).

### Sprint 1 (Semana 2-3)
- Implementar autenticação (login, refresh, logout).
- Criar guard de unidade + seleção.
- Configurar Prisma schema base (users, units, roles).
- Layout Next.js (sidebar, header, theme).
- QA: testes unitários de auth, smoke front.

### Sprint 2 (Semana 4-5)
- Dashboard cards básicos (KPI hardcoded -> Service).
- Seeds: admin, coordenador, profissional, secretária.
- Configurar E2E base (Playwright login).
- Preparar design system inicial (componentes base).

### Sprint 3 (Semana 6-7)
- Agenda: listagem semana/dia, criação agendamento, filtros.
- Validações (sobreposição, bloqueio).
- Eventos Nest para `AppointmentCompleted`.
- Iniciar worker BullMQ (fila genérica).
- QA: testes integração agenda.

### Sprint 4 (Semana 8-9)
- Evolução: criação automática, editor autosave, finalização.
- PDF evolução (worker).
- Dashboard mostrar pendências.
- E2E: fluxo agenda → evolução completo.

### Sprint 5 (Semana 10-11)
- Pacientes: CRUD, documentos (upload S3), timeline.
- Configurar storage + rotinas limpeza.
- UX: alertas paciente, tags.
- QA: testes de upload, timeline.

### Sprint 6 (Semana 12-13)
- Avaliações: builder (admin), preenchimento, assinatura.
- Template versioning.
- PDF avaliação.
- E2E: fluxo avaliação completo.

### Sprint 7 (Semana 14-15)
- Relatórios: endpoints agregados (produtividade, frequência).
- Export CSV/PDF (jobs).
- Salvar filtros favoritos.
- QA: testes performance (k6) agenda/relatórios.

### Sprint 8 (Semana 16-17)
- Configurações: perfil, preferências, branding.
- Painel admin (Refine) com usuários/unidades/templates.
- Backups: job diário + UI.
- QA/UX review conjunto (designer + PM).

### Sprint 9 (Semana 18-19)
- Chat interno (polling, leitura).
- Notificações/mural.
- Audit logs viewer.
- Monitoramento (Sentry, alertas Railway).

### Sprint 10 (Semana 20-21)
- Testes: aumentar cobertura, accessibility (axe).
- Migração: scripts export/import, dry-run parcial.
- Documentação: manual usuário, suporte.

### Sprint 11 (Semana 22-23)
- Migração staging completa, validações clínicas.
- Ajustes pós feedback.
- Preparar plano cutover produção.

### Sprint 12 (Semana 24-25)
- Freeze do legado, migração final.
- Go-live (fim de semana planejado).
- Monitoramento intensivo (hypercare).

### Sprint 13 (Semana 26)
- Coleta de feedback usuários.
- Correções rápidas, priorização backlog pós-lançamento.
- Retrospectiva geral e atualização documentação.

## Dependências & Pré-requisitos
- Equipe com acesso Railway, S3, GitHub.
- Aprovação de stakeholders clínicos para design e fluxos.
- Contratação/assinatura de serviço S3 (Wasabi/Backblaze).
- Configuração SMTP (para notificações e reset senha).

## Gestão de Riscos no Cronograma
- **Desvios de escopo**: utilizar ADR + change control com PM.
- **Atrasos em migração**: iniciar scripts cedo (Sprint 10).
- **Problemas de performance**: rodar testes desde Sprint 7.
- **Recursos humanos**: mapear substitutos, documentar continuamente.

## Cadências
- Daily short remoto (15 min).
- Review/Planning quinzenal.
- Retro quinzenal.
- Sync com stakeholders (clínica) mensal.
- QA sign-off antes de mover épico para concluído.

## Indicadores de Progresso
- % de funcionalidades concluídas por épico.
- Cobertura de testes por módulo.
- Tempo médio de deploy (staging/prod).
- Número de bugs críticos detectados pós release.

## Documentos de Apoio
- Blueprint completo (`Blueprint_Nova_Stack.md`).
- ADRs em `Sistema - Refazer/ADRs`.
- Planos específicos (Testes, Dados, UX, Setup).

