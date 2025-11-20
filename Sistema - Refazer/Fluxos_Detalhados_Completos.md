# Fluxos Detalhados Completos – Equidade+ Nova Stack

Documento que detalha todos os fluxos do sistema passo a passo, incluindo validações, estados, transições e tratamento de erros.

---

## 1. Autenticação e Primeiro Acesso

### 1.1 Login
**Fluxo:**
1. Usuário acessa `/login`
2. Sistema verifica status (online/manutenção) via `status.get`
3. Se manutenção: exibe mensagem, apenas admin pode logar
4. Usuário preenche email e senha
5. Frontend valida formato (Zod client-side)
6. Chama `auth.login` (tRPC)
7. Backend valida credenciais (Argon2id)
8. Se válido:
   - Gera JWT access token (15min) + refresh token (7d)
   - Salva tokens em HttpOnly cookies
   - Retorna dados do usuário + unidades disponíveis
9. Se >1 unidade: exibe modal de seleção
10. Se 1 unidade: define automaticamente
11. Redireciona para `/dashboard`

**Validações:**
- Email formato válido
- Senha não vazia
- Rate limit: 5 tentativas/min por IP

**Erros:**
- `INVALID_CREDENTIALS`: "Email ou senha incorretos"
- `ACCOUNT_LOCKED`: "Conta bloqueada. Contate o administrador"
- `MAINTENANCE_MODE`: "Sistema em manutenção. Apenas administradores podem acessar"

---

### 1.2 Recuperação de Senha
**Fluxo:**
1. Usuário clica "Esqueci minha senha" em `/login`
2. Preenche email
3. Chama `auth.forgotPassword`
4. Backend:
   - Verifica se email existe
   - Gera token único (UUID)
   - Salva em `password_reset_tokens` (expira em 30min)
   - Envia email com link `/reset-password?token=xxx`
5. Usuário clica no link
6. Frontend valida token via `auth.validateResetToken`
7. Se válido: exibe formulário de nova senha
8. Usuário define nova senha (validação: min 8 chars, maiúscula, número)
9. Chama `auth.resetPassword`
10. Backend:
   - Valida token e expiração
   - Hash nova senha (Argon2id)
   - Atualiza `User.password_hash`
   - Invalida token
   - Invalida todas as sessões ativas (logout forçado)
11. Redireciona para `/login` com mensagem de sucesso

**Validações:**
- Token válido e não expirado
- Nova senha diferente da anterior
- Senha forte (min 8 chars, maiúscula, número, especial)

---

### 1.3 Troca de Unidade (Switcher)
**Fluxo:**
1. Usuário clica no dropdown de unidade na sidebar
2. Lista unidades disponíveis (via `auth.me`)
3. Usuário seleciona nova unidade
4. Chama `settings.updateProfile` com `defaultUnitId`
5. Backend atualiza `UserPreference.default_unit_id`
6. Frontend atualiza contexto (React Context)
7. Todas as queries subsequentes filtram por nova unidade
8. Recarrega dados do dashboard/agenda automaticamente

**Validações:**
- Usuário tem acesso à unidade selecionada (verifica `UserUnit`)

---

## 2. Agenda

### 2.1 Criar Agendamento
**Fluxo:**
1. Usuário clica em slot vazio na agenda OU clica "Novo agendamento"
2. Modal abre com:
   - Data: hoje (ou data do slot clicado)
   - Hora início: horário clicado (ou atual)
   - Hora fim: início + duração padrão (60min, editável)
3. Usuário preenche:
   - Paciente (obrigatório, busca com autocomplete)
   - Profissional (obrigatório)
   - Sala (opcional)
   - Observações (opcional)
4. Frontend valida:
   - Fim > início
   - Data não no passado (exceto admin)
5. Chama `appointments.checkConflicts` antes de salvar
6. Se conflito:
   - Exibe aviso com horários alternativos sugeridos
   - Permite forçar (apenas admin/coordenador)
7. Chama `appointments.create`
8. Backend:
   - Valida permissões (secretária/profissional pode criar)
   - Verifica conflitos (sobreposição mesmo profissional)
   - Verifica bloqueios (feriados, indisponibilidade)
   - Cria registro com status "scheduled"
   - Loga ação em `AuditLog`
9. Frontend:
   - Fecha modal
   - Atualiza agenda (TanStack Query invalida cache)
   - Toast: "Agendamento criado com sucesso"

**Validações:**
- Paciente e profissional obrigatórios
- Horário não no passado (exceto admin)
- Sem conflitos (mesmo profissional, mesmo horário)
- Sem bloqueios (feriados, indisponibilidade)

