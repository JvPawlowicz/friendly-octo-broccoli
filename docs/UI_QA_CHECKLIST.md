# QA Visual – Fase 1

Execute após `php artisan serve` e `npm run dev`.

## Rotas obrigatórias
- `/dashboard` – cards, tabela de pendências, KPIs e toasts.
- `/app/agenda` – filtros persistidos, legenda, modal de atendimento.
- `/app/pacientes` → `Prontuário` – linha do tempo, tabs, anexos.
- `/app/evolucoes` – listagem completa + modal Livewire.
- `/app/avaliacoes` e `/app/minhas-avaliacoes`.
- `/app/relatorios` + abas de frequência/produtividade.
- `/app/meu-perfil` e `/profile/edit`.
- `/app/colaboradores` (somente Admin).

## Itens a observar
- Sidebar fixa e header com métricas.
- Seleção da unidade no header com persistência.
- Responsividade (≥ 1280px e ≤ 1024px).
- Toasts e modais sobrepostos corretamente.
- Acesso autorizado por role (Secretaria sem evoluções, etc.).

## Registro de achados
Anote problemas com captura ou descrição:

| Data | Rota | Descrição | Status | Responsável |
|------|------|-----------|--------|-------------|


