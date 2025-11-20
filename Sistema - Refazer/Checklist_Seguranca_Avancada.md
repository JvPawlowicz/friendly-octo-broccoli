# Checklist de Segurança Avançada – Equidade+ Nova Stack

## 1. Acesso & Identidade
- [ ] Hash de senha usando Argon2id com parâmetros atualizados (memória >= 64MB).
- [ ] Rotina de rotação de senha administrativa (Railway, S3, SMTP) definida.
- [ ] 2FA habilitado para equipe administrativa (GitHub, Railway, S3).
- [ ] Registros de login com IP e user agent.
- [ ] Política de senha forte (mín 8 caracteres, complexidade).
- [ ] Bloqueio temporário após X tentativas de login falhas.

## 2. Autorização
- [ ] Todas as rotas protegidas com `JwtAuthGuard` e `RolesGuard`.
- [ ] Policies revisadas por domínio (paciente, agenda, evolução, avaliação).
- [ ] Escopo de unidade aplicado (unit_id) em todas queries.
- [ ] Testes cobrindo tentativas de acesso indevido (403 esperado).
- [ ] Auditoria de permissões a cada release (roles vs features).

## 3. Proteção de Dados
- [ ] Conexão com Postgres usando SSL (`sslmode=require`).
- [ ] Armazenamento de documentos em S3 com policies restritivas (apenas app).
- [ ] URLs de download assinadas com expiração curta (15 min).
- [ ] Sanitização de campos ricos (evoluções, avaliações) para evitar XSS.
- [ ] Logs não incluem dados clínicos sensíveis.
- [ ] Política de retenção definida (LGPD) e implementada.

## 4. Aplicação
- [ ] CSRF protegido (cookies HttpOnly + SameSite).
- [ ] Rate limit aplicado a rotas críticas (login, reset, uploads).
- [ ] Helmet (ou equivalente) configurado (headers de segurança).
- [ ] Uploads validam tipo/ tamanho e fazem scan opcional (antivírus).
- [ ] Dependências auditadas (npm audit, npm-check-updates).
- [ ] Feature flags controlam lançamentos sensíveis.

## 5. Infraestrutura
- [ ] Railway com alertas de CPU, memória, restarts.
- [ ] Backups testados (restore em staging).
- [ ] Pipeline CI/CD protegido (branch protection, reviews).
- [ ] Secrets guardados apenas no Railway (sem .env no repo).
- [ ] Monitoramento Sentry/Axiom configurado com alertas.
- [ ] Logging estruturado com redacting de dados sensíveis.

## 6. Compliance LGPD
- [ ] Registro de consentimento e finalidade (quando aplicável).
- [ ] Procedimento para anonimização/exclusão de paciente.
- [ ] Política de privacidade atualizada.
- [ ] Termos de uso ajustados para nova plataforma.
- [ ] Treinamento da equipe sobre manuseio de dados clínicos.

## 7. Pentest & Threat Modeling
- [ ] Threat modeling realizado (diagrama de fluxo e riscos).
- [ ] Pentest interno/externo agendado antes do go-live.
- [ ] Checklist OWASP Top 10 revisado e mitigado.
- [ ] Plano de resposta a incidentes definido (ver `Plano_Comunicacao_Suporte.md`).

## 8. Pós-go-live
- [ ] Monitoramento ativo de falhas e alertas.
- [ ] Revisão trimestral de acessos e permissões.
- [ ] Atualização contínua das dependências com patch de segurança.
- [ ] Registro de incidentes e aprendizado documentado.

---

Atualize este checklist conforme novas medidas de segurança forem adotadas. Use-o como gate antes da liberação da nova versão.*** End Patch

