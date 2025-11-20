# Plano de Integrações Futuras – Equidade+ Nova Stack

Mapeamento de possíveis integrações externas e requisitos preliminares, para manter o projeto preparado sem afetar o objetivo de funcionar totalmente na Railway.

## 1. Objetivos
- Garantir que a nova arquitetura suporte integrações quando necessário.
- Antecipar requisitos técnicos e de compliance.
- Facilitar planejamento do roadmap pós-reescrita.

## 2. Integrações Prioritárias (Sugestões)
| Integração | Objetivo | Requisitos | Status |
| --- | --- | --- | --- |
| Faturamento / NF-e | Emissão de notas para convênios/pacientes particulares | API REST terceirizada, envio de dados do atendimento | Avaliar fornecedores |
| BI / Data Warehouse | Dashboards avançados (PowerBI, Looker) | Exportação CSV ou connector Postgres read-only | Planejamento |
| Mensageria externa (WhatsApp/SMS) | Lembrete de agendamentos | Gateway (Twilio/Zenvia), consentimento LGPD | Estudar após go-live |
| Assinatura digital avançada | Evoluções com assinatura certificada | ICP-Brasil, armazenar hash de documento, integração com certificado | Avaliar custo |

## 3. Padrões de Integração
- Criar módulo dedicado (`integrations/`) no backend com services/DTOs específicos.
- Configurar feature flags para ativar por unidade.
- Manter logs/auditoria das chamadas externas.
- Timeouts e retries com circuit breaker (ex.: `opossum`).

## 4. Segurança & LGPD
- Analisar contratos e DPA (Data Processing Agreements).
- Realizar DPIA (Data Protection Impact Assessment) conforme necessidade.
- Minimizar dados enviados (princípio da minimização).
- Armazenar tokens/chaves em Railway Secrets separados.

## 5. Checklist antes de integrar
- [ ] ADR específico justificando e detalhando arquitetura.
- [ ] Estimativa de custo adicional.
- [ ] Plano de fallback (comportamento se serviço estiver indisponível).
- [ ] Testes automatizados cobrindo falhas de comunicação.
- [ ] Consentimento dos usuários (caso envolva comunicação externa).

## 6. Roadmap sugerido
- **Após go-live + estabilidade (3 meses)**:
  - Avaliar necessidade de BI; criar ETL simples (dump Postgres → S3).
- **6 meses**:
  - Iniciar piloto com faturamento se solicitado por clientes.
- **12 meses**:
  - Revisão de assinaturas digitais avançadas e mensageria externa.

---

Manter este plano como referência e atualizar conforme novas demandas surgirem.*** End Patch

