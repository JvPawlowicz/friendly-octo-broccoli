# Feature Flags & Rollout Control – Equidade+ Nova Stack

## 1. Objetivo
- Habilitar/desabilitar funcionalidades incrementalmente.
- Permitir testes A/B ou lançamentos por unidade.
- Minimizar risco em deploys grandes.

## 2. Abordagem Inicial
- Implementar tabela `feature_flags` no Postgres:
  - `id`, `name`, `description`, `value (jsonb)`, `audience` (enum: global/unit/user), `is_enabled`, `created_at`, `updated_at`.
- Criar service `FeatureFlagService` no backend:
  - Métodos `isEnabled(name, context)` e `getValue(name, context)`.
- Cache leve (Redis) com TTL curto (5 min) para reduzir consultas.

## 3. Uso no Backend
```ts
if (await featureFlagService.isEnabled('chat.polling.v2', { unitId, userId })) {
  // logic nova
} else {
  // fallback
}
```

## 4. Uso no Frontend
- Expor flags via endpoint `/api/v1/feature-flags?scope=user`.
- Armazenar no contexto (`useFeatureFlags()`).
- Exemplo:
```tsx
const { isEnabled } = useFeatureFlags();
return isEnabled('agenda.drag-drop') ? <DragScheduler /> : <ClassicScheduler />;
```

## 5. Rollout Plans
| Flag | Descrição | Estratégia |
| --- | --- | --- |
| `agenda.drag-drop` | habilitar novo mecanismo de drag & drop | Rollout por unidade (piloto 1 unidade → expandir) |
| `chat.polling.v2` | novo polling com WebSockets | Habilitar por usuário (time interno) |
| `reports.new-filters` | filtros avançados em relatórios | Beta testers (coordenadores selecionados) |

## 6. Gestão
- Painel admin: seção “Feature Flags” com toggle e descrição.
- Registrar alterações em `audit_logs` (flag, valor, responsável).
- Documentar flags ativas no release notes.

## 7. Boas Práticas
- Evitar flags permanentes; definir data de revisão/remoção.
- Nomear flags com prefixo por módulo (`agenda.`, `chat.`, `reports.`).
- Não usar flags para lógica crítica de segurança.
- Limitar quantidade de flags simultâneas para evitar complexidade.

## 8. Desativação e Limpeza
- Após feature estável → remover flag do código e da tabela.
- Incluir processo nos post-releases (ex.: Sprint retrospective).

---

Atualize este documento quando novas flags forem criadas ou estratégia de rollout mudar.*** End Patch

