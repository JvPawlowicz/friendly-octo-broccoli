# Roles e Permissões Detalhadas – Equidade+ Nova Stack

Documento que define claramente cada role do sistema, o que cada uma pode fazer, acessar e modificar. **IMPORTANTE**: O admin tem acesso TOTAL a todas as funcionalidades, independente de unidade ou escopo.

---

## 1. Visão Geral das Roles

O sistema possui 4 roles principais:
- **Admin**: Acesso total e irrestrito a tudo
- **Coordenador**: Gerencia sua unidade (ou unidades atribuídas)
- **Profissional**: Atende pacientes e preenche evoluções/avaliações
- **Secretária**: Gerencia agenda e cadastros básicos

---

## 2. Admin (Super Usuário)

### 2.1 Acesso Total
**O admin tem acesso a TODAS as funcionalidades do sistema, sem restrições.**

### 2.2 Funcionalidades Específicas

#### Dashboard
- ✅ Visualiza dashboard global (todas as unidades)
- ✅ Pode filtrar por qualquer unidade
- ✅ Vê métricas consolidadas de todas as unidades
- ✅ Acessa relatórios de todas as unidades

#### Agenda
- ✅ Visualiza agenda de TODAS as unidades
- ✅ Pode criar/editar/cancelar agendamentos em qualquer unidade
- ✅ Pode ver todos os profissionais e salas de todas as unidades
- ✅ Pode mover agendamentos entre unidades (se necessário)
- ✅ Não tem restrições de horário (pode agendar no passado se necessário)

#### Pacientes
- ✅ Visualiza pacientes de TODAS as unidades
- ✅ Pode criar/editar pacientes em qualquer unidade
- ✅ Pode ver prontuário completo de qualquer paciente
- ✅ Pode fazer upload de documentos para qualquer paciente
- ✅ Pode ver timeline completa de qualquer paciente

#### Evoluções
- ✅ Visualiza evoluções de TODAS as unidades
- ✅ Pode criar/editar/finalizar evoluções de qualquer profissional
- ✅ Pode revisar evoluções de qualquer unidade
- ✅ Pode editar evoluções finalizadas (com auditoria)
- ✅ Pode ver histórico completo de qualquer evolução

#### Avaliações
- ✅ Visualiza avaliações de TODAS as unidades
- ✅ Pode criar/editar/finalizar avaliações de qualquer profissional
- ✅ Pode revisar avaliações de qualquer unidade
- ✅ Pode editar avaliações finalizadas (com auditoria)

#### Relatórios
- ✅ Acessa todos os tipos de relatórios
- ✅ Pode gerar relatórios de qualquer unidade ou todas as unidades
- ✅ Pode ver dados agregados de todas as unidades
- ✅ Não tem restrições de período ou filtros

#### Painel Admin (`/admin/*`)
- ✅ **Gestão de Usuários**: Criar, editar, deletar, resetar senha, impersonar
- ✅ **Gestão de Unidades**: Criar, editar, deletar unidades
- ✅ **Gestão de Salas**: Criar, editar, deletar salas em qualquer unidade
- ✅ **Templates de Avaliação**: Criar, editar, deletar templates (globais ou por unidade)
- ✅ **Configurações do Sistema**: Branding, cores, logo, status (online/manutenção)
- ✅ **Backups**: Executar, baixar, restaurar backups
- ✅ **Logs de Auditoria**: Ver todos os logs do sistema
- ✅ **Notificações**: Criar notificações globais ou por unidade
- ✅ **Feriados e Bloqueios**: Configurar para qualquer unidade

#### Configurações Pessoais
- ✅ Pode alterar seu próprio perfil
- ✅ Pode alterar preferências pessoais
- ✅ Pode trocar de unidade (vê todas as unidades)

### 2.3 Permissões Técnicas
- ✅ **Bypass de validações**: Admin pode forçar ações normalmente bloqueadas
- ✅ **Sem escopo de unidade**: Middleware `unitMiddleware` não aplica filtro para admin
- ✅ **Acesso a dados sensíveis**: Pode ver dados de qualquer usuário
- ✅ **Impersonação**: Pode logar como outro usuário (para suporte)

