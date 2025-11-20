# Governança & Processos – Equidade+ Nova Stack

## 1. Papéis e Responsabilidades
- **Product Manager (PM)**: priorização do backlog, alinhamento com stakeholders clínicos, roadmap.
- **Tech Lead**: decisões técnicas, revisão de arquitetura, garantia de qualidade.
- **Desenvolvedores Backend/Frontend**: implementação, testes, documentação.
- **QA**: estratégia de testes, automação, validação de releases.
- **Designer UX/UI**: mapas de tela, protótipos, suporte a componentes.
- **DevOps/Infra (compartilhado)**: Railway, pipelines, monitoramento.
- **Stakeholders clínicos**: validação funcional, feedback contínuo.

## 2. Fluxo de Desenvolvimento
1. **Discovery / Refinamento**
   - PM + Tech Lead + Designer detalham user stories.
   - Atualização de blueprint/ADR se necessário.
2. **Planejamento Sprint**
   - Seleção de histórias com definição clara de DoD (Definition of Done).
3. **Implementação**
   - Trabalhos preferencialmente em pares (pair programming) para partes críticas.
   - Branch naming `feature/<epic>-<descricao>`.
   - Commits convencionais (`feat:`, `fix:`, `chore:`).
4. **Code Review**
   - Dois revisores para áreas sensíveis (segurança, migração).
   - Checklist: testes, lint, cobertura, documentação, impacto migração.
5. **Testes**
   - Dev roda unit/integration; QA valida E2E.
   - Atualização de suites Playwright/k6 conforme necessário.
6. **Merge**
   - Squash & merge após aprovação + pipeline verde.
7. **Deploy**
   - Staging automático: validar manualmente features novas.
   - Produção: janela definida (preferência fora de horário comercial).

## 3. Definition of Ready (DoR)
- User story com descrição, critérios de aceite, dependências mapeadas.
- Designs/Wireframes anexados (quando aplicável).
- ADR atualizado ou em andamento para decisões relevantes.
- Estimativa em pontos ou horas validada pelo time.

## 4. Definition of Done (DoD) – Complemento ao Plano de Testes
- Código revisado e mergeado.
- Lint + testes unitários/integrados passando.
- Caso E2E criado/atualizado (se aplicável ao fluxo).
- Documentação atualizada (README, blueprint, ADR, manual usuário).
- Feature flag (se usada) documentada.
- Observabilidade configurada (logs métricas, alertas).

## 5. Gestão de Backlog
- **Epics** refletem grandes módulos (Agenda, Evoluções, etc.).
- **Stories** priorizadas por valor/custo, tamanho ideal <= 5 pontos.
- **Bugs** categorizados (P0 crítico, P1 alto, P2 médio, P3 baixo).
- Uso de ferramenta (Linear/Jira) com integração GitHub.

## 6. Comunicação
- Daily (15 min) – status, bloqueios, plano do dia.
- Weekly Tech Sync – decisões técnicas, dívidas.
- Monthly Steering – status geral com equipe clínica/diretoria.
- Canal Slack/Teams dedicado (#equidade-rewrite) para avisos e incidentes.

## 7. Gestão de Incidentes
- Severidade P0: queda geral, dados incorretos críticos → acionamento imediato (Tech Lead + PM).
- Playbook:
  1. Identificar e classificar.
  2. Comunicar stakeholders (status page + canais).
  3. Mitigar (rollback, feature flag).
  4. Post-mortem em até 48h (documentar causa raiz e ações).

## 8. Compliance & Segurança
- Revisão semestral de acessos Railway/S3.
- 2FA obrigatório para todas as contas produtivas.
- Política de senha forte + expiração anual.
- Auditoria de logs: relatórios trimestrais.
- Revisão LGPD (dados pessoais, consentimento) antes do go-live.

## 9. Documentação Viva
- `Sistema - Refazer/` é fonte oficial (versão controlada).
- Ao concluir épico, atualizar blueprint e arquivos relacionados.
- ADRs revisitados quando decisão mudar (marcar status `superseded`).
- Registro de reuniões/decisões chave em Notion (link referenciado nos docs).

## 10. Onboarding de Novos Membros
- Guia rápido contendo:
  - Visão geral do projeto.
  - Setup local (`Setup_Monorepo_e_Ambientes.md`).
  - Fluxos principais (agenda→evolução).
  - Boas práticas de código, padrões commit.
- Atribuir buddy para primeira sprint.

## 11. Métricas de Processo
- Lead time (story aberta → deploy).
- Cycle time (in progress → done).
- Débito técnico registrado vs resolvido por sprint.
- Bugs pós-deploy (por severidade).
- Satisfação stakeholders (survey mensal).

## 12. Revisão Contínua
- Retro quinzenal com ações concretas.
- Revisão do blueprint a cada mês.
- Ajuste de cronograma conforme necessidade (registrado no `Cronograma_Detalhado.md`).

