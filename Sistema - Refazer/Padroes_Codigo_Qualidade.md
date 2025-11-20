# Padrões de Código & Qualidade – Equidade+ Nova Stack

## 1. Estilo de Código
- **TypeScript**
  - `strict` habilitado em todos os projetos.
  - Usar `const` por padrão; `let` apenas quando necessário.
  - Evitar `any`; preferir tipos inferidos ou Zod inferidos.
- **Imports**
  - Ordenação automática (Prettier com `sort-imports`).
  - Agrupar por origem: libs externas, aliases (`@/`), relativos.
- **Naming**
  - Clases e componentes: `PascalCase`.
  - Funções e hooks: `camelCase`.
  - Constantes: `SCREAMING_SNAKE_CASE` quando global.
- **Arquivos**
  - Um componente principal por arquivo.
  - Tamanho máximo recomendável ~200 linhas (se maior, quebrar).
  - Testes no mesmo diretório `__tests__` ou `*.spec.ts`.

---

## 2. Linters & Formatadores
- ESLint com presets:
  - `@typescript-eslint/recommended`.
  - `eslint-config-next` (para web/admin).
  - Regras custom:
    - Proibir import relativo que sobe mais que 2 níveis.
    - Enforce `no-floating-promises` (uso de `void` se necessário).
- Prettier com regras:
  - `printWidth`: 100.
  - `singleQuote`: true.
  - `semi`: true.
  - `trailingComma`: all.
- Husky pre-commit:
  - `pnpm lint --filter ./... --fix`.
  - `pnpm format`.
  - `pnpm test --filter api -- --bail --findRelatedTests`.

---

## 3. Tratamento de Erros
- Backend:
  - Usar classes personalizadas (`DomainException`) quando necessário.
  - Retornar erros padronizados: `{ errorCode, message, details? }`.
  - Logar erros no nível adequado (`warn` para recuperáveis, `error` para críticos).
- Frontend:
  - Exibir toasts amigáveis.
  - Erros 401 → redirecionar login.
  - Erros 403 → mostrar mensagem de permissão negada.
- Documentar códigos de erro por módulo (ex.: `APP-001` conflito agenda).

---

## 4. Segurança
- Sanitizar inputs (XSS) com `dompurify` quando exibir HTML.
- Nunca logar dados sensíveis (senhas, tokens, conteúdo clínico completo).
- Implementar rate limiting e proteção contra brute force.
- Verificar permissões em todas rotas (policies + guards).

---

## 5. Reviews & Pull Requests
- Template de PR:
  ```
  ## Descrição
  - ...

  ## Checklist
  - [ ] Testes unitários
  - [ ] Testes e2e (se aplicável)
  - [ ] Documentação atualizada

  ## Screenshots / Gravação (opcional)
  ```
- Exigir 2 aprovadores para mudanças críticas (migração, segurança).
- Rodar `pnpm test:ci` antes de marcar como ready for review.

---

## 6. Observabilidade
- Logging estruturado (JSON) com campos: `timestamp`, `level`, `message`, `requestId`, `userId`.
- APM opcional (OpenTelemetry) para rotas críticas.
- Alertas Sentry:
  - Pirâmide (erro de backend -> alerta Slack/email).
  - Categorizar issues (Ex.: `API_EvolutionFinalize`).

---

## 7. Performance
- Front: lazy loading quando possível (ex.: componentes de relatório), memoização (`useMemo`, `useCallback`).
- Back: usar `select`/`include` Prisma para evitar `SELECT *`.
- Cache leve (Redis) para dashboards, listas frequentes (TTL 5 min).
- Evitar N+1 queries (usar `include`, `batching`).

---

## 8. Documentação
- Cada módulo gerado deve atualizar:
  - `Modulos_Componentes_Rotas.md`.
  - `Modulos_Arquitetura_Detalhada.md` (se alteração de fluxo).
  - ADR correspondente (se decisão nova).
- Comentários no código apenas quando necessário para explicar decisões complexas (link para ADR).

---

## 9. Acessibilidade & Internacionalização
- Sempre prever textos configuráveis (sem hardcode).
- Mensagens no backend em português, mas permitir tradução futura.
- Testar componentes com teclado.

---

## 10. Checklist de Qualidade antes de Merge
- [ ] Lint e testes passando.
- [ ] Sem regressões e2e.
- [ ] Cobertura >80% (backend).
- [ ] Resposta da API documentada (Swagger).
- [ ] Logs/erros tratados.
- [ ] Mudanças refletidas na documentação.
- [ ] Revisão de segurança (permissões, dados sensíveis).

---

> Atualizar este documento sempre que novos padrões forem acordados.