### 2.4 Implementação Técnica
```typescript
// Middleware tRPC
export const adminProcedure = protectedProcedure.use(async (opts) => {
  if (opts.ctx.user.role !== 'admin') {
    throw new TRPCError({ code: 'FORBIDDEN' });
  }
  return opts.next();
});

// Service - Admin bypassa filtros
export class AppointmentsService {
  async list(input: ListAppointmentsInput, user: User) {
    const where: Prisma.AppointmentWhereInput = {
      // Admin vê tudo, outros filtram por unidade
      ...(user.role !== 'admin' && { unitId: user.unitId }),
      ...input.filters,
    };
    return prisma.appointment.findMany({ where });
  }
}
```

---

## 3. Coordenador

### 3.1 Escopo
**O coordenador gerencia sua unidade (ou unidades atribuídas via `user_units`).**

### 3.2 Funcionalidades

#### Dashboard
- ✅ Visualiza dashboard da sua unidade
- ✅ Vê métricas consolidadas da unidade
- ✅ Acessa relatórios da unidade

#### Agenda
- ✅ Visualiza agenda da sua unidade
- ✅ Pode criar/editar/cancelar agendamentos na sua unidade
- ✅ Pode ver todos os profissionais e salas da unidade
- ✅ Pode mover agendamentos (dentro da unidade)
- ✅ Pode bloquear horários (feriados, indisponibilidade)

#### Pacientes
- ✅ Visualiza pacientes da sua unidade
- ✅ Pode criar/editar pacientes na unidade
- ✅ Pode ver prontuário completo de pacientes da unidade
- ✅ Pode fazer upload de documentos

#### Evoluções
- ✅ Visualiza evoluções da sua unidade
- ✅ Pode criar/editar/finalizar evoluções (próprias ou de outros profissionais da unidade)
- ✅ **Pode revisar evoluções** de profissionais da unidade
- ✅ Pode ver histórico de evoluções da unidade

#### Avaliações
- ✅ Visualiza avaliações da sua unidade
- ✅ Pode criar/editar/finalizar avaliações
- ✅ **Pode revisar avaliações** de profissionais da unidade

#### Relatórios
- ✅ Acessa todos os tipos de relatórios
- ✅ Pode gerar relatórios da sua unidade
- ✅ Pode ver dados agregados da unidade

#### Configurações
- ✅ Pode configurar preferências da agenda (duração padrão, horários úteis)
- ✅ Pode configurar bloqueios e feriados da unidade
- ✅ Pode criar notificações para a unidade
- ❌ **NÃO** pode acessar `/admin/*` (painel admin)
- ❌ **NÃO** pode criar/editar usuários
- ❌ **NÃO** pode alterar configurações globais do sistema

#### Configurações Pessoais
- ✅ Pode alterar seu próprio perfil
- ✅ Pode alterar preferências pessoais
- ✅ Pode trocar de unidade (vê apenas unidades atribuídas)

### 3.3 Permissões Técnicas
- ✅ **Escopo de unidade**: Middleware `unitMiddleware` aplica filtro `unitId`
- ✅ **Revisão de evoluções**: Pode revisar evoluções de profissionais da unidade
- ✅ **Gestão de bloqueios**: Pode configurar feriados e indisponibilidades da unidade
- ❌ **Sem acesso admin**: Não pode acessar rotas `/admin/*`

---

## 4. Profissional

### 4.1 Escopo
**O profissional atende pacientes e preenche evoluções/avaliações na sua unidade.**

### 4.2 Funcionalidades

#### Dashboard
- ✅ Visualiza dashboard da sua unidade
- ✅ Vê métricas pessoais (seus atendimentos, evoluções pendentes)
- ❌ Não vê métricas de outros profissionais

#### Agenda
- ✅ Visualiza agenda da sua unidade
- ✅ **Pode ver apenas seus próprios agendamentos** (ou todos se configurado)
- ✅ Pode criar agendamentos para si mesmo
- ✅ Pode editar/cancelar seus próprios agendamentos
- ✅ Pode marcar atendimento como concluído
- ❌ Não pode editar agendamentos de outros profissionais (exceto admin/coordenador)

