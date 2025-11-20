# Fluxos de Sequência – Equidade+ Nova Stack

Representação textual dos principais fluxos para orientar implementação e testes. Pode ser convertido em diagramas UML (PlantUML ou Mermaid) caso desejado.

---

## 1. Fluxo Agenda → Evolução
```
Secretaria -> Frontend: criar agendamento (POST /appointments)
Frontend -> API (AppointmentsController): validações (disponibilidade, unit scope)
API -> Prisma: salva appointment
API -> EventEmitter: "appointment.created"
Frontend <- API: retorno 201

Profissional -> Frontend: concluir atendimento (POST /appointments/:id/complete)
Frontend -> API: completa atendimento
API -> Prisma: atualiza status = completed
API -> EventEmitter: emite AppointmentCompleted
EventListener -> Prisma: cria Evolution status=pending
EventListener -> Prisma: adiciona TimelineEvent
Frontend <- API: OK
Front Dashboard -> API: GET /dashboard/pending (evolution pendente aparece)

Profissional -> Frontend: abre evolução pendente
Frontend -> API: GET /evolutions/:id (dados)
Profissional -> Frontend: edita conteúdo (autosave)
Frontend -> API: POST /evolutions/:id/autosave
API -> Prisma: atualiza content json
Profissional -> Frontend: finaliza
Frontend -> API: POST /evolutions/:id/finalize
API -> Prisma: atualiza status=finalized, signature
API -> EventEmitter: EvolutionFinalized
EventListener -> Prisma: TimelineEvent finalizado
EventListener -> Notifications: notificar coordenador?
```

---

## 2. Fluxo Avaliação (Template -> Preenchimento -> Revisão)
```
Admin -> Painel Refine: cria template (POST /assessment-templates)
API -> Prisma: salva template versionado
Profissional -> Frontend: inicia avaliação (POST /assessments)
API -> Prisma: cria assessment status=draft, responses={}
Frontend: renderiza campos conforme template.fields
Profissional -> Frontend: salva progresso (autosave)
Frontend -> API: PATCH/POST /assessments/:id (responses json)
Profissional -> Frontend: finaliza (POST /assessments/:id/finalize)
API -> Prisma: status=finalized, signature, finalized_at
API -> Event: AssessmentFinalized -> TimelineEvent
Coordenador -> Frontend: revisa (POST /assessments/:id/review)
API -> Prisma: status=reviewed, reviewed_at
Coordenador -> Frontend: gera PDF (GET /assessments/:id/pdf)
API -> BullMQ: enfileira GeneratePdfJob
Worker -> Prisma: recupera dados
Worker -> @react-pdf: renderiza pdf
Worker -> S3: upload file
Worker -> API webhook: export complete
Frontend <- API: retorna link para download
```

---

## 3. Fluxo Relatório com Exportação
```
Coordenador -> Frontend: seleciona filtros (unidade, período)
Frontend -> API: GET /reports/productivity?unitId=U&period=...
API -> ReportService: consulta agregações (Prisma groupBy)
API -> Frontend: retorna dados (gráfico, tabela)

Coordenador -> Frontend: exportar CSV
Frontend -> API: POST /reports/productivity/export
API -> Prisma: cria ReportExport status=pending
API -> BullMQ: enfileira export job
Worker -> Prisma: busca filtros
Worker -> ReportService: gera dataset
Worker -> CSV Writer: cria arquivo temporário
Worker -> S3: envia `exports/<id>.csv`
Worker -> Prisma: update ReportExport status=completed, expires_at
Worker -> API webhook: notifica finalização (emitir notificação usuário)
Frontend -> Polling: GET /reports/exports/:id -> status completed
Frontend: apresenta botão de download (S3 signed URL)
```

---

## 4. Fluxo Notificação Interna
```
Coordenador -> Frontend: cria notificação (POST /notifications)
API -> Prisma: salva notification (unit_id, body)
API -> NotificationsService: cria NotificationUser entries (todos usuários da unidade)
API -> Event: NotificationCreated -> Chat/Badge
Frontend -> Web: badge incrementado
Usuário -> Frontend: marca como lida (POST /notifications/:id/read)
API -> Prisma: atualiza NotificationUser.read_at
Frontend -> Dashboard: lista notificações não lidas
```

---

## 5. Fluxo Backup Automático
```
Scheduler (cron) -> API (BackupsService): enqueue backup job
BullMQ -> Worker: process backup
Worker -> Command: pg_dump -> gera arquivo .sql
Worker -> S3: upload `backups/<date>.sql.gz`
Worker -> Optional: zip storage attachments (ou manter incremental)
Worker -> Prisma: cria BackupRecord (status=completed, checksum)
Worker -> Notifications: envia mensagem para admin
Admin -> UI: vê backup listado, baixa arquivo
```

---

## 6. Fluxo Chat (Polling)
```
Profissional -> Frontend: abre chat (GET /messages/threads)
Frontend -> API: retorna lista de threads (DM + Unidade)
Frontend: inicia polling a cada 3s (GET /messages?threadId=XYZ&since=timestamp)
API -> Prisma: busca mensagens novas
Frontend: renderiza mensagens, scroll bottom
Profissional -> Frontend: envia mensagem (POST /messages)
Frontend -> API: salva mensagem
API -> Prisma: cria message, atualiza `read_at=null` para destinatário
API -> Notifications: opcional -> badge
Destinatário -> Frontend: polling detecta nova mensagem
Destinatário -> Frontend: abre thread, POST /messages/read
API -> Prisma: marca `read_at` = now
```

---

## 7. Fluxo Go-Live (simplificado)
```
Admin -> Painel: ativa modo manutenção (PUT /settings/system)
Sistema Legado: bloqueado para novas entradas
Scripts export -> arquivo JSON/CSV
Scripts import -> API (ou Prisma) -> popula novo banco
QA -> Frontend: valida fluxos
Admin -> Painel: desativa modo manutenção, comunica usuários
```

---

> Utilize estes fluxos como base para escrever testes de integração/E2E e para orientar desenvolvimento front/back sobre a ordem correta das chamadas e efeitos colaterais.

