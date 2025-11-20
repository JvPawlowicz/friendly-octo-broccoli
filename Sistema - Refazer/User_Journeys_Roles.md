# User Journeys por Papel – Equidade+ Nova Stack

Documento para orientar desenvolvimento focado em experiência de cada tipo de usuário, garantindo que os fluxos críticos estejam cobertos e mapeando integrações necessárias.

---

## 1. Admin Global
- **Objetivo**: gerenciar sistema, usuários, unidades e configurações gerais.
- **Fluxos principais**:
  1. **Monitorar saúde**: acessa dashboard global, verifica notificações, status sistema.
  2. **Configurar branding**: altera nome/logo/cores e valida que UI refletiu.
  3. **Gerenciar usuários**: cria novo coordenador; define unidades; envia reset senha.
  4. **Auditar ações**: consulta logs por usuário, exporta CSV.
  5. **Backups**: dispara backup manual e baixa arquivo para verificação.
  6. **Manutenção**: ativa modo manutenção, comunica equipe via mural.
- **Dependências**:
  - Painel admin (Refine).
  - Módulos `Settings`, `Users`, `Audit`, `Backups`, `Notifications`.
- **Pain points a evitar**:
  - Falta de feedback ao salvar configurações.
  - Falta de filtros avançados em usuários/logs.

---

## 2. Coordenador de Unidade
- **Objetivo**: supervisionar equipe da unidade, garantir operação fluida.
- **Fluxos principais**:
  1. **Visão do dia**: abre dashboard filtrado pela unidade; identifica pendências.
  2. **Gestão de agenda**: ajusta bloqueios/feriados, resolve conflitos de horários.
  3. **Revisar evoluções**: acessa lista de pendentes, lê conteúdo, marca como revisada.
  4. **Relatórios**: gera produtividade por profissional, exporta e compartilha.
  5. **Notificações**: envia comunicados internos (ex.: mudança de sala).
  6. **Suporte à equipe**: responde mensagens no chat (e.g., trocas de horário).
- **Dependências**:
  - Módulos agenda, evoluções, relatórios, notificações, chat.
  - Policies: acesso apenas à própria unidade.
- **Pain points a evitar**:
  - Interface sobrecarregada (filtrar dados da unidade).
  - Falta de histórico de revisões.

---

## 3. Profissional (Terapeuta, Psicólogo etc.)
- **Objetivo**: conduzir atendimentos, registrar evolução e avaliações.
- **Fluxos principais**:
  1. **Agenda pessoal**: verifica horários e status dos pacientes do dia.
  2. **Atendimento**: marca paciente como concluído ao terminar sessão.
  3. **Evolução**: recebe pendência, preenche relato/conduta, salva rascunho, finaliza.
  4. **Avaliação**: inicia avaliação a partir de template, finaliza e assina.
  5. **Prontuário**: consulta histórico e documentos de pacientes próprios.
  6. **Comunicação**: usa chat para alinhar com coordenador/secretaria.
- **Dependências**:
  - Agenda, evoluções, avaliações, pacientes, chat.
  - Permissões restritivas (somente pacientes atendidos).
- **Pain points a evitar**:
  - Processos lentos na evolução (autosave confiável, interface clean).
  - Dificuldade em encontrar informações-chave do paciente.

---

## 4. Secretaria
- **Objetivo**: organizar agenda, cadastrar pacientes e apoiar operação diária.
- **Fluxos principais**:
  1. **Consulta agenda**: filtra por profissional/sala para montar o dia.
  2. **Agendamento**: cria novos atendimentos (paciente novo ou existente).
  3. **Cadastros**: registra novos pacientes com dados básicos e responsáveis.
  4. **Confirmações**: altera status (confirmado, check-in, cancelado).
  5. **Documentos**: envia laudos/bilhetes fornecidos pelos responsáveis.
  6. **Notificações**: recebe comunicados da coordenação.
- **Dependências**:
  - Agenda (CRUD completo), pacientes (acesso parcial), documentos.
  - Políticas que ocultam conteúdos clínicos sensíveis.
- **Pain points a evitar**:
  - UI complexa para cadastro rápido.
  - Falta de visibilidade sobre horários livres.

---

## 5. Journeys Específicas (Cenários)
### 5.1 Marcar atendimento emergencial
1. Secretária localiza slot livre via timeline lateral.
2. Agenda paciente emergencial com prioridade.
3. Notificação enviada ao profissional + coordenador.
4. Profissional visualiza no dashboard.

### 5.2 Revisão semanal da coordenação
1. Coordenador gera relatório de faltas.
2. Analisa evoluções pendentes >72h.
3. Envia mensagem via chat para profissionais em atraso.
4. Agenda reunião (notificação de mural).

### 5.3 Onboarding de novo terapeuta
1. Admin cria usuário no painel.
2. Coordenador define disponibilidade e cor padrão.
3. Profissional recebe email com link de reset senha.
4. Após login, completa perfil, define unidade padrão.
5. Agenda exibe slots baseados na disponibilidade.

### 5.4 Auditoria clínica
1. Admin acessa logs filtrando paciente crítico.
2. Baixa relatório CSV das ações (criação, edição, download).
3. Exporta evoluções/avaliações em PDF para evidência.
4. Registra parecer no sistema (notificação interna).

---

## 6. Requisitos UX derivados
- Riibbon de status (pendências, alertas) visíveis por jornada.
- Filtros persistentes adaptados ao papel.
- Menus contextualizados (ex.: secretaria prioriza agenda e pacientes, profissional prioriza evoluções).
- Ajuda contextual (tooltips, links para manual).

---

## 7. Checklist de Validação por Papel
- **Admin**: consegue executar todos CRUDs admin, backups, auditorias, modo manutenção.
- **Coordenador**: agenda filtra apenas unidade, revisão de evoluções funciona, relatórios exportam.
- **Profissional**: fluxos agenda→evolução→avaliação completos, timeline paciente acessível.
- **Secretaria**: cadastro paciente, agendamento, upload documento, alteração status.

---

> Atualize este documento com novos papéis ou fluxos específicos conforme evoluírem os requisitos.

