# Especificação Funcional White Label – Sistema de Gestão Clínica

**Versão:** 1.0  
**Data:** 2025  
**Propósito:** Documentação funcional completa do sistema, descrevendo o que deve ter, sem especificar tecnologias ou camadas de implementação. Ideal para transformar sistemas open source neste sistema específico.

---

## 1. Identidade do Sistema

### 1.1 Nome e Branding
- O sistema deve permitir personalização completa de identidade visual:
  - Nome do sistema (configurável)
  - Logo (upload de imagem)
  - Cores primárias e secundárias
  - Mensagens personalizadas
  - Rodapé customizável
- Todas as telas devem refletir essas configurações dinamicamente
- O status do sistema (online/manutenção/offline) deve ser visível na tela de login

### 1.2 Linguagem e Terminologia
- **Unidade**: Local físico onde os atendimentos ocorrem (pode haver múltiplas unidades)
- **Profissional**: Pessoa que realiza atendimentos clínicos (terapeuta, psicólogo, etc.)
- **Paciente**: Pessoa que recebe atendimento
- **Atendimento**: Sessão agendada entre profissional e paciente
- **Evolução**: Registro clínico criado após conclusão de atendimento
- **Avaliação**: Formulário estruturado preenchido pelo profissional
- **Revisão**: Aprovação de evolução/avaliação pelo coordenador
- **Sala**: Espaço físico onde ocorre o atendimento
- **Agenda**: Calendário visual de agendamentos
- **Prontuário**: Conjunto de informações clínicas do paciente
- **Timeline**: Linha do tempo com histórico de eventos do paciente

---

## 2. Módulos Principais

### 2.1 Autenticação e Acesso

#### 2.1.1 Login
- Tela de login com email e senha
- Verificação de status do sistema antes de permitir acesso
- Em modo manutenção, apenas administradores podem acessar
- Após login bem-sucedido:
  - Se usuário tem apenas 1 unidade: define automaticamente como ativa
  - Se usuário tem múltiplas unidades: exibe modal de seleção
  - Redireciona para dashboard

#### 2.1.2 Recuperação de Senha
- Link "Esqueci minha senha" na tela de login
- Usuário informa email
- Sistema envia email com link de recuperação (válido por 30 minutos)
- Usuário define nova senha (validação: mínimo 8 caracteres, maiúscula, número, caractere especial)
- Após redefinição, todas as sessões ativas são invalidadas

#### 2.1.3 Troca de Unidade
- Seletor de unidade visível na interface (quando usuário tem múltiplas unidades)
- Ao trocar unidade, todos os dados exibidos são filtrados pela nova unidade
- A unidade selecionada é lembrada para próximos acessos

#### 2.1.4 Sessão
- Sessão expira após período de inatividade (configurável)
- Sistema deve avisar antes de expirar
- Renovação automática de sessão durante uso ativo

---

### 2.2 Dashboard

#### 2.2.1 Objetivo
- Visão geral do trabalho e saúde operacional
- Conteúdo varia conforme o papel do usuário

#### 2.2.2 Conteúdo por Papel

**Administrador:**
- KPIs globais (todas as unidades):
  - Total de atendimentos do dia
  - Evoluções pendentes
  - Pacientes ativos
  - Profissionais ativos
- Gráficos de produtividade (todas as unidades)
- Lista de notificações recentes
- Atalhos rápidos (gerenciar usuários, configurações, backups)

**Coordenador:**
- KPIs da unidade:
  - Atendimentos do dia
  - Evoluções pendentes de revisão
  - Pacientes ativos
  - Faltas do período
- Gráfico de produtividade por profissional
- Lista de pendências da unidade
- Atalhos (agenda, relatórios, notificações)

**Profissional:**
- KPIs pessoais:
  - Meus atendimentos do dia
  - Minhas evoluções pendentes
  - Próximos atendimentos
- Lista de evoluções pendentes
- Atalhos (agenda pessoal, evoluções, avaliações)

**Secretária:**
- KPIs da agenda:
  - Agendamentos do dia
  - Pendências de confirmação
  - Novos pacientes cadastrados
- Lista de tarefas pendentes
- Atalhos (agenda, pacientes, notificações)

#### 2.2.3 Funcionalidades
- Filtros por período (persistem entre sessões)
- Filtros por unidade (apenas para admin)
- Atualização automática de dados (polling ou real-time)
- Links clicáveis que levam para módulos específicos

---

### 2.3 Agenda

#### 2.3.1 Objetivo
- Gerenciar agendamentos de atendimentos clínicos
- Visualizar disponibilidade de profissionais e salas
- Evitar conflitos de horários

#### 2.3.2 Visualizações
- **Dia**: Visualização de um dia específico (horário por horário)
- **Semana**: Visualização de uma semana (dias da semana)
- **Mês**: Visualização mensal (calendário)
- **Lista**: Lista tabular de agendamentos com filtros

#### 2.3.3 Funcionalidades de Criação
- Clicar em slot vazio abre modal de criação
- Modal pré-preenche:
  - Data: data do slot clicado (ou hoje)
  - Hora início: horário do slot clicado
  - Hora fim: início + duração padrão (configurável, padrão 60 minutos)
- Campos obrigatórios:
  - Paciente (busca com autocomplete)
  - Profissional
  - Data e hora início
  - Data e hora fim
- Campos opcionais:
  - Sala
  - Observações
  - Categoria de atendimento
- Validações:
  - Verificar conflitos (mesmo profissional, mesmo horário)
  - Verificar bloqueios (feriados, indisponibilidade)
  - Não permitir agendamento no passado (exceto admin)
  - Fim deve ser posterior ao início
- Se houver conflito: exibir aviso com horários alternativos sugeridos
- Admin e coordenador podem forçar criação mesmo com conflito

#### 2.3.4 Funcionalidades de Edição
- Clicar em agendamento existente abre modal de edição
- Permissões:
  - Profissional pode editar apenas seus próprios agendamentos
  - Secretária pode editar qualquer agendamento da unidade
  - Coordenador pode editar qualquer agendamento da unidade
  - Admin pode editar qualquer agendamento
- Drag & Drop:
  - Arrastar agendamento para novo horário
  - Sistema valida novo horário antes de confirmar
  - Se inválido: reverte posição e exibe erro
  - Se válido: atualiza horário mantendo duração

#### 2.3.5 Status de Agendamento
- Estados possíveis:
  - **Agendado**: Criado, aguardando confirmação
  - **Confirmado**: Confirmado pelo paciente/responsável
  - **Check-in**: Paciente chegou
  - **Em andamento**: Atendimento iniciado
  - **Concluído**: Atendimento finalizado (gera evolução pendente)
  - **Cancelado**: Cancelado (com motivo opcional)
- Transições de status:
  - Qualquer status pode ser cancelado (exceto concluído)
  - Apenas profissional pode marcar como "concluído"
  - Secretária pode alterar status até "check-in"

#### 2.3.6 Conclusão de Atendimento
- Ao marcar como "concluído":
  - Sistema cria automaticamente uma evolução pendente
  - Evolução é vinculada ao atendimento
  - Profissional recebe notificação de evolução pendente
  - Badge de "evoluções pendentes" atualiza no dashboard

#### 2.3.7 Filtros e Busca
- Filtros disponíveis:
  - Unidade (admin vê todas)
  - Profissional
  - Paciente
  - Sala
  - Status
  - Período (data início/fim)
  - Categoria
- Filtros persistem entre sessões (salvos por usuário)
- Busca rápida por nome de paciente ou profissional

#### 2.3.8 Bloqueios e Indisponibilidades
- Coordenador e admin podem:
  - Bloquear horários específicos (feriados)
  - Bloquear períodos (indisponibilidade de sala/profissional)
  - Configurar horários úteis por unidade
- Bloqueios aparecem visualmente na agenda (diferentes de agendamentos)
- Sistema não permite criar agendamentos em horários bloqueados

#### 2.3.9 Cores e Personalização
- Cada profissional pode ter cor personalizada na agenda
- Cores são configuráveis por profissional
- Legenda de cores visível na interface

---

### 2.4 Pacientes

#### 2.4.1 Objetivo
- Gerenciar cadastro de pacientes
- Manter prontuário eletrônico completo
- Organizar documentos e histórico clínico

#### 2.4.2 Lista de Pacientes
- Tabela com colunas:
  - Foto/Iniciais
  - Nome completo
  - Idade
  - Tags/Diagnósticos
  - Unidade
  - Responsável
  - Última atualização
- Filtros:
  - Nome (busca textual)
  - Diagnóstico/Tag
  - Unidade
  - Status (ativo/inativo)
  - Período de cadastro
- Ações rápidas:
  - Ver perfil completo
  - Editar
  - Ver documentos
  - Ver timeline

#### 2.4.3 Cadastro de Paciente
- Campos obrigatórios:
  - Nome completo
  - Unidade
- Campos opcionais:
  - Data de nascimento
  - CPF (com validação e verificação de duplicidade)
  - Gênero
  - Telefone
  - Email
  - Endereço completo
  - Diagnósticos/Tags (múltiplos)
  - Alergias
  - Medicamentos em uso
  - Plano de crise
  - Observações gerais
  - Responsáveis/Guardiões (múltiplos):
    - Nome
    - Relacionamento
    - Telefone
    - Email
    - Observações
- Permissões:
  - Secretária e coordenador podem criar/editar
  - Profissional pode apenas visualizar
  - Admin pode criar/editar em qualquer unidade

#### 2.4.4 Perfil do Paciente
- Abas:
  - **Dados Gerais**: Informações cadastrais completas
  - **Documentos**: Upload e visualização de documentos
  - **Timeline**: Histórico completo de eventos
  - **Avaliações**: Avaliações realizadas
  - **Evoluções**: Evoluções clínicas (apenas profissionais)
- Alertas visíveis:
  - Alergias
  - Restrições
  - Observações importantes

#### 2.4.5 Documentos
- Upload de documentos:
  - Formatos permitidos: PDF, JPG, PNG
  - Tamanho máximo: 10MB por arquivo
  - Categoria (opcional): Laudo, Receita, Relatório, Outro
  - Descrição (opcional)
