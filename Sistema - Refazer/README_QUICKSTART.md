# Quickstart de Reescrita – Equidade+

Resumo para qualquer pessoa entender rapidamente o projeto de reescrita e encontrar os artefatos principais.

## 1. Visão Geral
- **Blueprint principal**: `Blueprint_Nova_Stack.md`
- **Arquitetura modular**: `Modulos_Componentes_Rotas.md`, `Modulos_Arquitetura_Detalhada.md`
- **Stack técnica**: `Guia_Desenvolvimento_Frontend.md`, `Guia_Desenvolvimento_Backend.md`
- **Dados & Auth**: `Modelo_Dados_ER.md`, `Blueprint_DB_Auth.md`
- **Roles & Permissões**: `Roles_Permissoes_Detalhadas.md` ⭐ **IMPORTANTE**: Define claramente o que cada role pode fazer. Admin tem acesso TOTAL.
- **Processos**: `Cronograma_Detalhado.md`, `Governanca_e_Processos.md`, `Checklist_GoLive.md`

## 2. Começando
1. Ler `Blueprint_Nova_Stack.md` para ver visão macro.
2. Configurar monorepo seguindo `Setup_Monorepo_e_Ambientes.md`.
3. Consultar guias front/back para implementar features.
4. Usar `Plano_Testes.md` e `Roteiros_QA_Manual.md` para validar.

## 3. Mapa de Arquivos Essenciais
| Tema | Arquivo |
| --- | --- |
| Stack e módulos | `Blueprint_Nova_Stack.md` |
| Rotas e componentes | `Modulos_Componentes_Rotas.md` |
| Arquitetura detalhada | `Modulos_Arquitetura_Detalhada.md` |
| UX e journeys | `Mapa_Telas_UX.md`, `User_Journeys_Roles.md`, `Design_Wireframes.md` |
| Dados & Auth | `Modelo_Dados_ER.md`, `Blueprint_DB_Auth.md`, `Plano_Dados_e_Migracao.md` |
| Roles & Permissões | `Roles_Permissoes_Detalhadas.md` ⭐ |
| Desenvolvimento | `Guia_Desenvolvimento_Frontend.md`, `Guia_Desenvolvimento_Backend.md`, `Padroes_Codigo_Qualidade.md` |
| Ambientes | `Guia_Ambientes_Variaveis.md` |
| Operação | `Cronograma_Detalhado.md`, `Checklist_GoLive.md`, `Plano_Comunicacao_Suporte.md` |
| QA & Segurança | `Plano_Testes.md`, `Roteiros_QA_Manual.md`, `Checklist_Seguranca_Avancada.md` |
| Estimativas | `Estimativa_Tempo_Desenvolvimento.md` ⭐ (tempo real com Cursor) |
| Pré-Dev | `Checklist_Pre_Desenvolvimento.md` (o que falta detalhar?) |

## 4. Fluxo Sugerido
1. **Discovery**: validar requisitos → blueprint/journeys.
2. **Setup**: monorepo + CI/CD → `Setup_Monorepo_e_Ambientes.md`, `.github/workflows`.
3. **Implementação**: seguir roadmap → documentos de módulos.
4. **Dados**: preparar migração → `Plano_Dados_e_Migracao.md`.
5. **QA**: testes automatizados + manuais → `Plano_Testes.md`, `Roteiros_QA_Manual.md`.
6. **Go-live**: usar checklists, plano de comunicação, segurança.

## 5. Contatos & Suporte
- Canais de suporte definidos em `Plano_Comunicacao_Suporte.md`.
- Decisões técnicas em `Sistema - Refazer/ADRs`.

Mantenha este quickstart atualizado sempre que novos documentos forem adicionados ou reorganizados.*** End Patch

