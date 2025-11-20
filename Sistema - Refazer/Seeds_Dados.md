# Seeds & Dados Demo – Equidade+ Nova Stack

Guia para configurar dados de exemplo em ambientes `local` e `staging`.

## 1. Objetivos
- Permitir teste rápido das funcionalidades (agenda, evoluções, avaliações).
- Fornecer dados realistas para demos e QA.
- Garantir anonimização em staging (sem dados reais).

## 2. Estrutura Recomendada
- Arquivo `prisma/seed.ts` (executado por `pnpm prisma db seed`).
- Módulos de seed separados:
  - `seedRolesAndAdmin()`
  - `seedUnitsAndRooms()`
  - `seedUsers()` (coordenador, profissionais, secretárias).
  - `seedPatients()`
  - `seedAppointmentsAndEvolutions()`
  - `seedAssessments()`
  - `seedNotifications()`

## 3. Dados Sugeridos
### 3.1 Usuários
| Nome | Email | Papel | Unidade |
| --- | --- | --- | --- |
| Admin Global | admin@equidade.test | admin | - |
| Coord Central | coordenacao@equidade.test | coordenador | Clínica Central |
| Prof Terapia | profissional@equidade.test | profissional | Clínica Central |
| Prof Psicologia | psicologia@equidade.test | profissional | Clínica Central |
| Secretaria | secretaria@equidade.test | secretaria | Clínica Central |

Senhas padrão: `Admin123!`, `Coordenador123!`, etc. (documentadas em `README.md`).

### 3.2 Pacientes
- 10 pacientes com diagnósticos variados (`TEA`, `TDAH`, `Motricidade`).
- Incluir contatos de responsáveis e info clínica.
- Associar documentos fictícios (ex.: `laudo-joao.pdf`).

### 3.3 Agenda
- Gerar atendimentos para os próximos 7 dias:
  - 4 atendimentos/dia por profissional.
  - Status variados: agendado, confirmado, concluído, cancelado.
- Incluir sala e observações fictícias.

### 3.4 Evoluções/Avaliações
- Para cada atendimento concluído, criar evolução pendente ou finalizada.
- Criar 3 templates de avaliação:
  - `Anamnese – Terapia Ocupacional`
  - `Avaliação Semanal – Psicologia`
  - `Escala de Progresso`
- Gerar pelo menos 5 avaliações finalizadas.

### 3.5 Relatórios / Notificações
- Criar notificações de exemplo (manutenção, reunião).
- Logar algumas ações (audit) para demonstrar trilha.

## 4. Scripts de Apoio
Criar pasta `scripts/` com:
- `seed-demo.ts`: executa seed completa (local).
- `seed-staging.ts`: seed reduzida (dados mascarados, sem documentos).
- `reset-db.ts`: drop + migrate + seed (para desenvolvimento rápido).

Exemplo (pseudo código):
```ts
import { prisma } from '../src/prisma/client';
import { seedRolesAndAdmin } from './seeds/roles';
import { seedUnitsAndRooms } from './seeds/units';
// ...

async function main() {
  await seedRolesAndAdmin();
  await seedUnitsAndRooms();
  await seedUsers();
  await seedPatients();
  await seedAppointmentsAndEvolutions();
  await seedAssessments();
  await seedNotifications();
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
}).finally(async () => {
  await prisma.$disconnect();
});
```

## 5. Dados para Staging (Anonimização)
- Substituir nomes por `Paciente A`, `Paciente B`.
- Usar datas relativas (ex.: `today + n days`).
- Remover dados identificáveis (documentos reais).
- Geração automática via faker (`@faker-js/faker`).

## 6. Verificação
- Após seed, rodar:
  - `pnpm prisma studio` para revisar dados.
  - Testes E2E básicos (agenda, evolução, avaliação).
- Documentar credenciais em `README.md`.

## 7. Manutenção
- Atualizar seeds quando novos módulos surgirem.
- Incluir toggles (env) para habilitar seeds parciais.
- Usar versionamento semântico (`seedVersion` guardado em `system_settings`).

---

> Seeds são essenciais para onboarding do time e demonstrações. Mantenha-os sincronizados com as últimas funcionalidades.*** End Patch

