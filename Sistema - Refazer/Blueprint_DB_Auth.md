# Blueprint de Banco de Dados & Autenticação – Equidade+ Nova Stack

Documento para detalhar a arquitetura do banco de dados (PostgreSQL) e o fluxo completo de autenticação/autorização, considerando a operação na Railway.

---

## 1. Banco de Dados (PostgreSQL)

### 1.1 Ferramentas e Configuração
- **Sistema gerenciador**: PostgreSQL 16 (Railway).
- **ORM**: Prisma ORM (schema versionado).
- **Migrations**: `prisma migrate` (deploy automatizado via CI/CD).
- **Seeds**: scripts `prisma db seed` para criar roles, usuário admin, dados demo.
- **Backups**: `pg_dump` diário (BullMQ job) enviado para bucket S3; retenção 30 dias.
- **Monitoramento**: métricas Railway (CPU, conexões), logs SQL habilitáveis se necessário.
- **Conexões**:
  - API principal (`equidade-api`): connection pooling builtin (Prisma).
  - Worker (`equidade-worker`): reusa Prisma/URL com max 3 conexões.
  - Limitar pool total para evitar saturar (ex.: `connection_limit=15`).

### 1.2 Estratégia de Schema
- Schema único `public`.
- Prefira `uuid` como chave primária (`@default(uuid())` no Prisma).
- Campos `created_at` e `updated_at` com `@default(now())` e `@updatedAt`.
- Campos `unit_id` em quase todas as tabelas para escopo multiunidade.
- Armazenar textos ricos em `jsonb` (evoluções, avaliações) para flexibilidade.
- Índices conforme `Modelo_Dados_ER.md`.

### 1.3 Migrations & Versionamento
- `prisma/migrations/<timestamp_name>/migration.sql`.
- Fluxo:
  1. Dev cria migration com `pnpm prisma migrate dev`.
  2. Commit no repositório.
  3. CI roda `prisma migrate deploy` em staging.
  4. Em produção, Railway executa `prisma migrate deploy` antes de subir nova versão.
- Em caso de rollback: utilizar backup + `prisma migrate resolve` para ajustar estado.

### 1.4 Dados Sensíveis
- Campos sensíveis (ex.: `patients.medications`) ficam no banco, mas acesso controlado via policies.
- Logs de auditoria armazenam apenas referência (sem conteúdo clínico completo).
- Possibilidade futura: criptografia por coluna (PGP Symmetric) caso necessário.

### 1.5 Estratégia para Ambientes
- `DATABASE_URL` diferente por ambiente.
- Seeds:
  - Local: dados fictícios completos (`seed-demo.ts`).
  - Staging: dados mascarados (`seed-staging.ts`).
  - Produção: apenas seeds essenciais (roles/admin).
- Migração legada: via scripts descritos em `Plano_Dados_e_Migracao.md`.

### 1.6 Observabilidade
- Configurar `pg_stat_statements` para analisar queries pesadas (Railway suporta via extensão).
- Monitorar conexão ativa (`SELECT count(*) FROM pg_stat_activity`).
- Alarmes: latência > 500ms, falha em backup, crescimento anormal de storage.

---

## 2. Autenticação & Autorização

### 2.1 Objetivos
- Login com email/senha (hash Argon2id).
- Sessões stateless (JWT) com refresh token rotativo.
- Escopo por unidade (multi-tenant) + roles (RBAC).
- Suporte para troca de senha, recuperação, logs de login.
- Preparado para MFA futura (ver ADR futuro).

### 2.2 Tokens
- **Access Token**
  - JWT assinada com chave privada (RS256 ou EdDSA).
  - Contém `sub` (user id), `role`, `permissions`, `unitId`.
  - TTL 15 minutos.
- **Refresh Token**
  - JWT/HMAC armazenada em cookie HttpOnly (`refresh_token`).
  - TTL 7 dias, rotacionada a cada refresh (invalidate previous).
  - Armazenar hash em tabela `refresh_tokens` (opcional) para invalidar.
- **Cookies**
  - `access_token` (opcional; pode ser armazenado em header).
  - `refresh_token` (HttpOnly, SameSite=Lax, Secure em produção).
  - `unit_id` (separado para persistir unidade ativa).

### 2.3 Fluxo de Login
1. Usuário envia `POST /api/v1/auth/login` com email e senha.
2. API valida credenciais (`AuthService`):
   - Busca usuário por email.
   - Compara hash Argon2id.
   - Verifica se usuário ativo.