**Erros:**
- `CONFLICT`: "Horário já ocupado. Sugestões: [horários]"
- `PAST_DATE`: "Não é possível agendar no passado"
- `BLOCKED_TIME`: "Horário bloqueado (feriado/indisponibilidade)"

---

### 2.2 Concluir Atendimento → Criar Evolução
**Fluxo:**
1. Profissional/Coordenador marca atendimento como "concluído"
2. Chama `appointments.complete`
3. Backend:
   - Atualiza status para "completed"
   - Dispara evento (Prisma trigger ou EventEmitter)
   - Cria `Evolution` com status "pendente":
     - `appointment_id` vinculado
     - `patient_id`, `professional_id`, `unit_id` copiados
     - `status = 'pendente'`
   - Cria `PatientTimelineEvent` (tipo "evolution_created")
   - Loga ação
4. Frontend:
   - Atualiza badge de "Evoluções pendentes"
   - Toast: "Evolução pendente criada"
   - Opcional: redireciona para editor de evolução

**Validações:**
- Apenas profissional do atendimento ou coordenador/admin pode concluir
- Atendimento deve estar em status "scheduled" ou "confirmed"

---

### 2.3 Mover Agendamento (Drag & Drop)
**Fluxo:**
1. Usuário arrasta agendamento para novo horário
2. Frontend calcula novo `start_at` e `end_at` (mantém duração)
3. Chama `appointments.checkConflicts` com novo horário
4. Se sem conflito:
   - Chama `appointments.update` com novos horários
   - Backend valida e atualiza
   - Frontend atualiza UI (optimistic update)
5. Se conflito:
   - Reverte posição visual
   - Exibe toast de erro com sugestões

**Validações:**
- Mesmas validações de criar agendamento
- Permissão para editar (criador ou admin/coordenador)

---

## 3. Evoluções

### 3.1 Preencher Evolução (com Autosave)
**Fluxo:**
1. Profissional acessa "Evoluções pendentes"
2. Clica em evolução pendente
3. Editor abre com campos:
   - Relato clínico (rich text)
   - Conduta
   - Objetivos
   - Observações internas (visível apenas coord/admin)
4. Auto-save a cada 30 segundos:
   - Chama `evolutions.autosave` (não altera status)
   - Indicador visual: "Salvando..." → "Salvo às 14:32"
5. Usuário pode salvar manualmente (Ctrl+S)
6. Ao fechar: pergunta se deseja salvar rascunho

**Validações:**
- Campos obrigatórios (configurável por unidade)
- Rich text sanitizado (remover scripts, manter formatação)

---

### 3.2 Finalizar Evolução
**Fluxo:**
1. Profissional preenche evolução
2. Clica "Finalizar"
3. Modal de confirmação:
   - "Ao finalizar, não será possível editar. Deseja continuar?"
   - Campo de "assinatura" (texto confirmatório, ex.: "Confirmo que li e concordo")
4. Usuário confirma
5. Chama `evolutions.finalize`
6. Backend:
   - Valida campos obrigatórios
   - Atualiza status para "finalizada"
   - Salva `finalized_at` e `signature_meta` (JSON com timestamp, IP)
   - Atualiza `PatientTimelineEvent` (marca como finalizada)
   - Notifica coordenador (cria `Notification`)
   - Loga ação
7. Frontend:
   - Bloqueia edição (campos readonly)
   - Toast: "Evolução finalizada com sucesso"
   - Badge de pendentes atualiza

**Validações:**
- Todos os campos obrigatórios preenchidos
- Assinatura confirmatória fornecida
- Apenas autor pode finalizar (ou admin)

**Erros:**
- `MISSING_FIELDS`: "Preencha todos os campos obrigatórios"
- `ALREADY_FINALIZED`: "Evolução já foi finalizada"

---

### 3.3 Revisar Evolução (Coordenador)
**Fluxo:**
1. Coordenador acessa "Evoluções para revisar"
2. Visualiza evolução finalizada
3. Pode adicionar comentário interno (não visível ao profissional)
4. Clica "Marcar como revisada"
5. Chama `evolutions.review`
6. Backend:
   - Atualiza `reviewed_at` e `reviewed_by`
   - Status permanece "finalizada" (não muda)
   - Notifica profissional (cria `Notification`)
7. Frontend atualiza badge

---

## 4. Avaliações

### 4.1 Criar Template (Admin)
**Fluxo:**
1. Admin acessa `/admin/assessment-templates`
2. Clica "Novo template"
3. Preenche:
   - Nome (ex.: "Anamnese Inicial")
   - Categoria
   - Campos (JSON schema):
     ```json
     {
       "fields": [
         {"type": "text", "label": "Nome completo", "required": true},
         {"type": "date", "label": "Data de nascimento"},
         {"type": "select", "label": "Gênero", "options": ["M", "F", "Outro"]}
       ]
     }
     ```
