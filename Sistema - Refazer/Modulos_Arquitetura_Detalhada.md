# Arquitetura Detalhada por Módulo – Equidade+ Nova Stack

Objetivo: descrever como cada módulo funciona, quais dados consome/exposta, interações internas/externas e passos práticos para implementação. Use este documento como guia de desenvolvimento e alinhamento entre back, front e produto.

---

## 1. Autenticação & Gestão de Sessão
- **Propósito**: validar credenciais, emitir tokens JWT, controlar unidade ativa e preferências de sessão.
- **Componentes**:
  - Front: `LoginForm` (shadcn/ui), `UnitSelectorModal` (Dialog), middleware Next.
  - Back: `authRouter` (tRPC), `AuthService`, `UsersService`.
  - Banco: `users`, `user_preferences`, `login_logs`.
- **Fluxo**:
  1. Usuário envia credenciais via `trpc.auth.login`.
  2. Backend valida hash (Argon2id), gera access/refresh tokens, retorna dados do usuário.
  3. Front grava cookies HttpOnly e solicita unidade ativa (`trpc.auth.me`).
  4. Middleware Next garante presença do token e redireciona para `/login` caso inválido.
- **Interações**:
  - Depende de `UsersModule` (dados do usuário, roles).
  - Alimenta `AuditLog` (login) e `login_logs`.
  - Fornece contexto de unidade (`UnitScopeGuard`) para todos os módulos subsequentes.
- **Considerações**:
  - Rate limit em login/reset.
  - Reset senha via email (SMTP).
  - Rotacionar refresh tokens ao cada uso.

---

## 2. Unidades, Salas e Escopo
- **Propósito**: controlar tenants/unidades, salas, feriados e bloqueios (agenda).
- **Componentes**:
  - Front: `UnitSwitcher` (shadcn/ui Select), telas de configuração (admin).
  - Back: `unitsRouter` (tRPC), `SettingsService`.
  - Banco: `units`, `rooms`, `unit_settings`, `professional_schedules`.
- **Fluxo**:
  1. Admin cria/edita unidade via painel.
  2. Usuários selecionam unidade ativa; guard injeta `unitId`.
  3. Agenda, pacientes e demais módulos filtram por `unit_id`.
- **Interações**:
  - Eleva `unit_id` como chave em todos os domínios (pacientes, agendamentos).
  - Bloqueios/feriados consultados pelo módulo de Agenda.
  - Settings da unidade influenciam relatórios, backups, branding (white-label per unidade no futuro).
- **Considerações**:
  - Possibilidade de usuários multi-unidade via tabela `user_units`.
  - Fuso horário por unidade (salvar `timezone`).

---

## 3. Agenda Inteligente
- **Propósito**: organizar atendimentos, respeitando disponibilidade de profissionais e salas.
- **Componentes**: descritos em `Modulos_Componentes_Rotas.md`.
- **Fluxo**:
  1. Usuário abre `/agenda` → front chama `/appointments` com filtros.
  2. Slots renderizados com cores do profissional (`user_preferences.professional_color`).
  3. Criação/edição/cancelamento dispara mutações (`POST/PATCH /appointments`).
  4. `PATCH /appointments/:id/status` controla fluxo (agendado → concluído).
  5. `POST /appointments/:id/complete` dispara evento `AppointmentCompleted`.
- **Interações**:
  - Dependência de `PatientsModule` (paciente vinculado).
  - `EvolutionModule`: cria pendência automaticamente.
  - `NotificationsModule`: pode gerar alerta para profissional.
  - `ReportsModule`: usa dados para produtividade/frequência.
- **Considerações**:
  - Validação de indisponibilidade (consultar `professional_schedules` + bloqueios).
  - Logs de alteração (quem agendou, cancelou).
  - Future: sugestão de horários livres (algoritmo).

---

## 4. Evoluções Clínicas
- **Propósito**: registrar evolução pós atendimento com aprovação hierárquica.
- **Componentes**: ver documento de módulos.
- **Fluxo**:
  1. Evento `AppointmentCompleted` cria `evolution` status `pending`.
  2. Profissional edita rascunho (`/evolutions/:id` autosave).
  3. Finalização (`POST /evolutions/:id/finalize`) salva assinatura e bloqueia edição.
  4. Coordenador/Admin revisa (`POST /evolutions/:id/review`).
  5. Timeline do paciente atualizada.