#### Pacientes
- ✅ Visualiza pacientes da sua unidade
- ✅ Pode ver prontuário completo de pacientes que atendeu
- ✅ Pode ver timeline de pacientes
- ❌ **NÃO** pode criar/editar pacientes (apenas secretária/coordenador/admin)

#### Evoluções
- ✅ Visualiza evoluções pendentes (próprias)
- ✅ **Pode criar/editar/finalizar apenas suas próprias evoluções**
- ✅ Pode ver histórico de suas evoluções
- ❌ Não pode editar evoluções de outros profissionais
- ❌ Não pode revisar evoluções (apenas coordenador/admin)

#### Avaliações
- ✅ Visualiza avaliações próprias
- ✅ **Pode criar/editar/finalizar apenas suas próprias avaliações**
- ✅ Pode ver histórico de suas avaliações
- ❌ Não pode editar avaliações de outros profissionais
- ❌ Não pode revisar avaliações (apenas coordenador/admin)

#### Relatórios
- ❌ **NÃO** tem acesso a relatórios (apenas coordenador/admin)

#### Configurações
- ✅ Pode configurar preferências pessoais (tema, agenda view)
- ✅ Pode configurar sua cor na agenda
- ❌ **NÃO** pode configurar agenda da unidade
- ❌ **NÃO** pode criar notificações
- ❌ **NÃO** pode acessar `/admin/*`

#### Configurações Pessoais
- ✅ Pode alterar seu próprio perfil
- ✅ Pode alterar preferências pessoais
- ✅ Pode trocar de unidade (vê apenas unidades atribuídas)

### 4.3 Permissões Técnicas
- ✅ **Escopo de unidade**: Middleware aplica filtro `unitId`
- ✅ **Escopo próprio**: Vê apenas seus próprios registros (evoluções, avaliações)
- ❌ **Sem permissão de gestão**: Não pode criar/editar pacientes, usuários, configurações

---

## 5. Secretária

### 5.1 Escopo
**A secretária gerencia agenda e cadastros básicos na sua unidade.**

### 5.2 Funcionalidades

#### Dashboard
- ✅ Visualiza dashboard da sua unidade
- ✅ Vê métricas da agenda (agendamentos do dia, pendências)
- ❌ Não vê métricas clínicas (evoluções, avaliações)

#### Agenda
- ✅ Visualiza agenda da sua unidade
- ✅ **Pode criar/editar/cancelar agendamentos de qualquer profissional**
- ✅ Pode ver todos os profissionais e salas da unidade
- ✅ Pode mover agendamentos
- ✅ Pode marcar status (confirmado, cancelado)
- ❌ Não pode marcar atendimento como concluído (apenas profissional)

#### Pacientes
- ✅ Visualiza pacientes da sua unidade
- ✅ **Pode criar/editar pacientes**
- ✅ Pode ver prontuário completo
- ✅ Pode fazer upload de documentos
- ✅ Pode ver timeline de pacientes
- ❌ Não pode editar dados clínicos (evoluções, avaliações)

#### Evoluções
- ❌ **NÃO** tem acesso a evoluções (apenas visualização se necessário)

#### Avaliações
- ❌ **NÃO** tem acesso a avaliações (apenas visualização se necessário)

#### Relatórios
- ❌ **NÃO** tem acesso a relatórios (apenas coordenador/admin)

#### Configurações
- ✅ Pode configurar preferências pessoais
- ❌ **NÃO** pode configurar agenda da unidade
- ❌ **NÃO** pode criar notificações
- ❌ **NÃO** pode acessar `/admin/*`

#### Configurações Pessoais
- ✅ Pode alterar seu próprio perfil
- ✅ Pode alterar preferências pessoais
- ✅ Pode trocar de unidade (vê apenas unidades atribuídas)

### 5.3 Permissões Técnicas
- ✅ **Escopo de unidade**: Middleware aplica filtro `unitId`
- ✅ **Gestão de agenda**: Pode gerenciar agendamentos de todos os profissionais
- ✅ **Gestão de pacientes**: Pode criar/editar pacientes
- ❌ **Sem acesso clínico**: Não pode criar/editar evoluções ou avaliações

---

## 6. Matriz de Permissões (Resumo)