- Visualização:
  - Lista de documentos com preview
  - Download de documentos
  - Exclusão (apenas coordenador/admin)
- Permissões:
  - Secretária, coordenador e admin podem fazer upload
  - Todos podem visualizar (da mesma unidade)
  - Log de downloads em auditoria

#### 2.4.6 Timeline
- Linha do tempo vertical com todos os eventos do paciente:
  - Evoluções (criação, finalização, revisão)
  - Avaliações (criação, finalização)
  - Documentos (upload)
  - Notificações relacionadas
  - Observações internas
- Ordenação: mais recente primeiro
- Filtros por tipo de evento
- Cada evento mostra:
  - Data e hora
  - Autor
  - Tipo (ícone)
  - Resumo/Preview
  - Link para item completo (quando aplicável)

---

### 2.5 Evoluções

#### 2.5.1 Objetivo
- Registrar evolução clínica após atendimento
- Permitir revisão pelo coordenador
- Manter histórico clínico completo

#### 2.5.2 Criação Automática
- Evolução é criada automaticamente quando:
  - Atendimento é marcado como "concluído"
- Evolução criada com:
  - Status: "pendente"
  - Vinculada ao atendimento
  - Vinculada ao paciente
  - Vinculada ao profissional do atendimento
  - Vinculada à unidade

#### 2.5.3 Lista de Evoluções
- Abas:
  - **Pendentes**: Evoluções aguardando preenchimento
  - **Minhas**: Evoluções do profissional logado
  - **Unidade**: Todas as evoluções da unidade (coordenador/admin)
  - **Finalizadas**: Evoluções já finalizadas
  - **Revisadas**: Evoluções revisadas pelo coordenador
- Colunas:
  - Paciente
  - Profissional
  - Atendimento (data/hora)
  - Status
  - Data de criação
  - Data de finalização
  - Data de revisão
- Filtros:
  - Período
  - Profissional
  - Paciente
  - Status
  - Unidade (admin)

#### 2.5.4 Editor de Evolução
- Campos editáveis:
  - **Relato Clínico**: Texto rico (formatação básica)
  - **Conduta**: Texto rico
  - **Objetivos**: Texto rico
  - **Observações Internas**: Texto rico (visível apenas para coordenador/admin)
- Funcionalidades:
  - **Autosave**: Salva automaticamente a cada 30 segundos (se houver mudanças)
  - Indicador visual: "Salvando..." → "Salvo às HH:MM"
  - Salvar manualmente (Ctrl+S ou botão)
  - Ao fechar: pergunta se deseja salvar rascunho
- Validações:
  - Campos obrigatórios (configuráveis por unidade)
  - Sanitização de HTML (remover scripts, manter formatação)

#### 2.5.5 Finalização
- Botão "Finalizar Evolução"
- Modal de confirmação:
  - "Ao finalizar, não será possível editar. Deseja continuar?"
  - Campo de assinatura textual: "Confirmo que li e concordo"
- Validações:
  - Todos os campos obrigatórios preenchidos
  - Assinatura confirmatória fornecida
- Após finalização:
  - Status muda para "finalizada"
  - Data de finalização registrada
  - Metadados de assinatura salvos (timestamp, IP)
  - Evento adicionado à timeline do paciente
  - Coordenador recebe notificação
  - Campos ficam somente leitura

#### 2.5.6 Revisão (Coordenador/Admin)
- Coordenador pode revisar evoluções da unidade
- Admin pode revisar qualquer evolução
- Funcionalidades:
  - Visualizar evolução completa
  - Adicionar comentário interno (não visível ao profissional)
  - Marcar como "revisada"
- Após revisão:
  - Data de revisão registrada
  - Revisor registrado
  - Status permanece "finalizada" (não muda)
  - Profissional recebe notificação
  - Evento adicionado à timeline

#### 2.5.7 Addendums (Observações Adicionais)
- Permite adicionar observações posteriores à evolução finalizada
- Apenas coordenador/admin pode adicionar
- Addendum registra:
  - Autor
  - Data/hora
  - Conteúdo
- Addendums aparecem na timeline do paciente

#### 2.5.8 Permissões
- **Profissional**: Pode criar/editar/finalizar apenas suas próprias evoluções
- **Coordenador**: Pode ver/editar/revisar todas as evoluções da unidade
- **Admin**: Pode ver/editar/revisar todas as evoluções
- **Secretária**: Não tem acesso a evoluções

---

### 2.6 Avaliações

#### 2.6.1 Objetivo
- Aplicar formulários estruturados aos pacientes
- Permitir templates customizáveis
- Manter histórico de avaliações

#### 2.6.2 Templates de Avaliação
- Templates são criados apenas por administradores
- Campos do template:
  - Nome (ex.: "Anamnese Inicial", "Avaliação Psicológica")
  - Categoria
  - Versão
  - Campos dinâmicos (JSON schema):
    - Texto
    - Número
    - Data
    - Seleção (dropdown)
    - Checkbox
    - Upload de arquivo
  - Cada campo pode ser:
    - Obrigatório ou opcional
    - Ter validação específica
    - Ter placeholder/ajuda
- Versionamento:
  - Templates podem ter versões
  - Avaliações antigas mantêm versão original
  - Novas avaliações usam versão ativa
- Escopo:
  - Template global (todas as unidades)
  - Template por unidade

#### 2.6.3 Criação de Avaliação
- Profissional/Coordenador/Admin pode criar avaliação
- Passos:
  1. Selecionar template
  2. Selecionar paciente
  3. Preencher campos do formulário
- Funcionalidades:
  - Autosave a cada 30 segundos
  - Validação em tempo real (campos obrigatórios)
  - Preview antes de finalizar

#### 2.6.4 Finalização
- Botão "Finalizar Avaliação"
- Modal de confirmação com assinatura textual
- Validações:
  - Todos os campos obrigatórios preenchidos
  - Respostas válidas conforme tipo de campo
- Após finalização:
  - Status muda para "finalizada"
  - Data de finalização registrada
  - Metadados de assinatura salvos
  - Evento adicionado à timeline do paciente
  - Coordenador recebe notificação

#### 2.6.5 Revisão
- Coordenador/Admin pode revisar avaliações
- Funcionalidades similares à revisão de evoluções
- Comentários internos possíveis

#### 2.6.6 Lista de Avaliações
- Filtros:
  - Template
  - Paciente
  - Profissional
  - Status
  - Período
  - Unidade (admin)
- Visualização:
  - Lista com preview das respostas
  - Link para visualização completa

---

### 2.7 Relatórios

#### 2.7.1 Objetivo
- Gerar análises e estatísticas
- Apoiar tomada de decisão
- Visualizar dados agregados

#### 2.7.2 Tipos de Relatórios

**Produtividade:**
- Atendimentos por profissional
- Média de atendimentos por dia
- Taxa de conclusão
- Período configurável
- Gráficos: barras, linhas
- Filtros: unidade, profissional, período

**Frequência:**
- Presenças vs faltas por paciente
- Taxa de frequência
- Gráficos: pizza, barras
- Filtros: paciente, período, unidade

**Clínico:**
- Evoluções finalizadas no período
- Avaliações finalizadas no período
- Gráficos: linhas temporais
- Filtros: profissional, paciente, período, unidade

**Unidade:**
- Visão geral da unidade
- Volume total de atendimentos
- Pacientes ativos
- Profissionais ativos
- Gráficos: múltiplos tipos
- Filtros: período

#### 2.7.3 Funcionalidades
- Filtros avançados (persistem entre sessões)
- Salvar filtros como favoritos
- Visualização com gráficos interativos
- Tabela detalhada com dados
- Exportação (quando implementada):
  - PDF
  - CSV
- Permissões:
  - Coordenador: relatórios da unidade
  - Admin: relatórios de todas as unidades
  - Profissional e Secretária: sem acesso

---

### 2.8 Notificações

#### 2.8.1 Objetivo
- Comunicar informações importantes
- Alertar sobre pendências
- Manter equipe informada

#### 2.8.2 Tipos de Notificação
- **Aviso**: Informação geral
- **Lembrete**: Lembretes importantes
- **Alerta Clínico**: Alertas relacionados a pacientes
- **Sistema**: Notificações automáticas do sistema

#### 2.8.3 Criação de Notificação
- Admin e Coordenador podem criar
- Campos:
  - Título
  - Corpo (texto rico)
  - Tipo
  - Unidade (ou todas)
  - Data de publicação (pode agendar)
  - Data de expiração (opcional)
- Após criação:
  - Se publicada imediatamente: notifica todos os destinatários
  - Se agendada: publica na data programada

#### 2.8.4 Feed de Notificações
- Lista de notificações:
  - Ordenação: mais recente primeiro
  - Badge "nova" se não lida
  - Tipo visível (ícone/cor)
  - Data/hora
- Filtros:
  - Tipo
  - Lida/Não lida
  - Período
  - Unidade
- Marcar como lida:
  - Ao clicar na notificação
  - Botão "Marcar todas como lidas"
- Contador de não lidas visível no ícone da sidebar

#### 2.8.5 Notificações Automáticas
- Sistema cria notificações automaticamente para:
  - Evolução pendente criada
  - Evolução finalizada (notifica coordenador)
  - Evolução revisada (notifica profissional)
  - Avaliação finalizada (notifica coordenador)
  - Novo agendamento (opcional, configurável)
  - Atendimento cancelado (opcional)

---

### 2.9 Configurações

#### 2.9.1 Perfil Pessoal
- Campos editáveis:
  - Nome
  - Email
  - Telefone
  - Foto (upload)
- Alteração de senha:
  - Senha atual
  - Nova senha
  - Confirmação de senha
  - Validação de força

#### 2.9.2 Preferências Pessoais
- Tema: Claro/Escuro/Automático
- Unidade padrão (se múltiplas)
- Visualização padrão da agenda: Dia/Semana/Mês/Lista
- Cor do profissional (para agenda)
- Filtros salvos (favoritos)

#### 2.9.3 Configurações de Agenda (Coordenador/Admin)
- Duração padrão de atendimento
- Horários úteis (início/fim do dia)
- Bloqueios e feriados
- Configurações de disponibilidade