3. Gera access token + refresh token.
4. Retorna dados do usuário + cookie refresh.
5. Front seleciona unidade (se >1) e salva via `user_preferences`.
6. Middleware aplica `unitId` a cada requisição subsequente.

### 2.4 Refresh Token
1. Front chama `POST /api/v1/auth/refresh`.
2. API valida refresh (JWT verificada + checa blacklist se existir).
3. Gera novo par access/refresh, invalida token anterior.
4. Atualiza cookie.

### 2.5 Logout
- `POST /api/v1/auth/logout`: remove refresh token (cookie + blacklist) e registra log.

### 2.6 Recuperação de Senha
1. `POST /api/v1/auth/forgot-password` com email.
2. Gera token (UUID) armazenado em `password_reset_tokens` com expiração 30 min.
3. Envia email via SMTP com link (`/reset-password?token=...`).
4. `POST /api/v1/auth/reset-password` valida token + atualiza hash.
5. Invalida refresh tokens anteriores.

### 2.7 Autorização (RBAC + Policies)
- **Roles**: `admin`, `coordenador`, `profissional`, `secretaria`.
- **Permissions**: granular via enum (ex.: `view_reports`, `manage_users`).
- **Guards**:
  - `JwtAuthGuard`: tokens válidos.
  - `RolesGuard`: verifica role/permissões.
  - `UnitScopeGuard`: injeta `unitId` e bloqueia acesso fora da unidade.
- **Policies**:
  - Implementadas no service (ex.: `PatientsService.canView(patient, user)`).
  - Decorators `@AllowRole('admin')`, `@AllowPolicy('patient:view')` para clareza.
- **Front**:
  - `useSession()` retorna roles/permissões.
  - Componentes e rotas usam `hasPermission('view_reports')`.

### 2.8 Logs de Login & Segurança
- Tabela `login_logs` com `user_id`, `email`, `ip`, `user_agent`, `result`.
- Rate limit login (5 tentativas/min) e forgot-password (3/h).
- Bloquear conta após X falhas consecutivas (ex.: 10) até ação manual do admin.
- Notificações de login em novo dispositivo (futuro).

### 2.9 Multi Fator (Futuro)
- ADR futura para MFA (TOTP ou email OTP).
- Estrutura atual já permite armazenar `mfa_enabled` e `mfa_secret`.

---

## 3. Integração com Railway

### 3.1 Deploy da API
- Railway service `equidade-api`:
  - Build: `pnpm install --frozen-lockfile && pnpm turbo run build --filter=api...`
  - Start: `pnpm turbo run start --filter=api`
- Variáveis definidas no serviço (ver `Guia_Ambientes_Variaveis.md`).
- `railway up` dentro do pipeline `deploy-prod`.

### 3.2 Provisionamento do DB
- Railway cria Postgres gerenciado.
- Configurar plan com storage adequado (estimado: 5-10GB inicialmente).
- Habilitar backups automáticos Railway + backups custom (S3).
- Documentar credenciais em local seguro (1Password/Vault).

### 3.3 Segurança
- Whitelisting IPs (quando disponível).
- Uso de `DATABASE_URL` com SSL forçado (`?sslmode=require`).
- Rotação periódica de senhas e chaves JWT.

---

## 4. Checklists

### 4.1 Checklist de Banco
- [ ] Schema Prisma atualizado com entidades e índices.
- [ ] Migrations versionadas e testadas (local + CI).
- [ ] Seeds criam roles/admin.
- [ ] Scripts de migração legada prontos.
- [ ] Backup job configurado e testado (staging).
- [ ] Monitoramento Railway com alertas.

### 4.2 Checklist de Autenticação
- [ ] Hash Argon2id aplicado em todas senhas (rota de reset).
- [ ] Tokens assinados com chaves seguras (não expostas).
- [ ] Cookies HttpOnly + Secure em prod.
- [ ] Unit scope garantido em cada requisição.
- [ ] Log de login e rate limit ativos.
- [ ] Recuperação de senha testada fim a fim.
- [ ] Documentação atualizada (Swagger).

---

> Este blueprint complementa `Modelo_Dados_ER.md`, `Modulos_Arquitetura_Detalhada.md` e `Guia_Ambientes_Variaveis.md`. Atualize quando novos requisitos de segurança ou dados surgirem.

