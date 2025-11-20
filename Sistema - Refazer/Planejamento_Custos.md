# Planejamento de Custos – Equidade+ Nova Stack

Estimativa inicial dos custos operacionais considerando Railway, armazenamento externo e ferramentas acessórios.

## 1. Componentes de Custo
| Item | Serviço | Plano estimado | Valor mensal aproximado | Observações |
| --- | --- | --- | --- | --- |
| API (NestJS) | Railway | Starter (1 GB RAM) | US$ 20 | Escalar para 2 GB se necessário. |
| Worker (BullMQ) | Railway | Starter (512 MB) | US$ 10 | Processos de PDF/backup. |
| Web (Next.js) | Railway | Starter (1 GB) | US$ 20 | SSR moderado. |
| Admin (Next.js) | Railway | Starter (512 MB) | US$ 10 | Baixo tráfego. |
| Postgres | Railway | 1 vCPU / 1 GB / 10 GB | US$ 40 | Ajustar storage conforme crescimento. |
| Redis | Railway | 256 MB | US$ 10 | Filas e cache. |
| S3 (Wasabi/Backblaze) | Externo | 1 TB armazenamento | US$ 6 | US$ 0.0059/GB. Considerar egress. |
| CDN (opcional) | Cloudflare | Free | US$ 0 | Caso use para arquivos estáticos. |
| SMTP | Mailgun/Postmark | 10k emails | US$ 15 | Recuperação de senha, notificações. |
| Monitoramento (Sentry) | Team plan | US$ 29 | Opcional (10k eventos). |

Total estimado: **US$ 160–200 / mês** para produção inicial.

## 2. Ambiente Staging
- Railway pode usar plano gratuito/mais barato (menor CPU/RAM).
- Postgres menor (512 MB, 5 GB).
- S3 com storage compartilhado (dados mascarados).
- Custo estimado: US$ 40–60 / mês.

## 3. Ambiente Development
- Railway on-demand (pagar apenas se ativo) ou local (Docker).
- Custos adicionais somente quando subir ambientes temporários.

## 4. Crescimento e Escalabilidade
- **Usuários simultâneos**: se aumentar, considerar:
  - API: subir para plano 2 GB (~US$ 40).
  - Web: subir para plano 2 GB.
  - Postgres: mais CPU/storage (US$ 80+).
- **Armazenamento**:
  - 100 GB em S3 ≈ US$ 6/mês.
  - Monitorar egress (downloads) – incluir margem (US$ 5-10).
- **PDF e Jobs**:
  - Se geração pesada, separar worker dedicado 1 GB (US$ 20).

## 5. Ferramentas de Time
- Notion / Linear / Jira: dependerá do plano existente (não incluso).
- Figma: se não houver, considerar plano (US$ 12 / editor).

## 6. Reserva para Contingências
- Recomenda-se 20% de margem para incidentes, upgrades inesperados.
- Total com margem: **US$ 240 / mês**.

## 7. Otimizações
- Escalar horizontalmente apenas quando necessário.
- Automatizar desligamento de ambientes não utilizados.
- Limpar arquivos exportados (expiresAt) para reduzir storage.
- Monitorar uso de CPU para evitar overprovisioning.

## 8. Registro e Aprovação
- Documentar custos mensais e comparar com orçamento.
- Revisão trimestral com finanças.
- Ajustar plano conforme roadmap (ex.: novas integrações, suporte 24/7).

---

Atualize este documento conforme novos serviços forem adicionados ou valores mudarem.*** End Patch