4. Preview em tempo real
5. Salva via `assessmentTemplates.create`
6. Backend valida schema JSON e salva

**Validações:**
- Nome único por unidade (ou global se `unit_id = null`)
- Schema JSON válido
- Pelo menos 1 campo

---

### 4.2 Preencher Avaliação (Profissional)
**Fluxo:**
1. Profissional acessa "Avaliações" → "Nova avaliação"
2. Seleciona template
3. Seleciona paciente
4. Formulário dinâmico renderiza campos do template
5. Preenche campos (auto-save a cada 30s)
6. Validação em tempo real (campos obrigatórios)
7. Clica "Finalizar"
8. Confirmação + assinatura
9. Chama `assessments.finalize`
10. Backend:
    - Valida respostas contra template
    - Salva `responses` (JSON)
    - Status = "finalizada"
    - Cria `PatientTimelineEvent`
11. Frontend atualiza timeline do paciente

---

## 5. Pacientes

### 5.1 Criar Paciente
**Fluxo:**
1. Secretária/Coordenador acessa "Pacientes" → "Novo"
2. Modal de cadastro mínimo:
   - Nome (obrigatório)
   - Data de nascimento (opcional)
   - CPF (opcional, validação)
   - Telefone (opcional)
3. Salva via `patients.create`
4. Backend:
   - Valida CPF único (se fornecido)
   - Cria registro
   - Loga ação
5. Frontend:
   - Fecha modal
   - Redireciona para perfil do paciente (ou mantém na lista)

**Validações:**
- Nome obrigatório
- CPF válido (se fornecido) e único
- Email válido (se fornecido)

---

### 5.2 Upload de Documento
**Fluxo:**
1. Usuário acessa perfil do paciente → aba "Documentos"
2. Clica "Adicionar documento"
3. Seleciona arquivo (PDF, JPG, PNG)
4. Frontend valida:
   - Tipo MIME permitido
   - Tamanho máximo (10MB)
5. Upload via `patients.uploadDocument` (tRPC com FormData)
6. Backend:
   - Valida arquivo
   - Salva em `storage/app/public/unit_{id}/documents/{patient_id}/{filename}`
   - Cria registro em `PatientDocument`
   - Cria `PatientTimelineEvent` (tipo "document")
7. Frontend atualiza lista de documentos

**Validações:**
- Tipo: PDF, JPG, PNG apenas
- Tamanho: max 10MB
- Permissão: secretária/coordenador/admin

---

### 5.3 Visualizar Timeline
**Fluxo:**
1. Usuário acessa perfil do paciente → aba "Timeline"
2. Chama `patients.getTimeline`
3. Backend:
   - Busca `PatientTimelineEvent` ordenado por `occurred_at DESC`
   - Agrupa por tipo (evolução, avaliação, documento)
   - Retorna com metadados (autor, data, resumo)
4. Frontend renderiza timeline vertical com:
   - Ícone por tipo
   - Data/hora
   - Autor
   - Link para item completo (se aplicável)

---

## 6. Relatórios

### 6.1 Visualizar Relatório de Produtividade
**Fluxo:**
1. Coordenador/Admin acessa "Relatórios" → "Produtividade"
2. Seleciona filtros:
   - Período (de/até)
   - Unidade (se admin)
   - Profissional (opcional)
3. Chama `reports.productivity`
4. Backend:
   - Agrega `Appointment` por profissional
   - Calcula: total atendimentos, média/dia, taxa de conclusão
   - Retorna dados para gráfico
5. Frontend:
   - Renderiza gráfico (Chart.js)
   - Tabela com detalhes
   - Opção de salvar filtro como favorito

**Validações:**
- Período máximo: 1 ano (para performance)
- Permissão: coordenador (sua unidade) ou admin (todas)

---

## 7. Notificações

### 7.1 Criar Notificação (Admin)
**Fluxo:**
1. Admin acessa `/admin/notifications` → "Nova notificação"
2. Preenche:
   - Título
   - Corpo (rich text)
   - Tipo (aviso, lembrete, alerta clínico)
   - Unidade (ou todas)
   - Data de publicação (agendar)
3. Salva via `notifications.create`
4. Backend:
   - Cria `Notification`
   - Se `published_at <= now`: cria `NotificationUser` para todos os destinatários
   - Se agendada: job cron cria `NotificationUser` na data
5. Frontend atualiza lista

---