#### 2.9.4 Gestão de Unidades (Admin)
- Lista de unidades
- Criar/Editar/Desativar unidade
- Campos:
  - Nome
  - Endereço
  - Telefone
  - Email
  - Fuso horário
  - Configurações (JSON)
- Gestão de salas:
  - Criar/Editar/Desativar sala
  - Nome, capacidade, cor

#### 2.9.5 Gestão de Usuários (Admin)
- Lista de usuários
- Criar/Editar/Desativar usuário
- Campos:
  - Nome
  - Email
  - Papel (role)
  - Unidade primária
  - Unidades adicionais (múltiplas)
  - Status (ativo/inativo)
  - Cor do profissional
- Ações:
  - Resetar senha
  - Impersonar (logar como outro usuário, para suporte)
  - Enviar email de boas-vindas

#### 2.9.6 Configurações do Sistema (Admin)
- Branding:
  - Nome do sistema
  - Logo
  - Cores primárias e secundárias
  - Mensagens personalizadas
  - Rodapé
- Status do sistema:
  - Online
  - Manutenção (com mensagem)
  - Offline
- Outras configurações:
  - Duração padrão de sessão
  - Políticas de senha
  - Retenção de dados
  - Configurações de backup

#### 2.9.7 Backups (Admin)
- Lista de backups:
  - Data/hora de criação
  - Tamanho
  - Status
  - Criado por
- Ações:
  - Executar backup manual
  - Baixar backup
  - Restaurar backup (com confirmação dupla)
- Backups automáticos:
  - Configurar frequência (diário, semanal)
  - Retenção (quantos backups manter)

---

### 2.10 Logs e Auditoria (Admin)

#### 2.10.1 Objetivo
- Rastrear todas as ações do sistema
- Manter trilha de auditoria
- Suportar compliance e segurança

#### 2.10.2 Logs de Ações
- Registro de todas as ações:
  - Criar/Editar/Excluir entidades
  - Login/Logout
  - Alterações de configuração
  - Acessos a dados sensíveis
- Campos registrados:
  - Data/hora
  - Usuário
  - Ação (create/update/delete/view)
  - Tipo de entidade (Patient, Appointment, etc.)
  - ID da entidade
  - Descrição
  - Dados alterados (payload JSON)
  - IP do usuário
  - User Agent
- Filtros:
  - Data (período)
  - Usuário
  - Ação
  - Tipo de entidade
  - IP
- Visualização:
  - Tabela com paginação
  - Detalhes expandíveis
  - Exportação CSV

#### 2.10.3 Logs de Login
- Registro de tentativas de login:
  - Sucesso/Falha
  - Data/hora
  - Usuário
  - IP
  - User Agent
- Útil para segurança e detecção de acessos não autorizados

---

## 3. Papéis (Roles) e Permissões

### 3.1 Administrador
- **Acesso Total**: Pode acessar todas as funcionalidades, sem restrições
- **Escopo**: Todas as unidades
- **Funcionalidades Especiais**:
  - Painel administrativo completo
  - Gestão de usuários
  - Gestão de unidades
  - Configurações do sistema
  - Backups
  - Logs de auditoria
  - Impersonação de usuários
  - Modo manutenção
- **Bypass de Validações**: Pode forçar ações normalmente bloqueadas

### 3.2 Coordenador
- **Escopo**: Sua unidade (ou unidades atribuídas)
- **Funcionalidades**:
  - Dashboard da unidade
  - Agenda completa da unidade
  - Gestão de pacientes da unidade
  - Revisão de evoluções da unidade
  - Revisão de avaliações da unidade
  - Relatórios da unidade
  - Configurações da agenda da unidade
  - Criar notificações para a unidade
- **Restrições**:
  - Não acessa painel admin
  - Não cria/edita usuários
  - Não altera configurações globais

### 3.3 Profissional
- **Escopo**: Sua unidade, seus próprios registros
- **Funcionalidades**:
  - Dashboard pessoal
  - Agenda pessoal (seus agendamentos)
  - Criar agendamentos para si mesmo
  - Ver pacientes da unidade
  - Criar/editar/finalizar suas próprias evoluções
  - Criar/editar/finalizar suas próprias avaliações
  - Ver prontuário de pacientes atendidos
- **Restrições**:
  - Não cria/edita pacientes
  - Não vê evoluções de outros profissionais
  - Não revisa evoluções
  - Não acessa relatórios
  - Não configura agenda

### 3.4 Secretária
- **Escopo**: Sua unidade
- **Funcionalidades**:
  - Dashboard da agenda
  - Agenda completa da unidade (criar/editar qualquer agendamento)
  - Criar/editar pacientes
  - Upload de documentos
  - Ver prontuário completo
  - Alterar status de agendamentos
- **Restrições**:
  - Não acessa evoluções
  - Não acessa avaliações
  - Não acessa relatórios
  - Não configura agenda
  - Não cria notificações

### 3.5 Matriz de Permissões Resumida

| Funcionalidade | Admin | Coordenador | Profissional | Secretária |
|----------------|-------|-------------|--------------|------------|
| Dashboard Global | ✅ Todas | ✅ Unidade | ✅ Pessoal | ✅ Unidade |
| Agenda - Ver | ✅ Todas | ✅ Unidade | ✅ Próprios | ✅ Unidade |
| Agenda - Criar/Editar | ✅ Todas | ✅ Unidade | ✅ Próprios | ✅ Unidade |
| Pacientes - Ver | ✅ Todas | ✅ Unidade | ✅ Unidade | ✅ Unidade |
| Pacientes - Criar/Editar | ✅ Todas | ✅ Unidade | ❌ | ✅ Unidade |
| Evoluções - Ver | ✅ Todas | ✅ Unidade | ✅ Próprias | ❌ |
| Evoluções - Criar/Editar | ✅ Todas | ✅ Unidade | ✅ Próprias | ❌ |
| Evoluções - Revisar | ✅ Todas | ✅ Unidade | ❌ | ❌ |
| Avaliações - Ver | ✅ Todas | ✅ Unidade | ✅ Próprias | ❌ |
| Avaliações - Criar/Editar | ✅ Todas | ✅ Unidade | ✅ Próprias | ❌ |
| Avaliações - Revisar | ✅ Todas | ✅ Unidade | ❌ | ❌ |
| Relatórios | ✅ Todas | ✅ Unidade | ❌ | ❌ |
| Painel Admin | ✅ | ❌ | ❌ | ❌ |
| Configurar Unidade | ✅ Todas | ✅ Própria | ❌ | ❌ |
| Criar Notificações | ✅ Todas | ✅ Unidade | ❌ | ❌ |
| Backups | ✅ | ❌ | ❌ | ❌ |
| Logs de Auditoria | ✅ | ❌ | ❌ | ❌ |

---

## 4. Regras de Negócio e Lógica

### 4.1 Escopo de Unidade
- Todos os dados são filtrados por unidade (exceto admin)
- Usuário seleciona unidade ativa na sessão
- Dados de outras unidades não são visíveis (exceto admin)
- Admin pode ver e gerenciar todas as unidades

### 4.2 Criação Automática de Evolução
- Quando atendimento é marcado como "concluído":
  - Sistema cria automaticamente evolução com status "pendente"
  - Evolução vinculada ao atendimento, paciente, profissional e unidade
  - Profissional recebe notificação
  - Badge de pendências atualiza

### 4.3 Validação de Conflitos
- Ao criar/editar agendamento:
  - Verificar se profissional já tem agendamento no mesmo horário
  - Verificar se sala já está ocupada (se sala especificada)
  - Verificar bloqueios (feriados, indisponibilidade)
  - Se conflito: sugerir horários alternativos
  - Admin e coordenador podem forçar criação

### 4.4 Versionamento de Templates
- Templates de avaliação podem ter versões
- Avaliações antigas mantêm versão original
- Novas avaliações usam versão ativa
- Histórico de versões mantido

### 4.5 Timeline Automática
- Sistema cria eventos na timeline automaticamente:
  - Evolução criada
  - Evolução finalizada
  - Evolução revisada
  - Avaliação criada
  - Avaliação finalizada
  - Documento uploadado
  - Notificação relacionada
- Timeline ordenada por data/hora (mais recente primeiro)

### 4.6 Autosave
- Evoluções e avaliações têm autosave automático:
  - Salva a cada 30 segundos (se houver mudanças)
  - Não altera status (mantém como rascunho)
  - Indicador visual de status de salvamento
- Salvamento manual também disponível

### 4.7 Finalização e Assinatura
- Evoluções e avaliações exigem confirmação para finalizar:
  - Modal de confirmação
  - Campo de assinatura textual obrigatório
  - Após finalização: campos ficam somente leitura
  - Metadados de assinatura salvos (timestamp, IP)

### 4.8 Revisão
- Coordenador/Admin pode revisar evoluções/avaliações:
  - Adicionar comentário interno
  - Marcar como revisada
  - Profissional recebe notificação
  - Status permanece "finalizada" (não muda)

### 4.9 Bloqueios e Feriados
- Coordenador/Admin pode configurar:
  - Feriados (datas específicas)
  - Bloqueios de período (indisponibilidade)
  - Horários úteis por unidade
- Bloqueios impedem criação de agendamentos
- Visualização diferenciada na agenda

### 4.10 Validações de Dados
- CPF: Validação de formato e verificação de duplicidade
- Email: Validação de formato
- Datas: Não permitir datas futuras em alguns contextos (exceto admin)
- Senha: Mínimo 8 caracteres, maiúscula, número, caractere especial
- Arquivos: Tipos e tamanhos permitidos validados

### 4.11 Notificações Automáticas
- Sistema cria notificações automaticamente para eventos:
  - Evolução pendente criada → notifica profissional
  - Evolução finalizada → notifica coordenador
  - Evolução revisada → notifica profissional
  - Avaliação finalizada → notifica coordenador
  - Agendamento criado (opcional, configurável)
  - Atendimento cancelado (opcional)

