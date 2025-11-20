# Guia de Desenvolvimento Frontend – Equidade+ Nova Stack

## 1. Objetivos
Estabelecer convenções e estruturas para o trabalho em `apps/web` (Next.js) e `apps/admin` (Next.js + Refine), garantindo consistência entre componentes, hooks e integração com a API NestJS.

---

## 2. Stack e Ferramentas
- Next.js 15 (App Router, Server Actions, React Server Components).
- TypeScript estrito (`"strict": true`).
- TailwindCSS + Radix UI + design tokens customizados (`packages/ui`).
- TanStack Query (mutations client-side), React Hook Form (quando necessário).
- Zod para validação (schemas compartilhados em `packages/schemas`).
- Storybook opcional em `packages/ui` para visualização de componentes.

---

## 3. Estrutura de Pastas (apps/web)
```
app/
  (auth)/
    login/
      page.tsx
      actions.ts
  (protected)/
    layout.tsx        # layout com Sidebar/Header
    dashboard/
      page.tsx
      components/
      hooks/
      actions.ts
    agenda/
      page.tsx
      components/
      hooks/
      actions.ts
    ...
  api/
    auth/
      login/route.ts  # proxies para NestJS quando necessário
  global-error.tsx
components/
  ui/                # wrappers tailwind + radix
  domain/            # componentes de domínio (AgendaCard, PatientCard)
hooks/
lib/
  api-client.ts      # fetch wrapper
  auth.ts
  formatters.ts
styles/
```

### Pastas por tela
- `components/`: componentes específicos da página (organismos, blocos).
- `hooks/`: hooks locais (`useAgendaFilters`, `useDashboardData`).
- `actions.ts`: server actions (create/update) invocando API Nest.
- `types.ts`: tipos específicos (derivados de schemas Zod).

---

## 4. Convenções de Componentes
- **Componentes UI (packages/ui)**:
  - Exportados com prefixo `Ui` ou nome funcional (ex.: `Button`, `Modal`).
  - Sem lógica de domínio; apenas estilização e comportamento básico.
- **Componentes de domínio**:
  - No app web (ex.: `AgendaCalendar`, `PatientTimeline`).
  - Devem receber dados já formatados sempre que possível.
  - Separar em `Presentation` (UI) e `Container` (quando necessário).
- **Estado**:
  - Preferir Server Components para buscar dados (SSR).
  - Client Components quando precisar de interatividade (drag & drop, forms).
  - Evitar `useState` excessivo; usar TanStack Query para dados client-side.

---

## 5. Server Actions vs Fetch
- **Quando usar Server Actions**:
  - Mutações simples que podem rodar no server (ex.: `createAppointmentAction`).
  - Permite revalidar cache via `revalidatePath`.
- **Quando usar API client (fetch/TanStack Query)**:
  - Operações que precisam de feedback imediato/sincronização no cliente (ex.: chat, agenda drag & drop).
  - Consulta em loops/polling (usar fetch com `useEffect` ou TanStack Query).

---

## 6. Hooks Compartilhados
- `useSession`: retorna usuário atual, unidade ativa, permissões.
- `useCommandPalette`: gestiona state do `cmd+k`.
- `usePolling(interval, callback)`: wrapper para intervalos controlados.
- `useExportJob`: status de exportações (polling do job).
- `useFormToast`: helper para feedback pós submit.

---

## 7. Integração com API
- Usar `fetch` com `next/headers` para incluir cookies automaticamente.
- Criar wrapper (`lib/api-client.ts`):
  - `apiClient.get('/api/v1/...')`.
  - Lidar com erros (401 → redirect login, 403 → toast).
- Validar responses com Zod para garantir tipos corretos.

---

## 8. Gestão de Estado e Cache
- TanStack Query:
  - Ex.: `const { data } = useQuery(['appointments', filters], fetchAppointments)`
  - Invalidar cache após server action (usando `queryClient.invalidateQueries` via context).
- Persistência de filtros (agenda, relatórios):
  - Salvar em `localStorage` + `user_preferences` quando aplicável.

---

## 9. Testes Frontend
- **Unitários**: Vitest/Testing Library para componentes críticos.
- **E2E**: Playwright (fluxos descritos em `Plano_Testes.md`).
- Snapshot Storybook opcional para componentes base.
- Regras:
  - Cada nova tela deve incluir pelo menos 1 teste e2e principal.
  - Hooks com lógica devem ter testes unitários.

---

## 10. Acessibilidade
- Aplicar padrões WCAG 2.2:
  - Semântica correta (`<button>`, `<label>` etc).
  - Aria-label/aria-describedby conforme necessário.
  - Navegação por teclado (tabindex).
  - Alto contraste (classes tailwind custom).
- Rodar `@axe-core/playwright` nos principais fluxos antes dos releases.

---

## 11. Internacionalização
- Iniciar com `pt-BR`, mas estruturar para fácil adição de idiomas:
  - Criar `packages/ui/strings.ts` ou usar `next-intl`.
  - Centralizar textos em objetos (nenhum texto hardcoded nos componentes).
  - Mensagens de validação via `zod` com dicionário.

---

## 12. Painel Admin (Refine)
- Estrutura:
  ```
  apps/admin/
    app/
      (refine)/
        layout.tsx
        users/
          list.tsx
          edit.tsx
        units/
        templates/
    providers/
      dataProvider.ts
      authProvider.ts
  ```
- Usar dataProvider custom para endpoints Nest.
- Autorização: `authProvider` verifica tokens; `accessControlProvider` integra permissões.
- Componentes Ant Design customizados com tokens do design system.

---

## 13. Estilo e Theming
- Tailwind config em `packages/config/tailwind-preset.js`.
- Usar CSS variables para cores (permite switch de tema).
- Dark mode: `data-theme="dark"` na tag `<html>`.
- Reutilizar tokens (spacing, font sizes) definidos no design system.

---

## 14. Deploy e Build
- `pnpm build --filter web` → gera `.next`.
- `pnpm start --filter web` (Next em modo standalone).
- Web e admin empacotados com Nixpacks (Railway):
  - Definir `NIXPACKS_NODE_VERSION=20`.
  - Habilitar `NEXT_TELEMETRY_DISABLED=1`.
- Configurar `next.config.js` para imagens externas (se houver).

---

## 15. Checklist de Implementação de Tela
1. Criar diretório em `app/(protected)/<tela>`.
2. Montar `page.tsx` (SSR/Server Component) consumindo dados.
3. Extrair componentes para `components` (client).
4. Criar hooks/actions conforme necessidade.
5. Validar dados com schemas Zod.
6. Escrever teste Playwright (cenário principal).
7. Verificar acessibilidade (labels, foco).
8. Atualizar documentação (mapa UX, módulos) se necessário.

---

> Mantenha este guia atualizado conforme novas práticas surgirem. Tudo que for padrão deve estar documentado aqui para facilitar onboarding e consistência.***

