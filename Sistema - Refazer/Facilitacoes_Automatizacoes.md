# Facilitações e Automações – Equidade+ Nova Stack

Documento que detalha todas as facilitações, automações e melhorias de produtividade implementadas no sistema para acelerar o trabalho dos usuários.

---

## 1. Automações Inteligentes

### 1.1 Preenchimento Automático de Evoluções
- **Objetivo**: Reduzir tempo de preenchimento de evoluções.
- **Funcionalidade**:
  - Templates de evolução por tipo de atendimento (configurável por unidade).
  - Preenchimento automático de campos comuns baseado em histórico do paciente.
  - Sugestões de condutas baseadas em diagnósticos anteriores.
- **Implementação**: tRPC procedure `evolutions.getSuggestions` retorna campos pré-preenchidos.

### 1.2 Sugestões de Horários na Agenda
- **Objetivo**: Evitar conflitos e otimizar uso de salas.
- **Funcionalidade**:
  - Ao criar agendamento, sistema sugere próximos horários livres baseado em:
    - Histórico de horários preferidos do profissional.
    - Disponibilidade de salas.
    - Padrões de agendamento do paciente.
- **Implementação**: tRPC procedure `appointments.suggestTimes` retorna array de horários sugeridos.

### 1.3 Auto-save Agressivo
- **Objetivo**: Evitar perda de dados.
- **Funcionalidade**:
  - Auto-save a cada 30 segundos em evoluções e avaliações (não apenas onBlur).
  - Indicador visual de "Salvando..." / "Salvo".
  - Recuperação automática de rascunho ao reabrir.
- **Implementação**: tRPC procedure `evolutions.autosave` chamada via `setInterval` no frontend.

---

## 2. Templates e Presets

### 2.1 Templates de Evoluções
- **Objetivo**: Padronizar e acelerar preenchimento.
- **Funcionalidade**:
  - Templates por tipo de atendimento (ex.: "Primeira consulta", "Retorno", "Avaliação").
  - Campos pré-preenchidos com variáveis (ex.: `{paciente.nome}`, `{data}`).
  - Admin pode criar/editar templates no painel.
- **Implementação**: Tabela `EvolutionTemplate` + procedure `evolutions.createFromTemplate`.

### 2.2 Filtros Salvos como Favoritos
- **Objetivo**: Reutilizar filtros complexos.
- **Funcionalidade**:
  - Usuário salva filtros de agenda, pacientes, relatórios como "favoritos".
  - Nome personalizado (ex.: "Meus pacientes ativos", "Agenda desta semana").
  - Acesso rápido via dropdown ou command palette.
- **Implementação**: Campo `saved_filters` (JSON) em `UserPreference` + procedures `filters.save`, `filters.list`.

### 2.3 Dashboards Personalizáveis
- **Objetivo**: Adaptar dashboard ao perfil de uso.
- **Funcionalidade**:
  - Usuário escolhe quais cards exibir.
  - Reordena cards via drag & drop.
  - Salva layout personalizado.
- **Implementação**: Campo `dashboard_layout` (JSON) em `UserPreference` + componente `DashboardBuilder`.

---

## 3. Atalhos e Produtividade

### 3.1 Command Palette (Cmd+K)
- **Objetivo**: Acesso rápido a qualquer funcionalidade.
- **Funcionalidade**:
  - Busca global: pacientes, agendamentos, evoluções.
  - Ações rápidas: "Novo agendamento", "Nova evolução", "Buscar paciente".
  - Navegação: "Ir para agenda", "Ir para relatórios".
- **Implementação**: Componente `CommandPalette` (shadcn/ui) + tRPC procedure `search.global`.

### 3.2 Atalhos de Teclado
- **Objetivo**: Navegação rápida sem mouse.
- **Funcionalidade**:
  - `N` = Novo agendamento (na agenda).
  - `S` = Buscar (abre command palette).
  - `Esc` = Fechar modal/dialog.
  - `Ctrl+S` = Salvar (em formulários).
  - `Ctrl+K` = Command palette.
- **Implementação**: Hook `useKeyboardShortcuts` + event listeners.

### 3.3 Drag & Drop na Agenda
- **Objetivo**: Mover agendamentos rapidamente.
- **Funcionalidade**:
  - Arrastar agendamento para outro horário.
  - Validação em tempo real (conflitos, bloqueios).
  - Feedback visual durante arrasto.
- **Implementação**: Biblioteca `@dnd-kit/core` + tRPC procedure `appointments.move`.

---

## 4. Validações Inteligentes

### 4.1 Detecção de Conflitos na Agenda
- **Objetivo**: Evitar sobreposições antes de salvar.
- **Funcionalidade**:
  - Ao selecionar horário, sistema verifica conflitos.
  - Mostra aviso visual com horários alternativos sugeridos.
  - Permite forçar (apenas admin/coordenador).