### 4.12 Auditoria Automática
- Todas as ações críticas são registradas:
  - Criação/edição/exclusão de entidades
  - Acessos a dados sensíveis
  - Alterações de configuração
  - Logins e logouts
- Logs são imutáveis (não podem ser editados ou deletados)
- Logs incluem: usuário, data/hora, ação, entidade, IP, User Agent

### 4.13 Retenção de Dados
- Logs de auditoria: 12 meses (configurável)
- Logs de login: 12 meses (configurável)
- Backups: Retenção configurável (ex.: 14 dias)
- Exports de relatórios: Expiração configurável

### 4.14 Modo Manutenção
- Admin pode ativar modo manutenção
- Em manutenção:
  - Apenas administradores podem acessar
  - Mensagem personalizada exibida na tela de login
  - Usuários logados são notificados antes do bloqueio
- Útil para atualizações e manutenções programadas

---

## 5. Entidades e Dados

### 5.1 Usuários
- **Campos principais**:
  - Identificador único
  - Nome completo
  - Email (único)
  - Senha (criptografada)
  - Papel (role)
  - Unidade primária
  - Status (ativo/inativo)
  - Cor do profissional (para agenda)
  - Foto (opcional)
- **Relacionamentos**:
  - Múltiplas unidades (via tabela de relacionamento)
  - Preferências pessoais
  - Agendamentos (como profissional)
  - Evoluções (como profissional)
  - Avaliações (como profissional)

### 5.2 Unidades
- **Campos principais**:
  - Identificador único
  - Nome
  - Endereço
  - Telefone
  - Email
  - Fuso horário
  - Configurações (JSON)
  - Status (ativa/inativa)
- **Relacionamentos**:
  - Usuários
  - Salas
  - Pacientes
  - Agendamentos
  - Evoluções
  - Avaliações
  - Notificações

### 5.3 Salas
- **Campos principais**:
  - Identificador único
  - Unidade (vinculada)
  - Nome
  - Capacidade
  - Cor (para visualização)
  - Status (ativa/inativa)
- **Relacionamentos**:
  - Agendamentos

### 5.4 Pacientes
- **Campos principais**:
  - Identificador único
  - Unidade (vinculada)
  - Nome completo
  - Data de nascimento
  - CPF (opcional, único)
  - Gênero
  - Telefone
  - Email
  - Endereço completo
  - Diagnósticos/Tags (array)
  - Alergias
  - Medicamentos em uso
  - Plano de crise
  - Observações gerais
  - Status (ativo/inativo)
  - Data de cadastro
- **Relacionamentos**:
  - Agendamentos
  - Evoluções
  - Avaliações
  - Documentos
  - Timeline
  - Responsáveis/Guardiões

### 5.5 Agendamentos
- **Campos principais**:
  - Identificador único
  - Unidade (vinculada)
  - Sala (opcional)
  - Profissional (vinculado)
  - Paciente (vinculado)
  - Data/hora início
  - Data/hora fim
  - Status (agendado/confirmado/check-in/em_andamento/concluído/cancelado)
  - Categoria (opcional)
  - Observações
  - Criado por
  - Cancelado por (se cancelado)
  - Motivo do cancelamento (se cancelado)
  - Data de criação
- **Relacionamentos**:
  - Evolução (1:1, se concluído)

### 5.6 Evoluções
- **Campos principais**:
  - Identificador único
  - Atendimento (vinculado, opcional)
  - Paciente (vinculado)
  - Profissional (vinculado)
  - Unidade (vinculada)
  - Status (pendente/rascunho/finalizada/revisada/arquivada)
  - Conteúdo (JSON):
    - Relato clínico
    - Conduta
    - Objetivos
    - Observações internas
  - Metadados de assinatura (JSON):
    - Timestamp
    - IP
    - Texto de confirmação
  - Data de finalização
  - Finalizado por
  - Data de revisão
  - Revisado por
  - Comentário de revisão
  - Data de criação
  - Data de atualização
- **Relacionamentos**:
  - Addendums (observações adicionais)

### 5.7 Templates de Avaliação
- **Campos principais**:
  - Identificador único
  - Unidade (vinculada, opcional para global)
  - Nome
  - Categoria
  - Versão
  - Campos (JSON schema)
  - Status (ativo/inativo)
  - Criado por
  - Data de criação
- **Relacionamentos**:
  - Avaliações

### 5.8 Avaliações
- **Campos principais**:
  - Identificador único
  - Template (vinculado)
  - Paciente (vinculado)
  - Profissional (vinculado)
  - Unidade (vinculada)
  - Status (em_preenchimento/finalizada/revisada)
  - Respostas (JSON)
  - Metadados de assinatura (JSON)
  - Data de finalização
  - Finalizado por
  - Data de revisão
  - Revisado por
  - Comentário de revisão
  - Data de criação
  - Data de atualização
- **Relacionamentos**:
  - Template (com versão específica)

### 5.9 Documentos
- **Campos principais**:
  - Identificador único
  - Paciente (vinculado)
  - Unidade (vinculada)
  - Categoria (laudo/receita/relatório/outro)
  - Nome do arquivo
  - Caminho do arquivo
  - Tamanho
  - Descrição (opcional)
  - Enviado por
  - Data de upload
- **Relacionamentos**:
  - Paciente

### 5.10 Timeline
- **Campos principais**:
  - Identificador único
  - Paciente (vinculado)
  - Unidade (vinculada)
  - Tipo (evolução/avaliação/documento/notificação/observação)
  - Referência (ID da entidade relacionada)
  - Título
  - Metadados (JSON)
  - Data/hora do evento
  - Data de criação
- **Relacionamentos**:
  - Paciente

### 5.11 Notificações
- **Campos principais**:
  - Identificador único
  - Unidade (vinculada, opcional para global)
  - Título
  - Corpo (texto rico)
  - Tipo (aviso/lembrete/alerta_clinico/sistema)
  - Data de publicação
  - Data de expiração (opcional)
  - Criado por
  - Data de criação
- **Relacionamentos**:
  - Usuários (via tabela de relacionamento para marcar como lida)

### 5.12 Logs de Auditoria
- **Campos principais**:
  - Identificador único
  - Usuário (vinculado)
  - Unidade (vinculada, opcional)
  - Ação (create/update/delete/view)
  - Tipo de entidade
  - ID da entidade
  - Descrição
  - Dados alterados (JSON)
  - IP do usuário
  - User Agent
  - Data/hora

### 5.13 Logs de Login
- **Campos principais**:
  - Identificador único
  - Usuário (vinculado)
  - Resultado (sucesso/falha)
  - IP do usuário
  - User Agent
  - Data/hora

### 5.14 Backups
- **Campos principais**:
  - Identificador único
  - Nome do arquivo
  - Caminho do arquivo
  - Tamanho
  - Status (em_andamento/concluído/erro)
  - Criado por
  - Data de criação
  - Checksum (opcional)

### 5.15 Configurações do Sistema
- **Campos principais**:
  - Chave (única)
  - Valor (JSON)
- **Exemplos de chaves**:
  - `system_name`: Nome do sistema
  - `logo_url`: URL do logo
  - `primary_color`: Cor primária
  - `secondary_color`: Cor secundária
  - `status`: Status do sistema (online/manutenção/offline)
  - `status_message`: Mensagem de status
  - `footer_text`: Texto do rodapé

### 5.16 Preferências do Usuário
- **Campos principais**:
  - Identificador único
  - Usuário (vinculado)
  - Tema (claro/escuro/automático)
  - Visualização padrão da agenda (dia/semana/mês/lista)
  - Unidade padrão
  - Duração padrão da agenda
  - Cor do profissional
  - Filtros salvos (JSON)

---

## 6. Fluxos Principais

### 6.1 Fluxo de Atendimento Completo
1. **Agendamento**: Secretária ou profissional cria agendamento
2. **Confirmação**: Status muda para "confirmado" (opcional)
3. **Check-in**: Secretária marca paciente como presente
4. **Atendimento**: Profissional realiza atendimento
5. **Conclusão**: Profissional marca como "concluído"
6. **Evolução Automática**: Sistema cria evolução pendente
7. **Preenchimento**: Profissional preenche evolução
8. **Finalização**: Profissional finaliza evolução
9. **Revisão**: Coordenador revisa evolução
10. **Timeline**: Eventos adicionados à timeline do paciente

### 6.2 Fluxo de Avaliação
1. **Criação de Template**: Admin cria template de avaliação
2. **Seleção**: Profissional seleciona template e paciente
3. **Preenchimento**: Profissional preenche formulário
4. **Autosave**: Sistema salva automaticamente
5. **Finalização**: Profissional finaliza avaliação
6. **Revisão**: Coordenador revisa (opcional)
7. **Timeline**: Evento adicionado à timeline

### 6.3 Fluxo de Cadastro de Paciente
1. **Criação**: Secretária cria cadastro mínimo
2. **Completar Dados**: Secretária ou coordenador completa informações
3. **Documentos**: Upload de documentos relacionados
4. **Timeline**: Eventos começam a ser registrados

### 6.4 Fluxo de Revisão
1. **Notificação**: Coordenador recebe notificação de evolução/avaliação finalizada
2. **Visualização**: Coordenador acessa item para revisar
3. **Comentário**: Coordenador adiciona comentário interno (opcional)
4. **Marcar como Revisada**: Coordenador marca como revisada
5. **Notificação**: Profissional recebe notificação de revisão
6. **Timeline**: Evento adicionado à timeline

---

## 7. Requisitos Não Funcionais

### 7.1 Performance
- Dashboard deve carregar em menos de 2 segundos
- Agenda deve suportar 200+ agendamentos por dia sem degradação
- Busca de pacientes deve retornar resultados em menos de 1 segundo
- Relatórios devem gerar em menos de 5 segundos

### 7.2 Segurança
- Senhas devem ser criptografadas (hash seguro)
- Sessões devem expirar após inatividade
- Dados sensíveis devem ser protegidos por permissões
- Logs de acesso devem ser mantidos
- Comunicação deve usar HTTPS

