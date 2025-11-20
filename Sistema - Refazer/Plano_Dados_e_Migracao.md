# Plano de Dados e Migração – Equidade+

## 1. Objetivos
- Garantir migração fiel dos dados clínicos (pacientes, agendamentos, evoluções, avaliações, documentos).
- Minimizar tempo de indisponibilidade durante o cutover.
- Atender requisitos de LGPD (consentimento, retenção, anonimização quando necessário).

## 2. Inventário de Dados Atual
| Entidade | Fonte atual | Observações |
| --- | --- | --- |
| Usuários | tabela `users` (MySQL) | Roles via Spatie; possíveis registros inativos. |
| Unidades & Salas | tabelas `units`, `rooms` | Checar relacionamentos com agenda. |
| Pacientes | `patients`, `patient_guardians` | Campos sensíveis (diagnóstico, alergias) devem ser validados. |
| Documentos | storage em disco (`storage/app/public`) | Nome dos arquivos + metadata em tabela. |
| Agendamentos | `appointments` | Status enum, referências a professional/patient. |
| Evoluções | `evolutions`, `evolution_addendums` | Conteúdo HTML/JSON; verificar encoding. |
| Avaliações | `assessments`, `assessment_templates` | Templates em JSON; versionamento. |
| Relatórios gerados | Não persistidos | Serão recriados sob demanda. |
| Logs | `activity_log` (Spatie) | Manter últimos 12 meses. |

## 3. Mapeamento para Novo Schema (Prisma)
- Consolidar diagráfico ER: ver `Blueprint_Nova_Stack.md` seção 5.
- Criar documento `docs/dicionario_dados.csv` (em construção) com:
  - Nome da tabela,
  - Descrição do campo,
  - Tipo atual,
  - Tipo novo,
  - Regras de transformação.

### Exemplos de transformação
- `users.password` → manter hash (bcrypt) ou rehash para Argon2id. Estratégia: migrar hash atual e forçar reset de senha após primeiro login.
- `patients.diagnosis_tags` (string separada por vírgula) → array JSON no Postgres.
- `appointments.status` (strings) → enum Prisma (`scheduled`, `confirmed`, `checked_in`, `in_progress`, `completed`, `cancelled`, `no_show`).
- `evolutions.content` (HTML) → armazenar como JSON {relato, conduta, objetivos}. Necessário parse/transform.

## 4. Estratégia de Migração
1. **Preparação**
   - Congelar novas features no sistema legado durante janela de migração.
   - Criar snapshots de banco MySQL + storage arquivos.
2. **Extração**
   - Scripts Node/TypeScript (`scripts/export-legacy.ts`) utilizando `mysql2` e `fs`.
   - Exportar dados para JSON/CSV segmentados por entidade.
3. **Transformação**
   - Aplicar normalizações (ex.: datas timezone, enums).
   - Sanitizar dados inválidos (campos vazios, duplicados).
   - Registrar inconsistências em relatório (`logs/migration-issues.csv`).
4. **Carga**
   - Executar `prisma migrate deploy`.
   - Rodar `scripts/import-legacy.ts` para inserir dados em lotes (batch size 500).
   - Upload de arquivos para bucket S3 usando prefixo `legacy-migration/{unit}/{id}`.
5. **Validação**
   - Scripts `scripts/verify-migration.ts` comparando contagens e checksums.
   - QA funcional em staging (fluxos reais com dados migrados).
6. **Cutover**
   - Ativar modo manutenção no sistema antigo.
   - Rodar extração incremental (deltas) desde o snapshot.
   - Reexecutar transformação/carga.
   - Liberar novo sistema, monitorar métricas.

## 5. Plano de Testes da Migração
- **Pré-migração**
  - Testar scripts export/import em ambiente de desenvolvimento com subset de dados.
  - Documentar tempo médio de execução por etapa.
- **Pós-migração (staging)**
  - Checklist por módulo (agenda, pacientes, evoluções) verificando 10 registros aleatórios.
  - Validar timeline do paciente (eventos completos).
  - Garantir arquivos baixam corretamente do novo storage.
- **Produção**
  - Executar `verify-migration` e comparar contagens 100%.
  - Obter aprovação do time clínico (confirmação de prontuários críticos).

## 6. Governança de Dados
- **Retenção**
  - Dados clínicos: manter tempo legal (mínimo 20 anos).
  - Logs: 12 meses (export para arquivo ao final).
  - Backups: retenção 30 dias (configurável).
- **Direitos do titular**
  - Implementar endpoint/processo para anonimização (remover identificadores do paciente).
  - Registrar pedidos de exclusão em auditoria.
- **Acesso**
  - Restringir acesso ao bucket S3 por IAM user com políticas granulares.
  - Variáveis de ambiente armazenadas na Railway (secret scope).

## 7. Riscos e Mitigações
- **Inconsistência de relacionamentos**: rodar validações cruzadas (appointments sem paciente → registrar e resolver manualmente).
- **Dados sensíveis corrompidos**: manter backup do snapshot original até final do hypercare.
- **Tempo de migração longo**: estimar volume (tamanho banco + storage) e considerar migração incremental por unidade se necessário.
- **Falha no upload de arquivos**: usar mecanismo de retry e logs detalhados.

## 8. Cronograma de Migração (Macro)
| Etapa | Responsável | Duração estimada |
| --- | --- | --- |
| Preparar scripts export/import | Engenharia | 1 semana |
| Dry-run com amostra real | Engenharia + Produto | 1 semana |
| Ajustes/fixes pós dry-run | Engenharia | 1-2 semanas |
| Migração staging completa | Engenharia | 3 dias |
| Validação QA/Clínica | Produto + Clínica | 1 semana |
| Cutover produção | Engenharia + Gestão clínica | 1 fim de semana |
| Hypercare | Todos | 2 semanas |

## 9. Artefatos
- Scripts em `scripts/`.
- Logs em `logs/migration/`.
- Relatórios de validação em `docs/migration/`.
- Checklist detalhado (ver `Checklist_GoLive.md`).

