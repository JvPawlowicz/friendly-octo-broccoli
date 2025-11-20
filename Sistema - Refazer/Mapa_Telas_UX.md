# Mapa de Telas & UX – Equidade+ Nova Stack

## 1. Estrutura de Navegação
- **Layout padrão**: sidebar fixa (desktop), header com avatar, busca global, seletor de unidade.
- **Breadcumbs**: exibidos em páginas internas (paciente, evolução, avaliação).
- **Command Palette**: `⌘/Ctrl + K` abre pesquisa global (paciente, agenda, relatório, notificação).

## 2. Fluxo de Autenticação
1. **Landing / Login**
   - Campos: email, senha.
   - Exibe status do sistema (online/manutenção/offline).
   - Logo, nome e cores dinâmicos (`SystemSettings`).
2. **Seleção de Unidade**
   - Modal pós-login se usuário possui >1 unidade.
   - Lembrar escolha (cookie + `user_preferences`).
3. **Recuperação de Senha**
   - Link para fluxo de reset (envio e email template).

## 3. Telas Protegidas

### 3.1 Dashboard (`/dashboard`)
- **Seções**
  - Cards KPIs (Atendimentos do dia, Evoluções pendentes, Pacientes ativos).
  - Gráfico produtividade (por profissional/unidade).
  - Lista de notificações recentes.
  - Atalhos rápidos (Novo agendamento, Novo paciente, Pendências).
- **Interações**
  - Filtros por unidade/período (persistem).
  - Layout responsivo: 1 coluna (mobile), 2-3 (tablet), 4 (desktop).

### 3.2 Agenda (`/agenda`)
- **Componentes**
  - Toolbar (modo: Dia/Semana/Mês/Lista, filtros, criação).
  - Calendário com slots coloridos por profissional.
  - Sidebar filtros (unidade, profissional, paciente, sala, status).
  - Legenda de cores por profissional.
- **UX**
  - Clique em slot vazio abre modal pré-preenchido.
  - Drag & drop com ghost e validação.
  - Tooltip exibe detalhes (paciente, status, notas).
  - Botões de atalho (Hoje, Próx, Anterior).

### 3.3 Pacientes (`/pacientes`)
- **Lista**
  - Tabela com foto/ iniciais, nome, idade, tags, unidade, responsável.
  - Filtro avançado (nome, diagnóstico, tag, unidade).
  - Ações rápidas (ver, editar, documentos).
- **Perfil do paciente (`/pacientes/{id}`)**
  - Header com alertas (alergias, restrições).
  - Abas: Dados Gerais, Documentos, Timeline, Avaliações.
  - Upload drag & drop com barra de progresso.
  - Timeline vertical com ícones (evolução, avaliação, documento, notificação).

### 3.4 Evoluções (`/evolucoes`)
- **Lista**
  - Tabs: Pendentes, Minhas, Unidade, Concluídas, Revisadas.
  - Colunas: paciente, profissional, atendimento, status, datas.
- **Editor (`/evolucoes/{id}`)**
  - Layout em duas colunas (form + preview/resumo).
  - Campos: relato clínico, conduta, objetivos, observações internas.
  - Autosave com indicador (“Rascunho salvo às HH:MM”).
  - Botões: Salvar rascunho, Finalizar evolução.
  - Modal de confirmação com assinatura textual (ex: “Confirmo as informações”).
  - Visualização de histórico/ addendums.

### 3.5 Avaliações (`/avaliacoes`)
- **Template Builder (Admin)**
  - Drag & drop de campos (texto, número, seleção, checkbox, data, upload).
  - Preview em tempo real.
  - Versão exibida na barra lateral.
- **Preenchimento (Profissional)**
  - Lista de avaliações por paciente.
  - Campos dinâmicos conforme template.
  - Autosave, finalização, assinatura simples.
  - Revisão (coordenador/admin) com comentário.