### 7.3 Usabilidade
- Interface deve ser intuitiva e fácil de usar
- Feedback visual para todas as ações
- Mensagens de erro claras e acionáveis
- Suporte a teclado (atalhos)
- Responsivo (funciona em mobile/tablet/desktop)

### 7.4 Confiabilidade
- Sistema deve estar disponível 99% do tempo
- Backups automáticos regulares
- Recuperação de dados em caso de falha
- Logs de auditoria imutáveis

### 7.5 Escalabilidade
- Sistema deve suportar múltiplas unidades
- Suportar crescimento de usuários e dados
- Performance não degrada com aumento de volume

### 7.6 Manutenibilidade
- Código deve ser documentado
- Estrutura modular
- Fácil adicionar novas funcionalidades
- Logs detalhados para debugging

---

## 8. Integrações e Extensibilidade

### 8.1 Email
- Envio de emails para:
  - Recuperação de senha
  - Notificações importantes
  - Boas-vindas
  - Alertas do sistema

### 8.2 Armazenamento de Arquivos
- Upload de documentos
- Armazenamento seguro
- Organização por unidade e paciente
- Controle de acesso

### 8.3 Exportação de Dados
- Exportação de relatórios (PDF, CSV)
- Exportação de dados para backup
- Formato estruturado e legível

### 8.4 Extensibilidade Futura
- Sistema deve permitir adicionar novos módulos
- APIs para integrações externas (quando necessário)
- Plugins ou extensões (se aplicável)

---

## 9. Considerações de Compliance

### 9.1 LGPD (Lei Geral de Proteção de Dados)
- Consentimento para uso de dados
- Direito ao esquecimento (exclusão de dados)
- Portabilidade de dados
- Acesso aos dados pessoais
- Logs de consentimento

### 9.2 Auditoria
- Trilha completa de auditoria
- Logs imutáveis
- Rastreabilidade de ações
- Relatórios de compliance

### 9.3 Privacidade
- Dados sensíveis protegidos
- Acesso restrito por permissões
- Criptografia de dados sensíveis (quando necessário)
- Política de retenção de dados

---

## 10. Glossário de Termos Técnicos Funcionais

- **Autosave**: Salvamento automático de dados sem intervenção do usuário
- **Badge**: Indicador visual de quantidade (ex.: notificações não lidas)
- **Drag & Drop**: Arrastar e soltar elementos na interface
- **Modal**: Janela sobreposta para ações específicas
- **Timeline**: Linha do tempo visual com eventos ordenados
- **Template**: Modelo reutilizável para criação de formulários
- **Versionamento**: Manutenção de versões históricas de templates
- **Escopo**: Limite de visibilidade de dados (por unidade, por usuário)
- **Bypass**: Ignorar validações normalmente aplicadas
- **Impersonação**: Acesso ao sistema como outro usuário (para suporte)
- **Polling**: Verificação periódica de atualizações
- **Real-time**: Atualizações instantâneas sem necessidade de recarregar

---

## 11. Checklist de Funcionalidades Essenciais

### 11.1 Autenticação
- [ ] Login com email e senha
- [ ] Recuperação de senha
- [ ] Troca de unidade
- [ ] Gerenciamento de sessão
- [ ] Modo manutenção

### 11.2 Dashboard
- [ ] KPIs por papel
- [ ] Gráficos interativos
- [ ] Filtros persistentes
- [ ] Atalhos rápidos
- [ ] Atualização automática

### 11.3 Agenda
- [ ] Visualizações (dia/semana/mês/lista)
- [ ] Criação de agendamentos
- [ ] Edição de agendamentos
- [ ] Drag & drop
- [ ] Validação de conflitos
- [ ] Bloqueios e feriados
- [ ] Conclusão de atendimento
- [ ] Criação automática de evolução

### 11.4 Pacientes
- [ ] Lista de pacientes
- [ ] Cadastro completo
- [ ] Perfil com abas
- [ ] Upload de documentos
- [ ] Timeline automática
- [ ] Filtros e busca

### 11.5 Evoluções
- [ ] Criação automática
- [ ] Editor com autosave
- [ ] Finalização com assinatura
- [ ] Revisão pelo coordenador
- [ ] Addendums
- [ ] Filtros e listagem

### 11.6 Avaliações
- [ ] Templates customizáveis
- [ ] Versionamento
- [ ] Preenchimento dinâmico
- [ ] Autosave
- [ ] Finalização
- [ ] Revisão

### 11.7 Relatórios
- [ ] Tipos de relatórios (produtividade, frequência, clínico, unidade)
- [ ] Filtros avançados
- [ ] Gráficos interativos
- [ ] Exportação (quando implementada)
- [ ] Filtros salvos

### 11.8 Notificações
- [ ] Criação manual
- [ ] Notificações automáticas
- [ ] Feed de notificações
- [ ] Marcação como lida
- [ ] Contador de não lidas

### 11.9 Configurações
- [ ] Perfil pessoal
- [ ] Preferências
- [ ] Gestão de unidades
- [ ] Gestão de usuários
- [ ] Branding do sistema
- [ ] Backups

### 11.10 Auditoria
- [ ] Logs de ações
- [ ] Logs de login
- [ ] Filtros e busca
- [ ] Exportação

---

## 12. Mensagens e Feedback do Sistema

### 12.1 Mensagens de Sucesso
- **Agendamento criado**: "Agendamento criado com sucesso"
- **Agendamento atualizado**: "Agendamento atualizado com sucesso"
- **Agendamento cancelado**: "Agendamento cancelado com sucesso"
- **Evolução salva**: "Evolução salva automaticamente às {hora}"
- **Evolução finalizada**: "Evolução finalizada com sucesso"
- **Evolução revisada**: "Evolução marcada como revisada"
- **Avaliação finalizada**: "Avaliação finalizada com sucesso"
- **Paciente criado**: "Paciente cadastrado com sucesso"
- **Documento enviado**: "Documento enviado com sucesso"
- **Notificação criada**: "Notificação criada e enviada"
- **Backup executado**: "Backup criado com sucesso. Download disponível por 15 minutos"
- **Senha alterada**: "Senha alterada com sucesso. Faça login novamente"
- **Configurações salvas**: "Configurações salvas com sucesso"

### 12.2 Mensagens de Erro
- **Credenciais inválidas**: "Email ou senha incorretos"
- **Conta bloqueada**: "Conta bloqueada. Contate o administrador"
- **Sistema em manutenção**: "Sistema em manutenção. Apenas administradores podem acessar"
- **Token expirado**: "Link de recuperação expirado. Solicite um novo"
- **Conflito de horário**: "Horário já ocupado. Sugestões: {horários alternativos}"
- **Data no passado**: "Não é possível agendar no passado"
- **Horário bloqueado**: "Horário bloqueado (feriado/indisponibilidade)"
- **Sem permissão**: "Você não tem permissão para esta ação"
- **Campos obrigatórios**: "Preencha todos os campos obrigatórios"
- **Arquivo inválido**: "Tipo de arquivo não permitido. Use: PDF, JPG ou PNG"
- **Arquivo muito grande**: "Arquivo muito grande. Tamanho máximo: 10MB"
- **CPF duplicado**: "CPF já cadastrado no sistema"
- **Email duplicado**: "Email já cadastrado no sistema"
- **Erro de conexão**: "Erro de conexão. Tentando novamente..."
- **Erro interno**: "Erro interno. Contate o suporte se o problema persistir"
- **Evolução já finalizada**: "Evolução já foi finalizada e não pode ser editada"
- **Backup em andamento**: "Já existe um backup em andamento. Aguarde a conclusão"

### 12.3 Mensagens de Validação
- **Email inválido**: "Informe um email válido"
- **Senha fraca**: "Senha deve ter no mínimo 8 caracteres, incluindo maiúscula, número e caractere especial"
- **Data inválida**: "Informe uma data válida"
- **Hora inválida**: "Hora fim deve ser posterior à hora início"
- **CPF inválido**: "CPF inválido"
- **Telefone inválido**: "Telefone inválido"
- **Campo obrigatório**: "{campo} é obrigatório"
- **Valor muito longo**: "{campo} excede o limite de {limite} caracteres"

### 12.4 Mensagens de Confirmação
- **Cancelar agendamento**: "Tem certeza que deseja cancelar este agendamento?"
- **Excluir paciente**: "Tem certeza que deseja excluir este paciente? Esta ação não pode ser desfeita"
- **Finalizar evolução**: "Ao finalizar, não será possível editar. Deseja continuar?"
- **Finalizar avaliação**: "Ao finalizar, não será possível editar. Deseja continuar?"
- **Restaurar backup**: "Restaurar backup irá substituir todos os dados atuais. Esta ação não pode ser desfeita. Tem certeza?"
- **Ativar modo manutenção**: "Ao ativar o modo manutenção, apenas administradores poderão acessar o sistema. Deseja continuar?"
- **Excluir documento**: "Tem certeza que deseja excluir este documento?"

### 12.5 Mensagens Informativas
- **Autosave ativo**: "Salvando automaticamente..."
- **Autosave concluído**: "Salvo às {hora}"
- **Sessão expirando**: "Sua sessão expirará em {minutos} minutos. Deseja continuar?"
- **Sessão expirada**: "Sua sessão expirou. Faça login novamente"
- **Sem resultados**: "Nenhum resultado encontrado"
- **Carregando**: "Carregando..."
- **Processando**: "Processando..."
- **Sem pendências**: "Não há pendências no momento"

---

## 13. Limites e Constraints

### 13.1 Limites de Dados
- **Tamanho de arquivo**: Máximo 10MB por documento
- **Tipos de arquivo permitidos**: PDF, JPG, PNG
- **Tamanho de texto**: 
  - Evolução: Sem limite prático (recomendado até 10.000 caracteres)
  - Observações: Até 1.000 caracteres
  - Notas de agendamento: Até 500 caracteres
- **Quantidade de tags/diagnósticos**: Até 10 por paciente
- **Quantidade de responsáveis**: Até 5 por paciente
- **Quantidade de campos em template**: Até 50 campos
- **Tamanho de nome**: Até 200 caracteres
- **Tamanho de email**: Até 255 caracteres

