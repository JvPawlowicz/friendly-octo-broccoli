# Setup Técnico – Monorepo & Ambientes Railway

## 1. Estrutura do Repositório
```
equidade-plus/
├── apps/
│   ├── api/          # NestJS (REST + BullMQ)
│   ├── web/          # Next.js 15 (App Router)
│   └── admin/        # Next.js + Refine (painel)
├── packages/
│   ├── ui/           # Componentes React compartilhados
│   ├── config/       # ESLint, TS config, Tailwind preset
│   └── schemas/      # Zod + tipos compartilhados (DTOs)
├── scripts/          # export/import, utilidades CI/CD
├── docs/             # documentação gerada (link com Pasta Sistema - Refazer)
├── turbo.json        # configuração Turborepo
├── pnpm-workspace.yaml
└── package.json      # dependências raiz (pnpm)
```

## 2. Ferramentas e Dependências
- **Gerenciador**: `pnpm`.
- **Lint/Format**: ESLint, Prettier, TypeScript, `@trivago/prettier-plugin-sort-imports`.
- **Commit hooks**: Husky + lint-staged.
- **Testes**: Jest (API), Playwright (web/admin), Vitest opcional para hooks/utilitários frontend.
- **CI**: GitHub Actions com caches (pnpm, turbo).
- **Env**: `dotenv-flow` para gerenciar `.env.{environment}`.

## 3. Scripts PNPM (raiz)
| Script | Descrição |
| --- | --- |
| `pnpm install` | instala dependências com workspace. |
| `pnpm dev` | roda `turbo run dev --parallel --filter=api web admin`. |
| `pnpm build` | build dos três apps + pacotes. |
| `pnpm lint` | executa lint em todos os pacotes. |
| `pnpm test` | roda testes unitários (api + web). |
| `pnpm test:e2e` | executa Playwright. |
| `pnpm format` | `prettier --write`. |

## 4. Configuração NestJS (`apps/api`)
- CLI Nest, módulos por domínio (`src/modules/...`).
- `PrismaService` para DI + transactions.
- Config module lendo env (Railway) com validação via Zod.
- `main.ts`: habilitar CORS restrito, cookies, versionamento `/api/v1`.
- `bull.config.ts`: fila Redis (Railway) + processor (PDF, backups).
- Swagger em `/api/docs` (apenas em ambientes não-prod ou protegido via auth).

## 5. Configuração Next.js (`apps/web` e `apps/admin`)
- App Router, Route Handlers (`app/api/*`) para proxy se necessário.
- Middleware para verificar JWT -> redirecionar login.
- Layouts persistentes (sidebar, header).
- `lib/api-client.ts`: fetch wrapper com cookies.
- `lib/auth.ts`: helpers (getSession, requireRole).
- `admin`: provider Refine configurado com `dataProvider` NestJS.

## 6. Infraestrutura Railway
### 6.1 Serviços
| Serviço | Repositório/branch | Build command | Start command |
| --- | --- | --- | --- |
| `equidade-api` | apps/api | `pnpm install && pnpm build --filter api` | `pnpm start --filter api` |
| `equidade-web` | apps/web | `pnpm install && pnpm build --filter web` | `pnpm start --filter web` |
| `equidade-admin` | apps/admin | `pnpm install && pnpm build --filter admin` | `pnpm start --filter admin` |
| `equidade-worker` | apps/api | `pnpm install && pnpm build --filter api` | `pnpm start:worker --filter api` |
| `equidade-postgres` | - | - | gerenciado Railway |
| `equidade-redis` | - | - | gerenciado Railway |

> Dica: utilizar Nixpacks (Railway) com comando custom: `pnpm install --frozen-lockfile && pnpm turbo run build --filter=<app>...`.

### 6.2 Variáveis de Ambiente (comuns)
- `APP_ENV`, `APP_URL`, `APP_NAME`.
- `DATABASE_URL` (Postgres).
- `REDIS_URL`.
- `JWT_PRIVATE_KEY`, `JWT_PUBLIC_KEY`.
- `REFRESH_TOKEN_SECRET`.
- `S3_ENDPOINT`, `S3_BUCKET`, `S3_ACCESS_KEY`, `S3_SECRET_KEY`, `S3_REGION`.
- `SMTP_HOST`, `SMTP_USER`, `SMTP_PASS` (notificações email).
- `CHAT_POLL_INTERVAL`, `AGENDA_POLL_INTERVAL`.
- `DEFAULT_APPOINTMENT_DURATION`.
- `PDF_BUCKET_PREFIX`.

### 6.3 Ambientes
- **Development**
  - Railway local (`railway run pnpm dev`) opcional.
  - Banco local (Docker compose Postgres + Redis).
  - Seeds demo.
- **Staging**
  - Branch `develop`.
  - Dados mascarados (anonimização parcial).
  - Acesso restrito (VPN/IP allowlist).
- **Production**
  - Branch `main` (ou tag `v*`).
  - Backups automáticos habilitados (Railway + scripts custom).
  - Observabilidade completa.

## 7. CI/CD (GitHub Actions)
- **Workflow `ci.yml`**
  1. Checkout.
  2. Setup pnpm + Node.
  3. Cache pnpm/turbo.
  4. `pnpm install --frozen-lockfile`.
  5. `pnpm lint`, `pnpm test`, `pnpm build`.
  6. Upload artifacts (coverage, Playwright).
- **Workflow `deploy.yml`**
  - Trigger em push para `develop`/`main` ou tags.
  - Usa Railway CLI (`railway up --service <name>`).
  - Executa migrations: `railway run pnpm prisma migrate deploy`.

## 8. Monitoramento
- Configurar Sentry (front/back) com DSN via secrets.
- Railway Metrics: monitor CPU, memória, restarts.
- Alertas (Slack/email) para thresholds (erros > 1%, latência > 1s).
- Health endpoint `/health` integrado ao Railway Health Checks.

## 9. Processos Operacionais
- **Migrations**: sempre versionadas; rodar `pnpm prisma migrate deploy` antes de subir nova versão.
- **Seeds**: script `pnpm prisma db seed` (cria roles/admin). Seeds demo somente em `dev/staging`.
- **Backups**:
  - Banco: `pg_dump` diário via BullMQ job.
  - Arquivos: replicação incremental S3 + index no banco.
- **Rotinas**: `@nestjs/schedule` para tarefas (backup, lembretes, limpeza exportações).

## 10. Checklist de Setup Inicial
1. Criar repositório Git, configurar `main`/`develop`.
2. Adicionar Turborepo + pnpm workspace.
3. Configurar Husky (`prepare` script, pre-commit).
4. Subir containers locais (docker compose).
5. Inicializar Prisma (`prisma init --datasource-provider postgresql`).
6. Criar projeto Railway e serviços (api, web, admin, worker, postgres, redis).
7. Configurar envs e secrets.
8. Integrar GitHub → Railway.
9. Criar workflows CI/CD.
10. Documentar no `README` como iniciar o projeto.

## 11. Acesso & Segurança
- Utilizar `railway team` para restringir acesso aos serviços.
- Habilitar 2FA para membros da equipe.
- Segredos sensíveis apenas em Railway (não commitar `.env`).
- Rotacionar chaves JWT a cada 6 meses.

## 12. Pendências
- Definir ferramenta de logs centralizados (Axiom, Logtail ou Datadog).
- Avaliar uso de Feature Flags (ex.: LaunchDarkly ou solução caseira).
- Criar playbook de incidentes (gestão de falhas).

