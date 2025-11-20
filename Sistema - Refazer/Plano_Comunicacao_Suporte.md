# Plano de Comunicação & Suporte – Reescrita Equidade+

## 1. Objetivos
- Garantir comunicação clara durante migração e pós-go-live.
- Definir canais de suporte (interno/externo) e tempos de resposta.
- Estabelecer rotina de acompanhamento com stakeholders clínicos.

## 2. Canais
| Canal | Propósito | Responsável |
| --- | --- | --- |
| Slack #equidade-rewrite | comunicação diária da equipe técnica | Tech Lead |
| Slack #equidade-operacao | dúvidas de secretaria/coordenadores | PM / Coordenação clínica |
| Email suporte@equidade.com | contato oficial com clientes | Suporte N1 |
| Status page (Notion/Website) | avisos de manutenção e incidentes | Admin |
| Reuniões semanais | alinhamento de progresso | PM + Stakeholders |

## 3. Suporte (Níveis)
| Nível | Escopo | Responsável | SLA resposta | SLA resolução |
| --- | --- | --- | --- | --- |
| N1 | Dúvidas operacionais, senha, navegação | Suporte interno | 30 min | 4 h |
| N2 | Problemas técnicos, bugs, integrações | Equipe técnica | 1 h | 24 h |
| N3 | Falhas críticas, indisponibilidade | Tech Lead + Admin | 15 min | 4 h |

## 4. Fluxo de Atendimento
1. Usuário abre ticket via Slack ou email.
2. Suporte N1 registra no sistema de chamados (Linear/Jira).
3. N1 resolve ou escala para N2 conforme severidade.
4. N2 investiga, envolve devs responsáveis, atualiza ticket.
5. Em incidentes críticos, abrir war room (call) e atualizar status page.

## 5. Comunicação de Migração
- **T-7 dias**: enviar comunicado sobre janela de manutenção, benefícios da nova versão.
- **T-1 dia**: lembrete com horário exato, instruções (backup, modo manutenção).
- **Go-live**: anunciar disponibilidade e mudanças principais.
- **Pós-go-live (T+1)**: envio de FAQ com novas funcionalidades e link para manual.
- **Feedback**: formulário (Google Forms) após 2 semanas.

## 6. Documentação para Usuários
- Manual (`Manual_Usuarios.md`) convertido em PDF/Slides.
- Vídeos curtos (loom) mostrando principais fluxos.
- Página de FAQ com perguntas frequentes.
- Link fixo no sistema (menu ajuda).

## 7. Monitoramento e Alertas
- Alertas automáticos (Railway, Sentry) enviando email/Slack.
- Dashboard com métricas de uso (acessos/dia, erros, tempo de resposta).
- Reuniões diárias de hypercare durante 2 semanas pós-go-live.

## 8. Indicadores de Sucesso
- Tempo médio de resposta a chamados.
- Número de bugs críticos pós-go-live.
- Taxa de adoção das novas funcionalidades.
- NPS ou satisfação dos usuários após 1 mês.

## 9. Pós-migração
- Reunião de retrospectiva com equipe clínica e técnica.
- Consolidar lições aprendidas e atualizar processos.
- Planejar roadmap de melhorias com base no feedback coletado.

---

Atualize este plano conforme novos canais/equipe forem definidos e mantenha alinhado com `Governanca_e_Processos.md`.*** End Patch