### 13.2 Limites de Operação
- **Tentativas de login**: 5 tentativas por minuto por IP
- **Backup manual**: 1 backup por hora
- **Retenção de backups**: 14 dias (configurável)
- **Retenção de logs**: 12 meses (configurável)
- **Período máximo de relatórios**: 1 ano
- **Agendamentos por dia**: Sem limite prático (testado até 500/dia)
- **Evoluções pendentes**: Sem limite prático
- **Notificações não lidas**: Sem limite prático

### 13.3 Limites de Performance
- **Timeout de requisição**: 30 segundos
- **Timeout de sessão**: 15 minutos de inatividade (configurável)
- **Tempo de carregamento do dashboard**: Máximo 2 segundos
- **Tempo de busca de pacientes**: Máximo 1 segundo
- **Tempo de geração de relatório**: Máximo 5 segundos

### 13.4 Limites de Escala
- **Unidades por sistema**: Sem limite prático
- **Usuários por unidade**: Sem limite prático
- **Pacientes por unidade**: Sem limite prático (testado até 10.000)
- **Agendamentos por unidade**: Sem limite prático (testado até 50.000)
- **Documentos por paciente**: Sem limite prático

---

## 14. Casos Especiais e Edge Cases

### 14.1 Agendamentos
- **Agendamento no passado**: Apenas admin pode criar (para correções)
- **Agendamento com duração zero**: Não permitido (mínimo 15 minutos)
- **Agendamento com sobreposição**: Sistema sugere horários alternativos
- **Agendamento cancelado após conclusão**: Não permitido (já gerou evolução)
- **Múltiplos agendamentos mesmo paciente/profissional**: Permitido em horários diferentes
- **Agendamento sem sala**: Permitido (atendimento remoto ou externo)
- **Agendamento sem profissional**: Não permitido (obrigatório)

### 14.2 Evoluções
- **Evolução sem atendimento**: Permitido (criação manual)
- **Múltiplas evoluções para mesmo atendimento**: Não permitido (1:1)
- **Evolução finalizada editada por admin**: Permitido com auditoria especial
- **Evolução pendente há muito tempo**: Sistema pode alertar coordenador (configurável)
- **Evolução sem conteúdo**: Permitido apenas como rascunho
- **Evolução de atendimento cancelado**: Permitido (para registro)

### 14.3 Pacientes
- **Paciente sem CPF**: Permitido (opcional)
- **Paciente duplicado (mesmo CPF)**: Sistema alerta mas permite (com aprovação)
- **Paciente inativo com agendamentos futuros**: Sistema alerta ao tentar agendar
- **Paciente sem responsável**: Permitido (para maiores de idade)
- **Paciente com múltiplas unidades**: Não permitido (1 paciente = 1 unidade)

### 14.4 Avaliações
- **Avaliação com template desativado**: Mantém versão original do template
- **Avaliação sem respostas**: Permitido apenas como rascunho
- **Múltiplas avaliações mesmo template/paciente**: Permitido
- **Avaliação com template atualizado**: Mantém versão original

### 14.5 Usuários e Permissões
- **Usuário sem unidade**: Não permitido (exceto admin)
- **Usuário inativo tentando logar**: Bloqueado com mensagem específica
- **Usuário com múltiplas unidades**: Seleciona unidade ativa na sessão
- **Admin sem unidade primária**: Permitido (admin não precisa de unidade)
- **Usuário excluído com registros**: Soft delete (mantém registros históricos)

### 14.6 Sistema
- **Backup durante operação**: Permitido (não bloqueia sistema)
- **Modo manutenção com usuários logados**: Notifica e permite concluir ações em andamento
- **Falha de autosave**: Sistema tenta novamente, se falhar alerta usuário
- **Conflito de edição simultânea**: Última edição vence (com aviso)
- **Token de recuperação usado duas vezes**: Invalida após primeiro uso

---

## 15. Validações Específicas Detalhadas

### 15.1 Validações de Agendamento
- **Data/hora início**: Deve ser data/hora válida, não no passado (exceto admin)
- **Data/hora fim**: Deve ser posterior à data/hora início
- **Duração mínima**: 15 minutos
- **Duração máxima**: 8 horas (configurável)
- **Profissional**: Deve existir e estar ativo
- **Paciente**: Deve existir e estar ativo na mesma unidade
- **Sala**: Se informada, deve existir e estar ativa na mesma unidade
- **Conflito de horário**: Verificar sobreposição com outros agendamentos do mesmo profissional
- **Bloqueio**: Verificar se horário está em feriado ou indisponibilidade

### 15.2 Validações de Paciente
- **Nome**: Obrigatório, mínimo 3 caracteres, máximo 200 caracteres
- **CPF**: Se informado, deve ser válido (algoritmo de validação) e único no sistema
- **Data de nascimento**: Se informada, deve ser data válida, não futura
- **Email**: Se informado, deve ser formato válido
- **Telefone**: Se informado, deve ser formato válido (aceita vários formatos)
- **Unidade**: Obrigatória, deve existir e estar ativa
- **Tags/Diagnósticos**: Máximo 10, cada tag até 50 caracteres

### 15.3 Validações de Evolução
- **Campos obrigatórios**: Configuráveis por unidade (padrão: relato clínico)
- **Tamanho de texto**: Sem limite prático, mas sistema pode alertar se muito extenso
- **Status**: Transições válidas apenas (pendente → rascunho → finalizada → revisada)
- **Finalização**: Exige todos os campos obrigatórios preenchidos
- **Assinatura**: Texto de confirmação obrigatório (mínimo 10 caracteres)

### 15.4 Validações de Avaliação
- **Template**: Deve existir e estar ativo
- **Paciente**: Deve existir e estar ativo na mesma unidade
- **Campos obrigatórios**: Validados conforme template
- **Tipos de campo**:
  - Texto: Até 10.000 caracteres
  - Número: Aceita decimais, validação de range se configurado
  - Data: Formato válido, validação de range se configurado
  - Seleção: Valor deve estar nas opções disponíveis
  - Checkbox: Boolean
  - Upload: Mesmas regras de documentos

### 15.5 Validações de Usuário
- **Nome**: Obrigatório, mínimo 3 caracteres, máximo 200 caracteres
- **Email**: Obrigatório, formato válido, único no sistema
- **Senha**: Mínimo 8 caracteres, maiúscula, número, caractere especial
- **Papel**: Deve ser um dos papéis válidos (admin, coordenador, profissional, secretária)
- **Unidade primária**: Obrigatória (exceto admin)
- **Status**: Ativo ou Inativo

### 15.6 Validações de Documento
- **Arquivo**: Obrigatório
- **Tipo**: PDF, JPG ou PNG
- **Tamanho**: Máximo 10MB
- **Paciente**: Deve existir e estar ativo
- **Categoria**: Opcional, se informada deve ser válida

---

## 16. Acessibilidade

### 16.1 Requisitos de Acessibilidade
- **Navegação por teclado**: Todas as funcionalidades devem ser acessíveis via teclado
- **Atalhos de teclado**:
  - `Ctrl+S` ou `Cmd+S`: Salvar (em formulários)
  - `Esc`: Fechar modais
  - `Enter`: Confirmar ações
  - `Tab`: Navegar entre campos
  - `Ctrl+K` ou `Cmd+K`: Busca global
- **Leitores de tela**: Interface compatível com leitores de tela
- **Contraste**: Contraste mínimo WCAG AA (4.5:1 para texto normal)
- **Foco visível**: Indicador de foco claro em todos os elementos interativos
- **Textos alternativos**: Imagens devem ter texto alternativo descritivo
- **Labels**: Todos os campos de formulário devem ter labels associados
- **Mensagens de erro**: Associadas aos campos correspondentes
- **Tamanho de fonte**: Mínimo 14px, com opção de aumentar
- **Modo de alto contraste**: Suportado (quando implementado)

### 16.2 Responsividade
- **Mobile**: Interface funcional em telas a partir de 320px
- **Tablet**: Interface otimizada para telas de 768px a 1024px
- **Desktop**: Interface completa para telas acima de 1024px
- **Touch**: Elementos interativos com área mínima de 44x44px
- **Orientação**: Suporta orientação vertical e horizontal

---

## 17. Casos de Uso Específicos

### 17.1 Caso: Atendimento Emergencial
**Cenário**: Secretária precisa agendar atendimento urgente
**Fluxo**:
1. Secretária acessa agenda
2. Busca horário livre mais próximo
3. Cria agendamento com observação "URGENTE"
4. Sistema notifica profissional automaticamente
5. Profissional visualiza no dashboard com destaque

### 17.2 Caso: Paciente com Múltiplos Responsáveis
**Cenário**: Paciente menor de idade com pais separados
**Fluxo**:
1. Secretária cadastra paciente
2. Adiciona primeiro responsável (mãe)
3. Adiciona segundo responsável (pai)
4. Ambos recebem notificações de agendamentos
5. Qualquer um pode confirmar/cancelar

### 17.3 Caso: Profissional em Férias
**Cenário**: Coordenador precisa bloquear período de férias
**Fluxo**:
1. Coordenador acessa configurações de agenda
2. Cria bloqueio de período (data início/fim)
3. Sistema bloqueia todos os horários do profissional
4. Tentativas de agendar retornam erro com mensagem
5. Após período, bloqueio é removido automaticamente

### 17.4 Caso: Evolução Pendente Antiga
**Cenário**: Evolução pendente há mais de 7 dias
**Fluxo**:
1. Sistema identifica evolução pendente há mais de 7 dias
2. Notifica coordenador automaticamente
3. Coordenador pode:
   - Contatar profissional
   - Marcar como arquivada
   - Reatribuir para outro profissional (admin)

### 17.5 Caso: Template Atualizado com Avaliações Antigas
**Cenário**: Admin atualiza template, mas há avaliações antigas
**Fluxo**:
1. Admin atualiza template (cria nova versão)
2. Avaliações antigas mantêm versão original
3. Novas avaliações usam versão atualizada
4. Sistema permite visualizar versão usada em cada avaliação