| Funcionalidade | Admin | Coordenador | Profissional | Secretária |
|----------------|-------|-------------|--------------|------------|
| **Dashboard Global** | ✅ Todas unidades | ✅ Unidade | ✅ Unidade (pessoal) | ✅ Unidade |
| **Agenda - Ver** | ✅ Todas unidades | ✅ Unidade | ✅ Próprios | ✅ Unidade |
| **Agenda - Criar/Editar** | ✅ Todas unidades | ✅ Unidade | ✅ Próprios | ✅ Unidade |
| **Pacientes - Ver** | ✅ Todas unidades | ✅ Unidade | ✅ Unidade | ✅ Unidade |
| **Pacientes - Criar/Editar** | ✅ Todas unidades | ✅ Unidade | ❌ | ✅ Unidade |
| **Evoluções - Ver** | ✅ Todas unidades | ✅ Unidade | ✅ Próprias | ❌ |
| **Evoluções - Criar/Editar** | ✅ Todas unidades | ✅ Unidade | ✅ Próprias | ❌ |
| **Evoluções - Revisar** | ✅ Todas unidades | ✅ Unidade | ❌ | ❌ |
| **Avaliações - Ver** | ✅ Todas unidades | ✅ Unidade | ✅ Próprias | ❌ |
| **Avaliações - Criar/Editar** | ✅ Todas unidades | ✅ Unidade | ✅ Próprias | ❌ |
| **Avaliações - Revisar** | ✅ Todas unidades | ✅ Unidade | ❌ | ❌ |
| **Relatórios** | ✅ Todas unidades | ✅ Unidade | ❌ | ❌ |
| **Painel Admin (`/admin/*`)** | ✅ Total | ❌ | ❌ | ❌ |
| **Configurar Unidade** | ✅ Todas | ✅ Própria | ❌ | ❌ |
| **Criar Notificações** | ✅ Todas unidades | ✅ Unidade | ❌ | ❌ |
| **Backups** | ✅ | ❌ | ❌ | ❌ |
| **Logs de Auditoria** | ✅ | ❌ | ❌ | ❌ |

---

## 7. Implementação Técnica

### 7.1 Middleware tRPC
```typescript
// packages/api/src/middleware/auth.ts
export const authMiddleware = t.middleware(async ({ ctx, next }) => {
  const user = await getUserFromToken(ctx.token);
  if (!user) {
    throw new TRPCError({ code: 'UNAUTHORIZED' });
  }
  return next({
    ctx: {
      ...ctx,
      user,
    },
  });
});

// packages/api/src/middleware/role.ts
export const roleMiddleware = (allowedRoles: Role[]) => {
  return t.middleware(async ({ ctx, next }) => {
    if (!allowedRoles.includes(ctx.user.role)) {
      throw new TRPCError({ code: 'FORBIDDEN' });
    }
    return next({ ctx });
  });
};

// packages/api/src/middleware/unit.ts
export const unitMiddleware = t.middleware(async ({ ctx, next }) => {
  // Admin bypassa filtro de unidade
  if (ctx.user.role === 'admin') {
    return next({ ctx: { ...ctx, unitId: undefined } });
  }
  
  // Outros roles: injeta unitId da sessão
  const unitId = ctx.user.unitId || ctx.user.defaultUnitId;
  if (!unitId) {
    throw new TRPCError({ code: 'BAD_REQUEST', message: 'Unit not selected' });
  }
  
  return next({ ctx: { ...ctx, unitId } });
});
```

### 7.2 Procedures tRPC
```typescript
// packages/api/src/routers/appointments.ts
export const appointmentsRouter = router({
  list: protectedProcedure
    .use(unitMiddleware)
    .input(z.object({ filters: z.object({}).optional() }))
    .query(async ({ input, ctx }) => {
      const where: Prisma.AppointmentWhereInput = {
        // Admin vê tudo, outros filtram por unidade
        ...(ctx.user.role !== 'admin' && { unitId: ctx.unitId }),
        // Profissional vê apenas seus próprios
        ...(ctx.user.role === 'profissional' && { professionalId: ctx.user.id }),
        ...input.filters,
      };
      return prisma.appointment.findMany({ where });
    }),

  create: protectedProcedure
    .use(unitMiddleware)
    .input(CreateAppointmentSchema)
    .mutation(async ({ input, ctx }) => {
      // Admin pode criar em qualquer unidade
      const unitId = ctx.user.role === 'admin' 
        ? input.unitId || ctx.unitId 
        : ctx.unitId;
      
      // Profissional só pode criar para si mesmo
      if (ctx.user.role === 'profissional' && input.professionalId !== ctx.user.id) {
        throw new TRPCError({ code: 'FORBIDDEN' });
      }
      
      return appointmentsService.create({ ...input, unitId }, ctx.user);
    }),
});
```

