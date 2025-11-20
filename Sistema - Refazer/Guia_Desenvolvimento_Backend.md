# Guia de Desenvolvimento Backend – Equidade+ Nova Stack

## 1. Objetivo
Padronizar a estrutura do tRPC (packages/api), estabelecer convenções de código, testes e integração com serviços externos (Postgres, Railway cron).

---

## 2. Stack
- tRPC (TypeScript RPC).
- Next.js API routes (para cron jobs e webhooks).
- Prisma ORM (PostgreSQL).
- Zod para validação e schemas.
- Railway cron para jobs agendados.

---

## 3. Estrutura de Pastas
```
packages/api/
  src/
    routers/
      auth.ts
      users.ts
      units.ts
      appointments.ts
      evolutions.ts
      assessments.ts
      patients.ts
      reports.ts
      notifications.ts
      settings.ts
      audit.ts
      backups.ts
    services/
      AuthService.ts
      UsersService.ts
      AppointmentsService.ts
      EvolutionsService.ts
      ...
    middleware/
      auth.ts
      role.ts
      unit.ts
    utils/
      prisma.ts
      errors.ts
    index.ts (appRouter)
  package.json
```

---

## 4. Routers tRPC e Responsabilidades
- Cada router deve conter:
  - Procedures (queries e mutations).
  - Validação de input com Zod.
  - Middleware de autenticação/autorização.
  - Chamadas a services para lógica de negócio.
- Exemplo de router:
  ```typescript
  import { router, protectedProcedure } from '../trpc';
  import { z } from 'zod';
  import { appointmentsService } from '../services/AppointmentsService';

  export const appointmentsRouter = router({
    list: protectedProcedure
      .input(z.object({
        unitId: z.string(),
        from: z.date().optional(),
        to: z.date().optional(),
      }))
      .query(async ({ input, ctx }) => {
        return appointmentsService.list(input, ctx.user);
      }),

    create: protectedProcedure
      .input(z.object({
        patientId: z.string(),
        professionalId: z.string(),
        startAt: z.date(),
        endAt: z.date(),
      }))
      .mutation(async ({ input, ctx }) => {
        return appointmentsService.create(input, ctx.user);
      }),
  });
  ```

---

## 5. Configuração e Env
- Validar variáveis de ambiente com Zod:
  ```typescript
  import { z } from 'zod';

  export const envSchema = z.object({
    NODE_ENV: z.enum(['development', 'production', 'test']),
    DATABASE_URL: z.string().url(),
    JWT_SECRET: z.string(),
    JWT_REFRESH_SECRET: z.string(),
    S3_ENDPOINT: z.string().url().optional(),
    S3_ACCESS_KEY: z.string().optional(),
    S3_SECRET_KEY: z.string().optional(),
  });

  export const env = envSchema.parse(process.env);
  ```
- Usar `env` em todo o código (nunca `process.env` diretamente).

---

## 6. Prisma
- Schema em `prisma/schema.prisma`.
- Scripts:
  - `pnpm prisma migrate dev`.
  - `pnpm prisma migrate deploy`.
  - `pnpm prisma db seed`.
- Instância única do Prisma Client:
  ```typescript
  import { PrismaClient } from '@prisma/client';

  const prisma = new PrismaClient();
  export default prisma;
  ```
- Transações: usar `prisma.$transaction`.
- Indexes definidos no schema (ver blueprint).

---

## 7. Validação com Zod
- Schemas compartilhados em `packages/schemas`:
  ```typescript
  export const CreatePatientSchema = z.object({
    name: z.string().min(1),
    birthdate: z.date().optional(),
    cpf: z.string().regex(/^\d{11}$/).optional(),
  });

  export type CreatePatientInput = z.infer<typeof CreatePatientSchema>;
  ```
- Procedures usam `.input()` com schema Zod:
  ```typescript
  create: protectedProcedure
    .input(CreatePatientSchema)
    .mutation(async ({ input, ctx }) => {
      return patientsService.create(input, ctx.user);
    }),
  ```

---

## 8. Autorização e Escopo
- Middleware tRPC:
  ```typescript
  export const protectedProcedure = publicProcedure.use(authMiddleware);
  export const adminProcedure = protectedProcedure.use(roleMiddleware('admin'));
  export const unitScopedProcedure = protectedProcedure.use(unitMiddleware);
  ```