### 17.6 Caso: Restauração de Backup
**Cenário**: Admin precisa restaurar backup após erro
**Fluxo**:
1. Admin acessa backups
2. Seleciona backup a restaurar
3. Sistema exige confirmação dupla
4. Sistema entra em modo manutenção
5. Restaura dados do backup
6. Sistema notifica admin da conclusão
7. Admin verifica dados e libera sistema

### 17.7 Caso: Usuário com Acesso a Múltiplas Unidades
**Cenário**: Coordenador gerencia duas unidades
**Fluxo**:
1. Usuário faz login
2. Sistema detecta múltiplas unidades
3. Exibe modal de seleção
4. Usuário seleciona unidade ativa
5. Todos os dados são filtrados pela unidade selecionada
6. Usuário pode trocar unidade a qualquer momento
7. Sistema lembra última unidade selecionada

### 17.8 Caso: Auditoria de Acesso a Dados Sensíveis
**Cenário**: Admin precisa verificar quem acessou dados de paciente específico
**Fluxo**:
1. Admin acessa logs de auditoria
2. Filtra por tipo de entidade "Paciente"
3. Filtra por ID do paciente
4. Visualiza todas as ações (visualização, edição, download)
5. Exporta relatório CSV
6. Analisa padrões de acesso

---

## 18. Workflows Complexos

### 18.1 Workflow: Revisão Semanal de Coordenação
**Objetivo**: Coordenador revisa trabalho da semana
**Passos**:
1. Coordenador acessa dashboard
2. Visualiza KPIs da semana
3. Acessa relatório de produtividade
4. Identifica profissionais com baixa produtividade
5. Acessa evoluções pendentes
6. Revisa evoluções finalizadas
7. Marca como revisadas
8. Envia notificação para profissionais com pendências
9. Gera relatório semanal
10. Compartilha com administração

### 18.2 Workflow: Onboarding de Novo Profissional
**Objetivo**: Integrar novo profissional ao sistema
**Passos**:
1. Admin cria usuário no painel
2. Define papel como "profissional"
3. Atribui unidade
4. Define cor na agenda
5. Coordenador configura disponibilidade
6. Sistema envia email de boas-vindas
7. Profissional recebe link de definição de senha
8. Profissional faz primeiro login
9. Completa perfil pessoal
10. Visualiza agenda com slots disponíveis
11. Sistema está pronto para uso

### 18.3 Workflow: Migração de Paciente entre Unidades
**Objetivo**: Transferir paciente para outra unidade
**Passos**:
1. Admin acessa perfil do paciente
2. Altera unidade do paciente
3. Sistema valida:
   - Não há agendamentos futuros na unidade antiga
   - Não há evoluções pendentes
4. Se válido: transfere paciente
5. Se inválido: exibe pendências a resolver
6. Após transferência:
   - Histórico mantido (vinculado à unidade antiga)
   - Novos registros vinculados à nova unidade
   - Timeline mostra transferência

### 18.4 Workflow: Auditoria Completa de Paciente
**Objetivo**: Rastrear todas as ações relacionadas a um paciente
**Passos**:
1. Admin acessa logs de auditoria
2. Filtra por paciente específico
3. Visualiza:
   - Criação e edições do cadastro
   - Todos os agendamentos
   - Todas as evoluções
   - Todas as avaliações
   - Uploads de documentos
   - Acessos ao prontuário
4. Exporta relatório completo
5. Analisa padrões e identifica inconsistências

---

## 19. Busca e Filtros Avançados

### 19.1 Busca Global
- **Objetivo**: Encontrar qualquer informação rapidamente
- **Funcionalidades**:
  - Campo de busca no header (sempre visível)
  - Busca em tempo real (conforme digita)
  - Busca em múltiplas entidades:
    - Pacientes (nome, CPF, tags)
    - Agendamentos (paciente, profissional, data)
    - Evoluções (paciente, profissional, conteúdo)
    - Avaliações (paciente, template)
    - Usuários (nome, email)
  - Resultados agrupados por tipo
  - Navegação direta para resultado
  - Histórico de buscas (últimas 5)
  - Atalho: `Ctrl+K` ou `Cmd+K` abre busca global

### 19.2 Filtros Avançados
- **Agenda**:
  - Por profissional (múltipla seleção)
  - Por paciente
  - Por sala (múltipla seleção)
  - Por status (múltipla seleção)
  - Por categoria
  - Por período (data início/fim)
  - Por unidade (admin)
- **Pacientes**:
  - Por nome (busca textual)
  - Por diagnóstico/tag (múltipla seleção)
  - Por unidade
  - Por status (ativo/inativo)
  - Por período de cadastro
  - Por responsável
- **Evoluções**:
  - Por status (múltipla seleção)
  - Por profissional (múltipla seleção)
  - Por paciente
  - Por período
  - Por unidade (admin)
- **Avaliações**:
  - Por template (múltipla seleção)
  - Por status
  - Por profissional
  - Por paciente
  - Por período
  - Por unidade (admin)

### 19.3 Filtros Salvos (Favoritos)
- Usuário pode salvar combinações de filtros complexas
- Nome personalizado (ex.: "Meus pacientes ativos", "Agenda desta semana")
- Acesso rápido via dropdown ou command palette
- Compartilhamento de filtros (opcional, futuro)
- Filtros persistem entre sessões

### 19.4 Busca Inteligente
- Busca por sinônimos (ex.: "terapia" encontra "psicoterapia")
- Busca por parte do nome (não precisa ser exato)
- Busca case-insensitive
- Busca com acentos (normalização)
- Sugestões enquanto digita (autocomplete)

---

## 20. Exportação e Importação de Dados

### 20.1 Exportação de Relatórios
- **Formatos suportados**:
  - PDF: Para impressão e arquivamento
  - CSV/Excel: Para análise em planilhas
  - JSON: Para integração com outros sistemas (futuro)
- **Tipos de relatórios exportáveis**:
  - Relatório de produtividade
  - Relatório de frequência
  - Relatório clínico
  - Relatório de unidade
  - Lista de pacientes
  - Lista de agendamentos
  - Lista de evoluções
  - Lista de avaliações
- **Funcionalidades**:
  - Exportação mantém filtros aplicados
  - Inclui cabeçalho com nome do sistema e data
  - Inclui rodapé com informações de geração
  - Opção de incluir/excluir colunas
  - Exportação assíncrona para relatórios grandes (notificação quando pronto)

### 20.2 Exportação de Dados Completos
- **Exportação de paciente**:
  - Dados cadastrais completos
  - Todas as evoluções (PDF ou JSON)
  - Todas as avaliações
  - Todos os documentos (links ou arquivos)
  - Timeline completa
  - Formato: ZIP com estrutura organizada
- **Exportação de unidade**:
  - Todos os pacientes da unidade
  - Todos os agendamentos
  - Todas as evoluções
  - Todas as avaliações
  - Configurações da unidade
  - Formato: ZIP estruturado
- **Permissões**: Apenas admin pode exportar dados completos

### 20.3 Importação de Dados
- **Importação de pacientes**:
  - Formato: CSV/Excel com colunas específicas
  - Validação de dados antes de importar
  - Preview dos dados a importar
  - Opção de atualizar existentes ou criar novos
  - Relatório de importação (sucessos e erros)
- **Importação de agendamentos**:
  - Formato: CSV/Excel
  - Validação de conflitos
  - Criação em lote
- **Permissões**: Apenas admin e coordenador podem importar

### 20.4 Backup e Restauração
- **Backup completo**:
  - Banco de dados completo
  - Arquivos (documentos, logos)
  - Configurações do sistema
  - Formato: ZIP criptografado
- **Restauração**:
  - Restauração completa do sistema
  - Restauração seletiva (apenas dados ou apenas arquivos)
  - Validação de integridade antes de restaurar
  - Confirmação dupla obrigatória

---

## 21. Impressão e Visualização

### 21.1 Impressão de Evoluções
- Botão "Imprimir" na visualização de evolução
- Layout otimizado para impressão:
  - Cabeçalho com logo e nome do sistema
  - Dados do paciente
  - Dados do profissional
  - Data do atendimento
  - Conteúdo da evolução formatado
  - Rodapé com data de impressão
- Opção de incluir/excluir observações internas
- Modo de impressão (CSS print media)

### 21.2 Impressão de Avaliações
- Layout similar à evolução
- Inclui template usado
- Respostas formatadas conforme tipo de campo
- Gráficos convertidos para imagem (se houver)

### 21.3 Impressão de Relatórios
- Layout otimizado para papel A4
- Quebra de página inteligente
- Cabeçalhos repetidos em cada página
- Gráficos em alta resolução
- Opção de orientação (retrato/paisagem)

### 21.4 Visualização de Documentos
- Visualizador integrado para PDFs
- Visualizador de imagens (zoom, rotação)
- Download direto
- Impressão do documento
- Navegação entre documentos do paciente

### 21.5 Visualização de Agenda
- Modo de impressão da agenda:
  - Visualização semanal ou mensal
  - Cores dos profissionais mantidas
  - Informações essenciais (paciente, horário, profissional)
  - Layout otimizado para papel

---

## 22. Atalhos de Teclado e Produtividade

### 22.1 Atalhos Globais
- `Ctrl+K` ou `Cmd+K`: Abre busca global / Command palette
- `Ctrl+S` ou `Cmd+S`: Salvar (em formulários)
- `Esc`: Fechar modal/dialog
- `Enter`: Confirmar ação (em modais)
- `Tab`: Navegar entre campos
- `Shift+Tab`: Navegar para trás
- `Ctrl+/` ou `Cmd+/`: Mostrar ajuda de atalhos

### 22.2 Atalhos por Módulo
- **Agenda**:
  - `N`: Novo agendamento
  - `T`: Hoje (voltar para data atual)
  - `←` / `→`: Dia anterior/próximo
  - `↑` / `↓`: Semana anterior/próxima
- **Pacientes**:
  - `N`: Novo paciente
  - `F`: Focar no campo de busca
- **Evoluções**:
  - `N`: Nova evolução
  - `F`: Finalizar evolução
- **Dashboard**:
  - `R`: Atualizar dados
  - `1-9`: Navegar para módulos (numeração do menu)