- **Interações**:
  - Usa `PatientsModule` (dados do paciente).
  - `TimelineModule`: adiciona evento.
  - `ReportsModule`: métricas de evoluções.
  - `AuditModule`: registra finalização/revisão.
- **Considerações**:
  - Armazenar conteúdo estruturado (JSON) para futuras análises.
  - Addendums permitidos após finalização (registrados na timeline).
  - Garantir compliance LGPD (logs de acesso).

---

## 5. Avaliações e Templates
- **Propósito**: coletar dados estruturados (anamnese, protocolos) via formulários dinâmicos.
- **Fluxo**:
  1. Admin cria template no painel (campos JSON).
  2. Profissional inicia avaliação para paciente (`POST /assessments`).
  3. Autosave durante preenchimento.
  4. Finalização com assinatura simples.
  5. Revisão (coordenador) opcional, timeline atualizada.
- **Interações**:
  - `AssessmentTemplate` com `unit_id` (personalização por unidade).
  - `PatientsModule` (associação).
  - `ReportsModule` (indicadores clínicos).
  - Pode disparar `Notifications` (pendência para revisão).
- **Considerações**:
  - Versionamento: avaliação guarda `template_version`.
  - Templates inativos não aparecem para novos registros, mas versões antigas permanecem acessíveis.
  - Validação dos campos deve ocorrer front e back (zod + pipes).

---

## 6. Pacientes, Documentos e Timeline
- **Propósito**: centralizar prontuário, documentos, contatos e histórico clínico.
- **Fluxo**:
  1. Cadastro mínimo (nome, unidade). Demais campos opcionais.
  2. Upload de documentos com metadados (categoria, descrição).
  3. Timeline agrega eventos de evoluções, avaliações, documentos, avisos.
  4. Alertas (alergias etc.) exibidos no header.
- **Interações**:
  - `DocumentsModule` → storage S3.
  - `TimelineModule` alimentado por evoluções, avaliações, notificações.
  - `ReportsModule` usa dados na frequência/indicadores.
  - `AuditLog` registra acessos e downloads.
- **Considerações**:
  - Uploads validados por tipo/tamanho.
  - Possibilidade de marcar pacientes como inativos.

---

## 7. Relatórios e Business Intelligence
- **Propósito**: fornecer métricas operacionais e clínicas com filtros personalizáveis.
- **Fluxo**:
  1. Usuário seleciona relatório e filtros (unidade, período, profissional).
  2. Front chama `/reports/{type}` para obter dados agregados.
  3. Relatórios gerados em tempo real (sem exportação).
- **Interações**:
  - `Appointments`, `Evolutions`, `Assessments`, `Patients`, `Units`.
  - Preferências salvas em `user_preferences.report_filters`.
- **Considerações**:
  - Performance: usar agregações SQL, índices, caching.
  - No futuro: permitir agendamento de relatórios (jobs).

---

## 8. Configurações do Sistema
- **Propósito**: personalizar branding, preferências, unidades, usuários, backups.
- **Fluxo**:
  - Perfil & Preferências: usuários atualizam dados pessoais, tema, agenda default.
  - Agenda: admin/coordenador define duração padrão, horários úteis, bloqueios.
  - Branding/Sistema: admin altera logo, cores, status (online/manutenção).
  - Backups: admin gera/baixa restaura.
- **Interações**:
  - Atualiza `system_settings`, `user_preferences`, `units`, `rooms`.
  - `BackupsModule` integra com job BullMQ + S3.
  - Alterações disparam eventos para limpar caches e notificar front.
- **Considerações**:
  - Toda alteração relevante gera log de auditoria.
  - Painel admin complementa (CRUD avançado).
  - Restaurar backup precisa de fluxo cuidadoso (talvez offline).

---

## 9. Notificações
- **Propósito**: manter comunicação operacional e avisos clínicos sem depender de serviços externos.
- **Fluxo**:
  1. Admin/coordenador cria aviso no mural (`trpc.notifications.create`).
  2. Sistema cria `NotificationUser` para destinatários (por unidade ou todos).
  3. Usuários visualizam feed (`trpc.notifications.list`); marcam como lido (`trpc.notifications.markAsRead`).
  4. Notificações importantes podem aparecer no dashboard.