- **Implementação**: tRPC procedure `appointments.checkConflicts` + componente `ConflictWarning`.

### 4.2 Busca Fuzzy de Pacientes
- **Objetivo**: Encontrar pacientes mesmo com erros de digitação.
- **Funcionalidade**:
  - Busca por nome, CPF, telefone.
  - Tolerância a erros de digitação (ex.: "João" encontra "Joao").
  - Sugestões enquanto digita (debounce 300ms).
- **Implementação**: Prisma full-text search + procedure `patients.search`.

### 4.3 Validação em Tempo Real
- **Objetivo**: Feedback imediato ao usuário.
- **Funcionalidade**:
  - Validação de CPF, email, telefone enquanto digita.
  - Mensagens de erro contextuais.
  - Indicadores visuais (verde = válido, vermelho = inválido).
- **Implementação**: Zod schemas + `react-hook-form` + validação client-side.

---

## 5. Notificações Simplificadas

### 5.1 Notificações In-App
- **Objetivo**: Comunicação rápida sem email.
- **Funcionalidade**:
  - Badge no ícone de notificações com contador.
  - Feed de notificações na sidebar.
  - Marcação automática como lida ao visualizar.
- **Implementação**: tRPC procedure `notifications.list` + componente `NotificationBell`.

### 5.2 Badges Visuais
- **Objetivo**: Indicadores rápidos de pendências.
- **Funcionalidade**:
  - Badge "3" em "Evoluções" = 3 pendentes.
  - Badge "5" em "Agenda" = 5 agendamentos hoje.
  - Atualização em tempo real (polling leve ou SSE futuro).
- **Implementação**: tRPC procedure `dashboard.badges` + componente `Badge`.

### 5.3 Toasts Simples
- **Objetivo**: Feedback imediato de ações.
- **Funcionalidade**:
  - Toast de sucesso: "Evolução salva com sucesso!".
  - Toast de erro: "Erro ao salvar. Tente novamente.".
  - Auto-dismiss após 3 segundos.
- **Implementação**: shadcn/ui `Toast` + contexto `ToastProvider`.

---

## 6. Busca e Filtros

### 6.1 Busca Global Unificada
- **Objetivo**: Encontrar qualquer coisa rapidamente.
- **Funcionalidade**:
  - Busca pacientes, agendamentos, evoluções, avaliações.
  - Resultados agrupados por tipo.
  - Navegação direta para resultado.
- **Implementação**: tRPC procedure `search.global` + componente `GlobalSearch`.

### 6.2 Filtros Persistentes
- **Objetivo**: Manter filtros entre sessões.
- **Funcionalidade**:
  - Filtros salvos em `localStorage` + banco.
  - Restauração automática ao reabrir página.
  - Compartilhamento de filtros (opcional, futuro).
- **Implementação**: Hook `usePersistedFilters` + tRPC procedure `filters.save`.

### 6.3 Histórico de Buscas
- **Objetivo**: Reutilizar buscas recentes.
- **Funcionalidade**:
  - Dropdown mostra últimas 5 buscas.
  - Limpar histórico.
- **Implementação**: `localStorage` + componente `SearchHistory`.

---

## 7. Interface Mais Limpa

### 7.1 Menos Cliques
- **Objetivo**: Reduzir fricção.
- **Funcionalidade**:
  - Criar agendamento em 1 clique (modal pré-preenchido).
  - Ações rápidas em cards (ex.: "Finalizar evolução" direto do card).
  - Context menu (clique direito) com ações comuns.
- **Implementação**: Componentes `QuickActions` + `ContextMenu` (shadcn/ui).

### 7.2 Modais Contextuais
- **Objetivo**: Criar registros rapidamente.
- **Funcionalidade**:
  - Clique em slot vazio da agenda → modal já com data/hora.
  - Clique em paciente → modal de "Nova evolução" pré-preenchido.
- **Implementação**: Componentes modais com props de contexto.

### 7.3 Feedback Visual Imediato
- **Objetivo**: Confirmar ações sem esperar resposta.
- **Funcionalidade**:
  - Loading states com skeletons.
  - Optimistic UI (atualiza UI antes da resposta do servidor).
  - Animações suaves (fade, slide).
- **Implementação**: TanStack Query `useMutation` com `onMutate` + shadcn/ui `Skeleton`.

---

## 8. Dados Pré-preenchidos

### 8.1 Seeds Realistas
- **Objetivo**: Desenvolvimento e demos rápidos.
- **Funcionalidade**:
  - Seeds com dados fictícios realistas (nomes, datas, diagnósticos).
  - Múltiplos cenários (unidade pequena, grande, múltiplas unidades).