### 7.2 Visualizar Feed de Notificações
**Fluxo:**
1. Usuário clica no ícone de notificações (badge com contador)
2. Chama `notifications.list` (filtra por unidade do usuário)
3. Backend retorna notificações não expiradas
4. Frontend renderiza lista com:
   - Badge "nova" se não lida
   - Tipo (ícone/cor)
   - Data
5. Ao clicar: marca como lida via `notifications.markAsRead`
6. Badge atualiza (contador diminui)

---

## 8. Configurações

### 8.1 Atualizar Branding (Admin)
**Fluxo:**
1. Admin acessa `/admin/settings` → "Branding"
2. Edita:
   - Nome do sistema
   - Logo (upload)
   - Cores primárias
3. Salva via `settings.updateSystem`
4. Backend:
   - Atualiza `SystemSetting`
   - Invalida cache
5. Frontend:
   - Atualiza logo/nome no header imediatamente
   - Aplica novas cores (CSS variables)

---

### 8.2 Atualizar Preferências Pessoais
**Fluxo:**
1. Usuário acessa "Configurações" → "Preferências"
2. Edita:
   - Tema (claro/escuro/auto)
   - Unidade padrão
   - Visualização padrão da agenda (dia/semana/mês)
   - Cor do profissional (para agenda)
3. Salva via `settings.updateProfile`
4. Backend atualiza `UserPreference`
5. Frontend aplica mudanças imediatamente (tema, agenda)

---

## 9. Backups

### 9.1 Executar Backup Manual (Admin)
**Fluxo:**
1. Admin acessa `/admin/backups`
2. Clica "Executar backup agora"
3. Chama `backups.run`
4. Backend:
   - Executa `pg_dump` (PostgreSQL dump)
   - Compacta em ZIP
   - Salva em `storage/app/backups/backup_{timestamp}.zip`
   - Cria registro em `BackupRecord`
   - Retorna URL de download (temporária, expira em 15min)
5. Frontend:
   - Exibe progresso (se possível)
   - Ao concluir: botão "Baixar backup"
   - Toast: "Backup criado com sucesso"

**Validações:**
- Apenas admin
- Limite: 1 backup manual a cada 1 hora (evitar sobrecarga)

---

### 9.2 Backup Automático Diário
**Fluxo:**
1. Railway cron job executa diariamente (ex.: 2h da manhã)
2. Chama API route `/api/cron/backup` (protegida com secret)
3. Backend executa mesmo processo de backup manual
4. Mantém últimos 14 backups (deleta mais antigos)
5. Envia notificação ao admin se falhar

---

## 10. Logs e Auditoria

### 10.1 Visualizar Logs (Admin)
**Fluxo:**
1. Admin acessa `/admin/audit-logs`
2. Filtros:
   - Data (de/até)
   - Usuário
   - Ação (create/update/delete)
   - Entidade (Patient, Appointment, etc.)
3. Chama `audit.list`
4. Backend:
   - Busca `AuditLog` com filtros
   - Paginação (50 por página)
5. Frontend renderiza tabela com:
   - Data/hora
   - Usuário
   - Ação
   - Entidade + ID
   - IP
   - Link para detalhes (payload JSON)

**Validações:**
- Apenas admin
- Período máximo: 1 ano (performance)

---

## 11. Tratamento de Erros Global

### 11.1 Erros de Rede
- **Sintoma**: Timeout ou conexão perdida
- **Ação**: Toast "Erro de conexão. Tentando novamente..." + retry automático (3 tentativas)

### 11.2 Erros de Validação
- **Sintoma**: `ZodError` ou `VALIDATION_ERROR`
- **Ação**: Exibe erros inline nos campos do formulário

### 11.3 Erros de Permissão
- **Sintoma**: `FORBIDDEN` ou `UNAUTHORIZED`
- **Ação**: Toast "Você não tem permissão para esta ação" + redireciona se necessário

### 11.4 Erros de Servidor
- **Sintoma**: `500` ou `INTERNAL_ERROR`
- **Ação**: Toast "Erro interno. Contate o suporte." + loga erro (Sentry)

---

## 12. Estados e Transições

### 12.1 Estados de Agendamento
```
scheduled → confirmed → checked_in → in_progress → completed
                ↓
            cancelled (pode ocorrer em qualquer estado exceto completed)
```

### 12.2 Estados de Evolução
```
pendente → rascunho → finalizada → revisada
              ↑
         (pode voltar a rascunho antes de finalizar)
```

### 12.3 Estados de Avaliação
```
em_preenchimento → finalizada → revisada
```

---

> **Nota**: Todos os fluxos devem ser testados via E2E (Playwright) para garantir funcionamento correto.