### 3.6 Relatórios (`/relatorios`)
- **Layout**
  - Sidebar com filtros (unidade, profissional, paciente, período, status).
  - Main area com gráficos (Chart.js) + tabela detalhada.
- **Funções**
  - Salvar filtro favorito.
  - Exportar PDF/CSV (botões com estado de processamento).
  - Modo leitura para impressão.

### 3.7 Chat (`/chat`)
- **Estrutura**
  - Coluna esquerda: lista de conversas (1:1 + grupo unidade).
  - Header com busca e filtros (não lidas).
  - Área de mensagens com bubble e timestamp.
  - Composer com upload de imagem (preview) e ícones.
- **Detalhes**
  - Polling 3s (`wire:poll` equivalente via fetch).
  - Badge de não lido no ícone da sidebar.
  - Admin acessa todas as conversas (indicador de auditoria).

### 3.8 Notificações (`/notificacoes`)
- **Feed**
  - Cards por tipo (aviso, alerta, lembrete).
  - Filtros por unidade, tipo, período.
  - Opção “marcar tudo como lido”.
- **Mural (Admin/Coord)**
  - Editor rich text para publicar mensagens.
  - Agendamento (publicar agora vs data futura).

### 3.9 Configurações (`/configuracoes`)
- **Abas**
  1. Perfil: dados pessoais, conselho, senha.
  2. Preferências: tema, agenda view, unidade padrão, cor profissional.
  3. Agenda: horários, bloqueios, feriados (coordenador/admin).
  4. Unidades: CRUD (admin).
  5. Usuários: CRUD, redefinir senha, impersonate (admin).
  6. Sistema: branding, status, mensagens.
  7. Backups: listar, gerar, restaurar.
- **UI**
  - Formulários consistentes com `FormField`.
  - Alertas de sucesso/erro via toasts.

### 3.10 Painel Admin (`/admin/*`)
- Construído com Refine (Ant Design).
- Recursos: Users, Units, Rooms, AssessmentTemplates, SystemSettings, Backups, AuditLogs, Notifications.
- Layout com breadcrumbs automáticos, filtros e actions visíveis por permissão.

## 4. Responsividade
- **Mobile**
  - Sidebar colapsada (hamburger).
  - Agenda exibe lista (cards por agendamento).
  - Painéis utilizam tabs horizontais.
  - Botão fixo “Ações rápidas” (f) abre menu.
- **Tablet**
  - Sidebar retrátil (mini ícones).
  - Agenda mostra duas colunas.
  - Formularios ocupam largura média (max 720px).
- **Desktop**
  - Layout completo (sidebar + conteúdo 1200–1440px).
  - Gráficos com tooltips detalhados.

## 5. Acessibilidade & Usabilidade
- **Acessibilidade**
  - Contrast ratio >= 4.5:1.
  - Labels explícitos, aria-label em ícones.
  - Foco visível (outline azul).
  - Navegação via teclado: sidebar (`Ctrl+1` a `Ctrl+9`), agenda (`Shift+←/→`), chat (`Ctrl+J`).
- **Usabilidade**
  - Feedback imediato em ações (spinners, toasts).
  - Mensagens claras (“Nenhum agendamento para hoje”).
  - Confirm dialogs para ações críticas (excluir, restaurar backup).

## 6. Conteúdo Dinâmico
- **Branding**
  - Nome do sistema, logo, cores primárias configuráveis.
  - Mensagem de status exibida em header e login.
- **Alertas**
  - Banner global para manutenção programada.
  - Indicadores no header (pendências, chat).

## 7. Referências Visuais
- Prototipagem no Figma (link a definir).
- Layouts baseados no design system documentado (`docs/blueprint 2.0`; seção design system).

## 8. Pendências UX
- Definir microinterações específicas (animações agenda, drag highlight).
- Criar componentes de onboarding para novos usuários (tour rápido).
- Validar com usuários finais (secretária, profissional) se fluxos cobrem expectativas.