- Policies em services:
  ```typescript
  export class PatientsService {
    async findOne(id: string, user: User) {
      const patient = await prisma.patient.findUnique({ where: { id } });
      if (!patient) throw new TRPCError({ code: 'NOT_FOUND' });
      
      // Verificar permissão
      if (patient.unitId !== user.unitId && user.role !== 'admin') {
        throw new TRPCError({ code: 'FORBIDDEN' });
      }
      
      return patient;
    }
  }
  ```

---

## 9. Eventos
- EventEmitter simples (Node.js nativo ou `eventemitter3`):
  ```typescript
  import { EventEmitter } from 'events';

  export const eventEmitter = new EventEmitter();

  // Em service
  eventEmitter.emit('appointment.completed', { appointmentId, patientId });

  // Listener (em outro lugar)
  eventEmitter.on('appointment.completed', async (data) => {
    await evolutionsService.createPending(data);
  });
  ```
- Ou usar Prisma triggers (PostgreSQL) para eventos de banco.

---

## 10. Logging e Observabilidade
- Usar `pino` para logs estruturados:
  ```typescript
  import pino from 'pino';

  export const logger = pino({
    level: process.env.LOG_LEVEL || 'info',
  });

  logger.info({ appointmentId }, 'Appointment created');
  ```
- Integrar com Sentry (SDK Node) para exceções não tratadas.
- Correlation ID via middleware tRPC.

---

## 11. Tratamento de Erros
- Usar `TRPCError`:
  ```typescript
  import { TRPCError } from '@trpc/server';

  throw new TRPCError({
    code: 'NOT_FOUND',
    message: 'Patient not found',
  });
  ```
- Códigos disponíveis: `BAD_REQUEST`, `UNAUTHORIZED`, `FORBIDDEN`, `NOT_FOUND`, `CONFLICT`, `INTERNAL_SERVER_ERROR`.
- Mapear erros do Prisma:
  ```typescript
  try {
    await prisma.patient.create({ data });
  } catch (error) {
    if (error.code === 'P2002') {
      throw new TRPCError({
        code: 'CONFLICT',
        message: 'Patient already exists',
      });
    }
    throw error;
  }
  ```

---

## 12. Testes
- **Unitários**: Vitest (ou Jest).
  - Criar testes para services (mockar Prisma).
  - Testar routers isoladamente.
- **Integração**: usar `@trpc/server` com `supertest`.
  - Rodar Postgres em container (test env).
  - Criar fixtures/seed mínimas.
- **Cobertura**: alvo 80% (services, routers).

---

## 13. Cron Jobs (Railway)
- Criar API route em Next.js:
  ```typescript
  // app/api/cron/backup/route.ts
  import { NextRequest, NextResponse } from 'next/server';

  export async function GET(request: NextRequest) {
    const authHeader = request.headers.get('authorization');
    if (authHeader !== `Bearer ${process.env.CRON_SECRET}`) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    await backupsService.run();
    return NextResponse.json({ success: true });
  }
  ```
- Configurar Railway cron para chamar esta rota.

---

## 14. Scripts Úteis
- `pnpm dev --filter api` – ambiente dev com watch.
- `pnpm test --filter api`.
- `pnpm test:watch --filter api`.
- `pnpm lint --filter api`.
- `pnpm format --filter api`.

---

## 15. Boas Práticas
- Services pequenos, reutilizáveis (métodos <50 linhas).
- Não acessar Prisma direto em routers (sempre via service).
- Reutilizar schemas Zod no front (via `packages/schemas`).
- Documentar decisões em ADR (ver diretório correspondente).
- Type-safety: usar `z.infer` para tipos derivados de schemas.

---

## 16. Checklist de Feature Backend
1. Criar schema/migration (Prisma) → rodar `prisma migrate dev`.
2. Implementar service + router + schemas Zod.
3. Escrever testes unitários/integrados.
4. Conectar com eventos se aplicável.
5. Atualizar `Modulos_Componentes_Rotas.md` e plano de testes.
6. Rodar lint/testes antes do PR.

---

> Mantenha este guia atualizado. Sempre que surgir padrão novo (ex.: política de erro, integração externa), documente aqui.
