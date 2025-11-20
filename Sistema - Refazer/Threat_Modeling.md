# Threat Modeling – Equidade+ Nova Stack

## 1. Metodologia
- Baseado em STRIDE (Spoofing, Tampering, Repudiation, Information disclosure, Denial of service, Elevation of privilege).
- Avaliar componentes principais: Autenticação, API, Frontend, Banco de dados, Storage, Fila/Workers.

## 2. Ativos Críticos
- Dados clínicos (evoluções, avaliações, documentos).
- Credenciais de usuários e tokens.
- Configurações de sistema (branding, preferências).
- Logs de auditoria e backups.

## 3. Superfícies de Ataque
| Componente | Vetores |
| --- | --- |
| Frontend | Inputs (formularios, rich text), downloads, cookies. |
| API REST | Endpoints públicos (auth), upload de arquivos, exportações. |
| Banco de dados | Injeção via ORM, credenciais vazadas. |
| Storage S3 | URLs públicas, listas de objetos. |
| Autenticação | Força bruta, phishing, reutilização tokens. |
| Infra Railway | Acesso não autorizado ao painel, tokens expostos. |

## 4. Ameaças Identificadas & Mitigações
| Ameaça | Categoria | Mitigação |
| --- | --- | --- |
| Força bruta no login | Spoofing | Rate limiting, lock temporário, logging. |
| Acesso indevido a paciente de outra unidade | Elevation | Guard unit scope + testes. |
| Upload de arquivo malicioso | Tampering | Validação MIME, limite tamanho, antivírus. |
| XSS via rich text | Information disclosure | Sanitização com whitelist (DOMPurify). |
| CSRF via cookies | Tampering | SameSite Lax, tokens anti-CSRF (se necessário). |
| Sequestro de sessão | Spoofing | HttpOnly cookies, expiração curta, invalidar refresh. |
| Vazamento de documento S3 | Information disclosure | URLs assinadas, policies restritas. |
| DOS via exportações massivas | DoS | Throttling, fila com limites, monitoramento. |
| Alteração de logs | Repudiation | Logs append-only, somente admin, auditoria. |
| Injeção SQL | Tampering | Prisma preparado, validação Zod. |
| Credentials Hardcoded | Elevation | Secrets apenas via Railway, sem commit. |

## 5. Cenários Especiais
- **Usuário mal-intencionado interno**: auditoria detalhada, controle de permissões, alerts.
- **Perda de dados**: backups diários + restore testado.
- **Incidente de segurança**: seguir plano em `Plano_Comunicacao_Suporte.md`, notificar stakeholders.

## 6. Plano de Ação
- [ ] Revisar e validar mitigação antes do go-live.
- [ ] Executar pentest focado em auth, uploads, relatórios.
- [ ] Monitorar continuamente (Sentry, logs).
- [ ] Atualizar threat model a cada nova funcionalidade ou integração.

---

Documento complementar ao `Checklist_Seguranca_Avancada.md`.*** End Patch

