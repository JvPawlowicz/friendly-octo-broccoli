# Plano de Testes – Reescrita Equidade+

## 1. Objetivos
- Garantir paridade funcional com o sistema atual.
- Cobrir fluxos críticos (agenda → evolução, avaliações, relatórios, configurações).
- Validar requisitos não funcionais (performance, segurança, acessibilidade).
- Automatizar ao máximo para suportar deploy contínuo na Railway.

## 2. Estratégia Geral
- **Piramide de testes**:
  - Unitários (NestJS services, guards, utilitários).
  - Integração (endpoints REST com base real – Postgres test container).
  - End-to-End (Next.js web + API utilizando Playwright).
- **Ambientes**:
  - `local`: execução rápida com banco SQLite (unit/integration com Prisma test env).
  - `CI`: pipelines GitHub Actions com Postgres e Redis usando `docker compose`.
  - `staging`: execução de smoke tests pós-deploy.

## 3. Ferramentas
- **Unit/Integration (API)**: Jest + `@nestjs/testing` + `supertest`.
- **Contract testing**: Zod shared schemas separam request/response; usar `zod-openapi` para garantir alinhamento.
- **E2E (web)**: Playwright com fixtures por perfil (admin, coordenador, profissional, secretária).
- **Lint**: ESLint + TypeScript strict + Prisma lint.
- **Accessibility**: `@axe-core/playwright` nos principais fluxos.
- **Performance**: k6 (script direcionado para agenda, dashboard e relatórios).

## 4. Cobertura por Módulo
| Módulo | Testes unit/integration | Testes E2E |
| --- | --- | --- |
| Autenticação & RBAC | Guards, policies, refresh token, troca de unidade | Login, troca de unidade, sessão expirada |
| Agenda | Regras de conflito, bloqueios, criação auto | Criar, editar, mover, concluir atendimento |
| Evoluções | Autosave, finalização, revisão, PDF | Fluxo completo de evolução pendente |
| Avaliações | Template versioning, responses, assinatura | Criar avaliação, finalizar, revisar |
| Pacientes | CRUD, documentos, timeline | Cadastro, upload, visualização timeline |
| Relatórios | Aggregations, export jobs | Geração e download CSV/PDF |
| Configurações | Branding, preferências, backups | Alterar branding, executar backup manual |
| Chat & Notificações | Polling, mark as read | Enviar mensagem, ver mural, marcar como lida |
| Logs/Auditoria | Registro de ações, filtros | Consultar log, exportar |

## 5. Planos de Caso (E2E)
### 5.1 Agenda → Evolução
1. Secretária agenda paciente.
2. Profissional conclui atendimento.
3. Evolução pendente aparece no dashboard.
4. Profissional preenche, salva rascunho, finaliza.
5. Coordenador revisa e marca como revisada.
6. Timeline do paciente apresenta registro.

### 5.2 Avaliação clínica
1. Admin cria template (campos texto, select, checkbox).
2. Profissional inicia avaliação, salva, finaliza.
3. Coordenador revisa.
4. Exportação PDF disponível no histórico.

### 5.3 Relatório produtividade
1. Coordenador aplica filtros (unidade, período, profissional).
2. Visualiza gráfico e tabela.
3. Exporta CSV, baixa arquivo, valida dados.

### 5.4 Configurações e branding
1. Admin altera nome, logo, cor primária.
2. Verifica aplicação imediata no header/login.
3. Gera backup manual e baixa arquivo.

### 5.5 Chat & Notificações
1. Profissional envia mensagem para coordenador.
2. Coordenador responde; ambos marcam como lido.
3. Admin publica notificação global; usuários recebem e marcam lida.

## 6. Métricas e Critérios
- **Cobertura mínima**: 80% statements no backend; 70% linhas no frontend (focar em lógica).
- **Build breaker**: falha em testes unit/integration/E2E bloqueia merge.
- **Tempo de pipeline**: alvo < 15 minutos (parallelização de jobs).
- **Definição de pronto**: feature só conclui após
  - testes unitários relevantes,
  - caso E2E criado/atualizado (se aplicável),
  - documentação/ADR atualizada.

## 7. Processos
- **Pre-commit**: `lint-staged` executa `pnpm lint --filter ./...` e `pnpm test --filter api -- --bail --findRelatedTests`.
- **Pull Request**: obrigatório rodar `pnpm test:ci` local antes de abrir PR.
- **Release**:
  - CI executa suíte completa.
  - Deploy para staging.
  - Smoke tests automáticos + QA manual direcionado (checklist por módulo).
- **Bugfix**: todo bug deve incluir teste reproduzindo cenário.

## 8. Testes de Performance (k6)
- **Cenários**:
  - 50 usuários simultâneos navegando na agenda (GET, PATCH).
  - Exportação de relatórios em carga (jobs fila).
  - Login simultâneo (rate limiting).
- **Indicadores**:
  - Latência p(95) < 700ms para endpoints dashboard/agenda.
  - Zero erros HTTP 5xx durante carga planejada.

## 9. Segurança
- Testes de permissão: tentativas de acessar recursos de outra unidade → 403.
- Brute force login: garantir rate limit e bloqueio temporário.
- Validação uploads: rejeitar tipos não permitidos, testar vírus (arquivo eicar).
- Verificar sessões expiradas (refresh inválido) e CSRF (middlewares).

## 10. Acessibilidade
- Execução de `axe` nas principais telas (dashboard, agenda, evolução, avaliação).
- Testes de teclado (tab order, skip links).
- Verificação de contraste via `tailwindcss-accessibility`.

## 11. Manutenção
- Revisar plano trimestralmente ou quando adicionar módulos críticos.
- Adicionar seção de cobertura nos relatórios de release.
- Documentar scripts e comandos em `docs/testing/test-plan.md` (gerado a partir deste arquivo).

