# Checklist de Go-Live – Equidade+ Nova Stack

## 1. Pré-Go-Live (T-4 semanas)
- [ ] Completar migração staging com dados atualizados.
- [ ] Aprovação clínica dos principais fluxos (agenda, evolução, avaliação, relatórios).
- [ ] Documentação final do usuário (manuais, vídeos rápidos).
- [ ] Treinamento equipe interna (secretaria, profissionais, coordenadores, admin).
- [ ] Plano de comunicação para clientes (email, avisos no sistema atual).
- [ ] Validar planos de contingência (rollback, suporte).

## 2. Pré-Go-Live (T-1 semana)
- [ ] Congelar deploys em produção do sistema legado (apenas hotfix).
- [ ] Revisar acessos (Railway, S3, GitHub) – garantir apenas equipe autorizada.
- [ ] Configurar alertas Railway/Sentry/Axiom (limiares testados).
- [ ] Revisar jobs agendados (backup, lembretes).
- [ ] Confirmar janela de manutenção com stakeholders (data/hora).
- [ ] Verificar licenças/assinaturas (S3, Railway plano, SMTP).
- [ ] Checklist LGPD final (consentimento, políticas, contratos).

## 3. Cutover (Dia D)
1. **Modo manutenção**
   - [ ] Habilitar mensagem de manutenção no sistema legado.
   - [ ] Bloquear logins/novos agendamentos (se possível).
2. **Snapshots**
   - [ ] Dump completo banco legado (MySQL).
   - [ ] Backup storage (documentos).
3. **Migração final**
   - [ ] Rodar scripts export -> import (prod).
   - [ ] Upload documentos para S3 (verificar checksums).
4. **Verificações**
   - [ ] Rodar `scripts/verify-migration.ts` (contagem registros).
   - [ ] Validar 10 prontuários random (clínica).
   - [ ] Verificar agenda do dia + próximas 2 semanas.
   - [ ] Checar evoluções pendentes e avaliações recentes.
5. **Configuração**
   - [ ] Atualizar DNS/domínios (se necessário).
   - [ ] Configurar envs finais (APP_URL, SMTP).
   - [ ] Executar `pnpm prisma migrate deploy`.
   - [ ] Executar seeding final (apenas se necessário).
6. **Liberação**
   - [ ] Desabilitar modo manutenção.
   - [ ] Avisar stakeholders (Slack/Email) “Sistema online”.

## 4. Pós-Go-Live (T+0 a T+14 dias)
- [ ] Monitorar métricas (erros 5xx, latência, uso CPU/memória).
- [ ] Analisar logs de auditoria (acessos inesperados).
- [ ] Revisar backups diário (confirmar execução e integridade).
- [ ] Realizar pesquisa de feedback com usuários após 1 semana.
- [ ] Atender rapidamente bugs P0/P1 (SLA: <4h).
- [ ] Atualizar documentação conforme ajustes emergenciais.
- [ ] Reunião diária rápida de hypercare (10 min).
- [ ] Planejar sprint pós-hypercare com melhorias.

## 5. Rollback Plan (em caso de falha crítica)
- [ ] Restaurar banco MySQL legado (snapshot).
- [ ] Repoint DNS/app para infraestrutura anterior.
- [ ] Comunicar stakeholders (status page, email) sobre rollback.
- [ ] Investigar causa, registrar post-mortem antes de tentar novo go-live.

## 6. Artefatos Necessários
- Scripts de migração validados.
- Relatórios de verificação (antes/depois).
- Manual de usuário final.
- Plano comunicação clientes.
- Post-mortem template.

## 7. Critérios de Sucesso Pós Go-Live
- Latência média < 500 ms nas rotas principais.
- 0 incidentes críticos (P0) nas primeiras 72h.
- Taxa de erro < 1% (API).
- Feedback positivo (>80% satisfação) das equipes-chave.