### 7.3 Services
```typescript
// packages/api/src/services/AppointmentsService.ts
export class AppointmentsService {
  async list(input: ListAppointmentsInput, user: User) {
    const where: Prisma.AppointmentWhereInput = {
      // Admin bypassa filtro
      ...(user.role !== 'admin' && { unitId: input.unitId }),
      // Profissional vê apenas seus próprios
      ...(user.role === 'profissional' && { professionalId: user.id }),
      ...input.filters,
    };
    return prisma.appointment.findMany({ where });
  }

  async create(input: CreateAppointmentInput, user: User) {
    // Validações por role
    if (user.role === 'profissional' && input.professionalId !== user.id) {
      throw new TRPCError({ code: 'FORBIDDEN' });
    }
    
    // Admin pode criar em qualquer unidade
    const unitId = user.role === 'admin' 
      ? input.unitId || user.unitId 
      : user.unitId;
    
    return prisma.appointment.create({
      data: { ...input, unitId, createdBy: user.id },
    });
  }
}
```

---

## 8. Regras Importantes

### 8.1 Admin Sempre Tem Acesso Total
- ✅ Admin **NUNCA** é bloqueado por filtros de unidade
- ✅ Admin pode acessar **TODAS** as rotas e funcionalidades
- ✅ Admin pode ver dados de **TODAS** as unidades
- ✅ Admin pode criar/editar/deletar **QUALQUER** registro

### 8.2 Escopo de Unidade
- ✅ Coordenador, Profissional e Secretária são filtrados por unidade
- ✅ Middleware `unitMiddleware` injeta `unitId` automaticamente
- ✅ Admin bypassa este filtro

### 8.3 Escopo Próprio (Profissional)
- ✅ Profissional vê apenas seus próprios registros (evoluções, avaliações)
- ✅ Profissional pode criar agendamentos apenas para si mesmo
- ✅ Coordenador pode ver/editar registros de todos os profissionais da unidade

### 8.4 Validações por Role
- ✅ Sempre validar role antes de permitir ações
- ✅ Usar middleware tRPC para garantir permissões
- ✅ Services devem validar permissões também (defesa em profundidade)

---

## 9. Exemplos de Uso

### 9.1 Admin Visualizando Agenda de Todas as Unidades
```typescript
// Frontend
const { data } = trpc.appointments.list.useQuery({
  unitId: undefined, // Admin pode passar undefined para ver todas
});

// Backend (appointmentsRouter.list)
// Como user.role === 'admin', o filtro unitId é ignorado
```

### 9.2 Coordenador Revisando Evolução
```typescript
// Frontend
trpc.evolutions.review.useMutation({
  onSuccess: () => {
    toast.success('Evolução revisada');
  },
});

// Backend (evolutionsRouter.review)
// Middleware verifica: role === 'coordenador' || role === 'admin'
// Service valida: evolução pertence à unidade do coordenador
```

### 9.3 Profissional Criando Evolução
```typescript
// Frontend
trpc.evolutions.create.useMutation({
  onSuccess: () => {
    toast.success('Evolução criada');
  },
});

// Backend (evolutionsRouter.create)
// Service valida: professionalId === ctx.user.id (ou admin bypassa)
```

---

> **IMPORTANTE**: Durante o desenvolvimento, sempre testar que:
> 1. Admin tem acesso total a tudo
> 2. Outros roles são filtrados corretamente por unidade
> 3. Profissional vê apenas seus próprios registros
> 4. Coordenador pode gerenciar sua unidade
> 5. Secretária pode gerenciar agenda e pacientes