### 22.3 Command Palette
- Acesso via `Ctrl+K` ou `Cmd+K`
- Busca de ações:
  - "Novo agendamento"
  - "Novo paciente"
  - "Nova evolução"
  - "Buscar paciente {nome}"
  - "Ir para agenda"
  - "Ir para relatórios"
- Busca de entidades:
  - Pacientes
  - Agendamentos
  - Evoluções
- Navegação direta para resultado

### 22.4 Ações Rápidas
- Menu de contexto (clique direito):
  - Em agendamento: Editar, Cancelar, Concluir
  - Em paciente: Ver perfil, Novo agendamento, Nova evolução
  - Em evolução: Editar, Finalizar, Imprimir
- Botões de ação rápida em cards:
  - "Finalizar" direto do card de evolução
  - "Ver perfil" direto do card de paciente

---

## 23. Help e Ajuda Contextual

### 23.1 Ajuda Contextual
- **Tooltips**: Explicações curtas ao passar o mouse
- **Ícone de ajuda**: `?` ao lado de campos complexos
- **Modais de ajuda**: Explicações detalhadas para funcionalidades
- **Tours guiados**: Para novos usuários (opcional)
- **Vídeos tutoriais**: Links para vídeos explicativos (opcional)

### 23.2 Documentação Integrada
- **Manual do usuário**: Acessível via menu "Ajuda"
- **FAQ**: Perguntas frequentes por módulo
- **Glossário**: Definições de termos técnicos
- **Guias rápidos**: Passo a passo para tarefas comuns
- **Changelog**: Histórico de atualizações do sistema

### 23.3 Suporte
- **Contato de suporte**: Email e telefone visíveis
- **Chat de suporte**: Integração com sistema de chat (opcional)
- **Tickets**: Sistema de abertura de tickets (opcional)
- **Status do sistema**: Link para página de status

### 23.4 Feedback do Usuário
- Botão "Enviar feedback" em todas as páginas
- Formulário de sugestões
- Relatório de bugs
- Avaliação de funcionalidades

---

## 24. Histórico de Alterações

### 24.1 Histórico de Edições
- **Pacientes**: Histórico de todas as alterações
  - Campo alterado
  - Valor anterior
  - Valor novo
  - Data/hora
  - Usuário que alterou
- **Evoluções**: Histórico de versões
  - Versões salvas (autosave)
  - Comparação entre versões
  - Restaurar versão anterior (antes de finalizar)
- **Avaliações**: Similar às evoluções
- **Agendamentos**: Histórico de alterações de status e horários

### 24.2 Visualização de Histórico
- Timeline de alterações
- Comparação lado a lado
- Filtros por data, usuário, tipo de alteração
- Exportação do histórico

### 24.3 Auditoria de Alterações
- Todas as alterações registradas em logs de auditoria
- Impossível deletar histórico (apenas arquivar)
- Histórico mantido mesmo após exclusão de registro (soft delete)

---

## 25. Temas e Personalização Visual

### 25.1 Temas do Sistema
- **Tema claro**: Padrão, cores claras
- **Tema escuro**: Modo escuro completo
- **Tema automático**: Segue preferência do sistema operacional
- Aplicação imediata ao selecionar
- Persistência da escolha

### 25.2 Cores Personalizáveis
- **Cores primárias**: Configuráveis pelo admin
- **Cores secundárias**: Configuráveis pelo admin
- **Cores de status**: 
  - Sucesso (verde)
  - Aviso (amarelo)
  - Erro (vermelho)
  - Info (azul)
- **Cores de profissionais**: Cada profissional pode escolher cor na agenda

### 25.3 Personalização de Interface
- **Densidade**: Compacto, Confortável, Espaçoso
- **Tamanho de fonte**: Pequeno, Médio, Grande
- **Layout**: Sidebar sempre visível, colapsável, ou oculta
- **Ordem de colunas**: Em tabelas (arrastar para reordenar)

### 25.4 Branding Completo
- Logo do sistema (upload)
- Favicon personalizado
- Nome do sistema
- Cores da marca
- Mensagens personalizadas
- Rodapé customizável
- Email templates personalizados (opcional)

---

## 26. Recorrência e Agendamentos Periódicos

### 26.1 Agendamentos Recorrentes
- **Tipos de recorrência**:
  - Diário (todos os dias)
  - Semanal (dias específicos da semana)
  - Quinzenal (a cada 15 dias)
  - Mensal (dia específico do mês)
  - Personalizado (intervalo customizado)
- **Configuração**:
  - Data/hora inicial
  - Frequência
  - Data final (ou número de ocorrências)
  - Exceções (datas específicas a pular)
- **Gerenciamento**:
  - Editar série completa
  - Editar ocorrência única
  - Cancelar série completa
  - Cancelar ocorrência única

### 26.2 Bloqueios Periódicos
- Bloqueios recorrentes (ex.: toda segunda-feira)
- Feriados fixos e móveis
- Indisponibilidade periódica de profissional
- Indisponibilidade periódica de sala

### 26.3 Validações de Recorrência
- Verificar conflitos em todas as ocorrências
- Sugerir ajustes se houver conflitos
- Permitir criar mesmo com conflitos (admin/coordenador)

---

## 27. Integrações e APIs

### 27.1 APIs Disponíveis (Futuro)
- **API REST**: Para integração com sistemas externos
- **Autenticação**: Token-based (JWT)
- **Endpoints principais**:
  - CRUD de pacientes
  - CRUD de agendamentos
  - Leitura de evoluções
  - Webhooks para eventos
- **Documentação**: Swagger/OpenAPI
- **Rate limiting**: Limite de requisições por minuto

### 27.2 Webhooks (Futuro)
- Eventos disponíveis:
  - Agendamento criado/atualizado/cancelado
  - Evolução finalizada
  - Avaliação finalizada
  - Paciente criado/atualizado
- Configuração de URLs de destino
- Retry automático em caso de falha

### 27.3 Integrações Comuns (Futuro)
- **Calendários externos**: Google Calendar, Outlook
- **Sistemas de pagamento**: Para faturamento (se necessário)
- **Sistemas de prontuário**: Interoperabilidade
- **Sistemas de laboratório**: Recebimento de resultados
- **SMS/WhatsApp**: Notificações externas

### 27.4 Exportação para Integração
- Exportação em formatos padrão (HL7, FHIR - futuro)
- Estrutura de dados compatível
- Mapeamento de campos configurável

---

## 28. Recuperação de Dados e Desfazer Ações

### 28.1 Lixeira (Soft Delete)
- Registros excluídos vão para lixeira
- Lixeira acessível apenas por admin
- Período de retenção: 30 dias (configurável)
- Restauração de registros da lixeira
- Exclusão permanente após período

### 28.2 Desfazer Ações
- Desfazer última ação (Ctrl+Z) em formulários
- Histórico de ações recentes
- Restaurar versão anterior (evoluções/avaliações antes de finalizar)

### 28.3 Recuperação de Sessão
- Recuperação automática de formulários não salvos
- Pergunta ao retornar: "Você tinha um formulário em andamento. Deseja restaurar?"
- Restauração de filtros e preferências

### 28.4 Backup de Segurança
- Backups automáticos antes de operações críticas (opcional)
- Restauração pontual de dados
- Versionamento de backups

---

## 29. Notificações Avançadas

### 29.1 Canais de Notificação
- **In-app**: Notificações dentro do sistema
- **Email**: Notificações por email (configurável)
- **SMS**: Notificações por SMS (opcional, futuro)
- **Push**: Notificações push no navegador (opcional)

### 29.2 Preferências de Notificação
- Usuário pode configurar quais notificações receber
- Frequência de notificações (imediata, resumo diário, semanal)
- Silenciar notificações por período
- Categorias de notificação (clínica, administrativa, sistema)

### 29.3 Notificações Agendadas
- Notificações com data de publicação futura
- Notificações recorrentes (ex.: lembrete semanal)
- Notificações com expiração automática

### 29.4 Notificações Inteligentes
- Agrupamento de notificações similares
- Priorização (urgente, normal, baixa)
- Notificações baseadas em comportamento (ex.: alerta se evolução pendente há muito tempo)

---

## 30. Relatórios Avançados

### 30.1 Relatórios Customizáveis
- Usuário pode criar relatórios personalizados
- Seleção de campos a incluir
- Ordenação e agrupamento
- Filtros salvos como template de relatório

### 30.2 Gráficos Interativos
- Gráficos clicáveis (drill-down)
- Zoom e pan em gráficos temporais
- Exportação de gráficos como imagem
- Múltiplos tipos de gráfico:
  - Barras (vertical/horizontal)
  - Linhas
  - Pizza
  - Área
  - Dispersão

### 30.3 Dashboards Personalizáveis
- Usuário escolhe quais cards exibir
- Reordenação via drag & drop
- Tamanhos de cards configuráveis
- Múltiplos dashboards (salvos como favoritos)

### 30.4 Relatórios Agendados
- Agendamento de geração automática de relatórios
- Envio automático por email
- Formato configurável (PDF, CSV)
- Destinatários configuráveis

---

## 31. Notas Finais

Este documento descreve **o que** o sistema deve fazer, sem especificar **como** implementar. É uma especificação funcional completa que pode ser usada para:

1. **Transformar sistemas open source**: Adaptar sistemas existentes (como OpenEMR) para atender estas especificações
2. **Desenvolvimento do zero**: Guiar o desenvolvimento de um novo sistema
3. **Validação de requisitos**: Verificar se um sistema atende aos requisitos
4. **Documentação para stakeholders**: Explicar funcionalidades para não-técnicos

### 31.1 Personalização White Label
Todas as referências a nomes, cores, logos e mensagens devem ser configuráveis através do painel administrativo, permitindo que o sistema seja completamente personalizado para diferentes organizações.

### 31.2 Extensibilidade
O sistema deve ser projetado de forma modular, permitindo adicionar novos módulos ou funcionalidades sem quebrar funcionalidades existentes.

### 31.3 Manutenção
Este documento deve ser atualizado sempre que novas funcionalidades forem adicionadas ou regras de negócio forem alteradas.

---

**Fim do Documento**