- **Interações**:
  - `AuditLog` registra criação de notificações (metadados) para compliance.
  - `Timeline` pode incorporar notificações clínicas específicas.
  - `Reports` podem considerar notificações (ex.: contagem de avisos críticos).
- **Considerações**:
  - Badge de contador atualizado via polling leve (TanStack Query).
  - Notificações podem ser agendadas (campo `published_at`).

---

## 10. Auditoria & Logs
- **Propósito**: rastrear ações críticas para segurança e compliance.
- **Fluxo**:
  1. Observers nos modelos (paciente, evolução, avaliação, config) criam entradas em `audit_logs`.
  2. Acesso via `/configuracoes/logs` (front) ou `/admin/audit-logs` (painel).
  3. Visualização via tRPC (`trpc.audit.list`) com filtros.
- **Interações**:
  - `Auth` (logins), `Backups`, `Settings`, `Documents`.
  - Integra com monitoramento (Sentry) para correlacionar erros e ações.
- **Considerações**:
  - Guardar IP, user agent, timestamp.
  - Retenção mínima 12 meses (arquivar após).
  - Garantir que logs não exponham dados sensíveis inteiros (mascarar).

---

## 11. Painel Administrativo (shadcn/ui + TanStack Table)
- **Propósito**: acelerar operações administrativas (CRUDs, configurações avançadas).
- **Fluxo**:
  1. Admin acessa `/admin` → páginas com TanStack Table carregam dados via tRPC.
  2. Operações diretas via tRPC (mesmos routers com verificação de role admin).
  3. Customizações complexas (ex.: builder de template) com componentes shadcn/ui dedicados.
- **Interações**:
  - Compartilha autorização com app principal (middleware tRPC verifica role admin).
  - Reflete em tempo real nas demais interfaces (ex.: template ativo aparece para profissionais).
  - Pode disparar notificações (ex.: novo usuário criado).
- **Considerações**:
  - Garantir consistência visual (shadcn/ui custom).
  - Logging pesado (cada operação).
  - Feature flags (ex.: habilitar módulos por unidade no futuro).

---

## 12. Backups & Recuperação
- **Propósito**: assegurar recuperação de dados e conformidade.
- **Fluxo**:
  1. Job diário `BackupJob` faz dump Postgres + zip storage (S3).
  2. Admin pode disparar backup manual (UI).
  3. Registro em `backup_records` com metadados.
  4. Restore: fluxo controlado (apenas admin, com double confirmation).
- **Interações**:
  - Depende de acesso S3 (credentials).
  - `AuditLog` registra execuções.
  - `Notifications` pode avisar sucesso/falha.
- **Considerações**:
  - Testar restore periodicamente (staging).
  - Clientes podem solicitar dados (LGPD) – usar backups como base.

---

## 13. Health Checks & Observabilidade
- **Propósito**: monitorar disponibilidade e diagnosticar problemas.
- **Fluxo**:
  - API expõe `/health` (db, redis, queue).
  - Landing/login exibe status do sistema (online/manutenção/offline).
  - Sentry/Axiom coletam erros; Railway alertas configurados.
- **Interações**:
  - `Settings` controla status exibido.
  - `Notifications` pode alertar sobre manutenções.
- **Considerações**:
  - Health check deve ser leve (<100ms).
  - Adicionar endpoint `/metrics` (prometheus) no futuro se necessário.

---

## 14. Integração entre Módulos (Resumo)
- **Agenda → Evolução → Timeline**: fluxo automatizado a partir da conclusão do atendimento.
- **Avaliações → Timeline → Relatórios**: dados estruturados alimentam indicadores.
- **Configurações → Todos os módulos**: branding, preferências e duração padrão replicam.
- **Backups/Auditoria**: transversais, acompanham todas as ações.
- **Notificações**: suportam comunicação operacional e avisos clínicos.

---

## 15. Boas Práticas de Implementação
- Manter camadas separadas (Routers -> Services -> Prisma).
- Usar DTOs/Zod compartilhados para garantir contratos.
- Registrar eventos principais no bus (Nest EventEmitter).
- Documentar novos fluxos neste arquivo ao evoluir o sistema.
- Incluir exemplos de payload/resposta em testes de integração para facilitar manutenção.

---

> Atualizar sempre que um módulo ganhar novas responsabilidades ou depender de integrações adicionais. Este documento serve como mapa mental e técnico para a reescrita, garantindo que todos entendam como cada parte se conecta.