- **Implementação**: Script `prisma/seed-demo.ts` + faker.js.

### 8.2 Templates Prontos de Avaliações
- **Objetivo**: Começar a usar imediatamente.
- **Funcionalidade**:
  - Templates padrão: "Anamnese Inicial", "Avaliação Periódica", "Alta".
  - Admin pode duplicar e personalizar.
- **Implementação**: Seeds de `AssessmentTemplate` + procedure `assessmentTemplates.duplicate`.

---

## 9. Acessibilidade e Usabilidade

### 9.1 Navegação por Teclado
- **Objetivo**: Acessibilidade e produtividade.
- **Funcionalidade**:
  - Tab navigation em todos os formulários.
  - Foco visível (outline azul).
  - Skip links para conteúdo principal.
- **Implementação**: Atributos `tabindex`, `aria-*`, CSS `:focus-visible`.

### 9.2 Tooltips Contextuais
- **Objetivo**: Ajuda sem poluir interface.
- **Funcionalidade**:
  - Tooltip em ícones sem label.
  - Explicações de campos complexos.
  - Dicas de uso (ex.: "Use Cmd+K para buscar").
- **Implementação**: shadcn/ui `Tooltip` component.

### 9.3 Modo Foco/Leitura
- **Objetivo**: Reduzir distrações ao ler prontuário.
- **Funcionalidade**:
  - Botão "Modo Leitura" oculta sidebar/header.
  - Foco no conteúdo principal.
  - Restauração com tecla `Esc`.
- **Implementação**: Estado global `readingMode` + CSS `display: none`.

---

## 10. Performance e Otimizações

### 10.1 Lazy Loading
- **Objetivo**: Carregar apenas o necessário.
- **Funcionalidade**:
  - Componentes pesados carregados sob demanda (ex.: gráficos, tabelas grandes).
  - Imagens com `loading="lazy"`.
  - Code splitting automático (Next.js).
- **Implementação**: `React.lazy` + `Suspense` + Next.js dynamic imports.

### 10.2 Cache Inteligente
- **Objetivo**: Reduzir requisições desnecessárias.
- **Funcionalidade**:
  - Cache de dados estáticos (unidades, profissionais) por 5 minutos.
  - Invalidação automática ao atualizar.
  - Stale-while-revalidate para dados semi-estáticos.
- **Implementação**: TanStack Query com `staleTime` e `cacheTime`.

### 10.3 Paginação Eficiente
- **Objetivo**: Carregar listas grandes sem travar.
- **Funcionalidade**:
  - Paginação server-side (25 itens por página).
  - Infinite scroll opcional (futuro).
  - Virtual scrolling para tabelas muito grandes.
- **Implementação**: TanStack Table com paginação + tRPC `limit`/`offset`.

---

## 11. Implementação Técnica

### 11.1 Hooks Customizados
```typescript
// useAutosave.ts
export function useAutosave(data: any, procedure: string) {
  // Auto-save a cada 30s
}

// useKeyboardShortcuts.ts
export function useKeyboardShortcuts() {
  // Atalhos globais
}

// usePersistedFilters.ts
export function usePersistedFilters(key: string) {
  // Salva/restaura filtros
}
```

### 11.2 Procedures tRPC
```typescript
// search.global
export const searchRouter = router({
  global: publicProcedure
    .input(z.object({ query: z.string() }))
    .query(async ({ input }) => {
      // Busca unificada
    }),
});

// appointments.suggestTimes
export const appointmentsRouter = router({
  suggestTimes: protectedProcedure
    .input(z.object({ professionalId: z.string(), date: z.date() }))
    .query(async ({ input }) => {
      // Sugere horários livres
    }),
});
```

### 11.3 Componentes shadcn/ui
- `Command` (command palette)
- `Dialog` (modais)
- `Toast` (notificações)
- `Tooltip` (dicas)
- `Skeleton` (loading)
- `Badge` (contadores)

---

## 12. Roadmap de Facilitações

### Fase 1 (MVP)
- ✅ Auto-save básico
- ✅ Busca global simples
- ✅ Atalhos básicos (Cmd+K, Esc)
- ✅ Toasts de feedback

### Fase 2 (Melhorias)
- ⏳ Templates de evoluções
- ⏳ Sugestões de horários
- ⏳ Filtros salvos
- ⏳ Drag & drop na agenda

### Fase 3 (Avançado)
- ⏳ Dashboards personalizáveis
- ⏳ Busca fuzzy
- ⏳ Modo leitura
- ⏳ Infinite scroll

---

> **Nota**: Todas as facilitações devem manter a simplicidade e não sobrecarregar a interface. Priorizar o que realmente acelera o trabalho do usuário.

