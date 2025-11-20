# Guia de Ambientes & Variáveis – Equidade+ Nova Stack

## 1. Ambientes
| Ambiente | Descrição | Branch | Banco | Observações |
| --- | --- | --- | --- | --- |
| Local | Desenvolvimento individual | n/a | Docker Postgres (local) | Usa `.env.local`, seeds completos, dados fictícios. |
| Development (Railway) | Ambiente compartilhado interno | `feature/*` (deploy manual) | Postgres Railway | Usado para QA rápido, permissões restritas. |
| Staging | Pré-produção | `develop` | Postgres Railway dedicado | Dados mascarados, espelha configuração de produção. |
| Production | Produção final | `main` ou tags `v*` | Postgres Railway prod | Backup diário, monitoramento ativo. |

---

## 2. Estrutura de Arquivos .env
- `apps/api/.env.example`.
- `apps/web/.env.example`.
- `apps/admin/.env.example`.
- `packages/*` herdam variáveis quando necessário.

Usar `dotenv-flow` para sobrepor (`.env`, `.env.local`, `.env.production`).

---

## 3. Variáveis Comuns
| Variável | Descrição |
| --- | --- |
| `APP_NAME` | Nome exibido no sistema. |
| `APP_ENV` | `development` / `staging` / `production`. |
| `APP_URL` | URL pública do frontend. |
| `API_URL` | URL pública da API NestJS. |
| `ADMIN_URL` | URL do painel admin. |

---

## 4. Backend (apps/api)
| Variável | Exemplo | Anotações |
| --- | --- | --- |
| `PORT` | `3000` | Porta da API. |
| `DATABASE_URL` | `postgresql://user:pass@host:port/db?schema=public` | Connection string Prisma. |
| `REDIS_URL` | `redis://default:password@host:port` | BullMQ. |
| `JWT_PRIVATE_KEY` | `-----BEGIN PRIVATE KEY-----...` | RSA ou EdDSA. |
| `JWT_PUBLIC_KEY` | `-----BEGIN PUBLIC KEY-----...` | |
| `REFRESH_TOKEN_SECRET` | `string longa` | Para refresh tokens (HMAC). |
| `ACCESS_TOKEN_TTL` | `900` | Segundos (15min). |
| `REFRESH_TOKEN_TTL` | `604800` | Segundos (7d). |
| `S3_ENDPOINT` | `https://s3.wasabisys.com` | |
| `S3_REGION` | `us-east-1` | |
| `S3_BUCKET` | `equidade-plus-prod` | |
| `S3_ACCESS_KEY` | `...` | |
| `S3_SECRET_KEY` | `...` | |
| `SMTP_HOST` | `smtp.mailgun.org` | |
| `SMTP_PORT` | `587` | |
| `SMTP_USER` | `postmaster@...` | |
| `SMTP_PASS` | `...` | |
| `SMTP_FROM_EMAIL` | `suporte@equidade.com` | |
| `CHAT_POLL_INTERVAL` | `3000` | ms. |
| `AGENDA_POLL_INTERVAL` | `120000` | ms. |
| `DEFAULT_APPOINTMENT_DURATION` | `60` | minutos. |
| `BACKUP_CRON` | `0 3 * * *` | Horário do backup diário. |
| `EXPORT_TTL_MINUTES` | `15` | Expiração de arquivos exportados. |
| `LOG_LEVEL` | `info` | `debug`, `warn`, etc. |
| `SENTRY_DSN` | `https://...` | Opcional. |
| `OTEL_EXPORTER_OTLP_ENDPOINT` | `...` | OpenTelemetry (opcional). |

---

## 5. Frontend Web (apps/web)
| Variável | Descrição |
| --- | --- |
| `NEXT_PUBLIC_APP_NAME` | Exibido no título/SEO. |
| `NEXT_PUBLIC_API_URL` | URL da API para fetch client. |
| `NEXT_PUBLIC_SENTRY_DSN` | Monitoramento. |
| `NEXT_PUBLIC_DEFAULT_UNIT_ID` | Opcional para ambientes demo. |
| `NEXT_PUBLIC_FEATURE_FLAGS` | JSON string com flags (ex.: `{"chat":true}`). |

Observação: não expor segredos (somente variáveis públicas com prefixo `NEXT_PUBLIC_`).

---

## 6. Painel Admin (apps/admin)
| Variável | Descrição |
| --- | --- |
| `NEXT_PUBLIC_API_URL` | Endpoint da API NestJS. |
| `NEXT_PUBLIC_APP_NAME` | Nome exibido. |
| `NEXT_PUBLIC_S3_PUBLIC_URL` | (se houver exibição de arquivos públicos). |

---

## 7. Railway Configuração
- Setar variáveis por serviço (API, Web, Admin, Worker).
- Utilizar `railway variables set KEY=VALUE`.
- Separar ambientes (project `equidade-dev`, `equidade-prod`).
- Secrets sensíveis apenas na Railway (não commitar `.env`).

---

## 8. Docker Compose (ambiente local)
```
services:
  postgres:
    image: postgres:16
    environment:
      POSTGRES_USER: equidade
      POSTGRES_PASSWORD: equidade
      POSTGRES_DB: equidade_dev
    ports: ["5432:5432"]
  redis:
    image: redis:7
    ports: ["6379:6379"]
```
- Arquivo `.env.local` referencia `localhost`.
- Scripts `pnpm db:reset` para recriar banco (`prisma migrate reset`).

---

## 9. Estratégia de Segredos
- Armazenar chaves (JWT, S3, SMTP) com rotação semestral.
- Utilizar `railway variables` auditáveis.
- 2FA obrigatório no Railway/GitHub.
- Documentar processo de rotação (quem, quando, como).

---

## 10. Checklists
### Novo Ambiente
- [ ] Criar projeto Railway (api, web, admin, worker, postgres, redis).
- [ ] Configurar variáveis via CLI.
- [ ] Subir base prisma (`pnpm prisma migrate deploy`).
- [ ] Rodar seed inicial (`pnpm prisma db seed`).
- [ ] Executar smoke tests.

### Rotação de Segredos
- [ ] Gerar nova chave.
- [ ] Atualizar Railway (manter backup temporário).
- [ ] Deploy apps.
- [ ] Invalidar tokens/segredos antigos.
- [ ] Registrar na documentação (data, responsável).

---

> Mantenha este guia atualizado quando novas variáveis ou serviços forem adicionados.

