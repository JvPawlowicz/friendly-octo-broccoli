# Plano de Ação - Migração para OpenEMR

## 🎯 Objetivo

Migrar o sistema Equidade VPS para utilizar o OpenEMR como base, removendo módulos desnecessários e mantendo apenas funcionalidades essenciais, com deploy no Railway.

## 📅 Fases do Projeto

### Fase 1: Preparação e Setup (Semana 1-2)

#### 1.1 Download e Instalação Local
- [ ] Baixar OpenEMR mais recente do GitHub
- [ ] Instalar em ambiente local (Docker ou XAMPP)
- [ ] Configurar banco de dados local
- [ ] Testar instalação básica
- [ ] Documentar versão utilizada

#### 1.2 Análise de Funcionalidades
- [ ] Mapear funcionalidades do sistema atual
- [ ] Comparar com módulos do OpenEMR
- [ ] Identificar gaps e necessidades de customização
- [ ] Criar lista definitiva de módulos a manter/remover

#### 1.3 Setup do Repositório
- [ ] Criar repositório Git para o projeto OpenEMR customizado
- [ ] Configurar estrutura de pastas
- [ ] Adicionar documentação criada
- [ ] Configurar .gitignore apropriado

**Entregáveis**:
- OpenEMR funcionando localmente
- Documentação de análise completa
- Repositório Git configurado

---

### Fase 2: Customização Básica (Semana 3-4)

#### 2.1 Remoção de Módulos
- [ ] Fazer backup completo
- [ ] Desativar módulos via interface admin
- [ ] Testar sistema após desativação
- [ ] Remover arquivos de módulos desativados (opcional)
- [ ] Limpar banco de dados
- [ ] Documentar módulos removidos

#### 2.2 Customização de Interface
- [ ] Remover links de menu para módulos desativados
- [ ] Simplificar navegação
- [ ] Personalizar dashboard
- [ ] Ajustar cores e branding (se necessário)

#### 2.3 Configuração Inicial
- [ ] Configurar timezone (America/Sao_Paulo)
- [ ] Configurar locale (pt_BR)
- [ ] Configurar limites de upload
- [ ] Configurar segurança básica

**Entregáveis**:
- OpenEMR com módulos desnecessários removidos
- Interface simplificada
- Sistema funcional e testado

---

### Fase 3: Customização de Roles e Permissões (Semana 5-6)

#### 3.1 Mapeamento de Roles
- [ ] Analisar sistema ACL do OpenEMR
- [ ] Mapear roles do sistema atual para ACL do OpenEMR
- [ ] Criar grupos de permissões customizados:
  - Admin
  - Coordenador
  - Profissional
  - Secretária

#### 3.2 Implementação de Unidades
- [ ] Criar tabela `units` no banco de dados
- [ ] Adicionar campo `unit_id` nas tabelas relevantes:
  - users
  - appointments
  - patients
  - evolutions
- [ ] Criar middleware para filtro por unidade
- [ ] Adaptar queries para incluir filtro de unidade

#### 3.3 Configuração de Permissões
- [ ] Configurar permissões por grupo
- [ ] Testar acesso por role
- [ ] Validar filtros de unidade
- [ ] Documentar permissões configuradas

**Entregáveis**:
- Sistema de roles funcionando
- Filtros por unidade implementados
- Permissões testadas e validadas

---

### Fase 4: Adaptação de Funcionalidades (Semana 7-8)

#### 4.1 Agendamentos
- [ ] Adaptar sistema de agendamentos
- [ ] Implementar gestão de salas
- [ ] Configurar bloqueios de horário
- [ ] Implementar feriados e indisponibilidades
- [ ] Testar fluxo completo de agendamentos

#### 4.2 Pacientes
- [ ] Adaptar cadastro de pacientes
- [ ] Configurar campos necessários
- [ ] Implementar upload de documentos
- [ ] Configurar timeline de eventos
- [ ] Testar gestão completa de pacientes

#### 4.3 Evoluções e Avaliações
- [ ] Adaptar sistema de evoluções
- [ ] Configurar templates de avaliação
- [ ] Implementar sistema de revisão
- [ ] Configurar workflow de aprovação
- [ ] Testar fluxo completo

#### 4.4 Relatórios
- [ ] Remover relatórios de módulos desativados
- [ ] Criar relatórios customizados:
  - Agendamentos por unidade
  - Evoluções pendentes
  - Atendimentos por profissional
- [ ] Configurar permissões de acesso
- [ ] Testar geração de relatórios

**Entregáveis**:
- Todas as funcionalidades adaptadas
- Fluxos de trabalho testados
- Sistema funcional completo

---

### Fase 5: Setup no Railway (Semana 9-10)

#### 5.1 Preparação
- [ ] Criar projeto no Railway
- [ ] Configurar repositório Git
- [ ] Criar serviço MySQL
- [ ] Configurar variáveis de ambiente

#### 5.2 Configuração de Deploy
- [ ] Configurar nixpacks.toml ou Dockerfile
- [ ] Configurar scripts de inicialização
- [ ] Configurar volume de armazenamento
- [ ] Configurar healthcheck

#### 5.3 Deploy Inicial
- [ ] Fazer primeiro deploy
- [ ] Executar instalação do OpenEMR
- [ ] Configurar banco de dados
- [ ] Testar acesso básico
- [ ] Configurar domínio (se necessário)

#### 5.4 Configuração de Produção
- [ ] Configurar HTTPS
- [ ] Configurar segurança
- [ ] Configurar backups automáticos
- [ ] Configurar monitoramento
- [ ] Documentar processo de deploy

**Entregáveis**:
- Sistema funcionando no Railway
- Deploy automatizado configurado
- Documentação de deploy completa

---

### Fase 6: Migração de Dados (Semana 11-12)

#### 6.1 Preparação
- [ ] Fazer backup completo do sistema atual
- [ ] Analisar estrutura de dados atual
- [ ] Mapear campos entre sistemas
- [ ] Criar scripts de migração

#### 6.2 Migração
- [ ] Migrar usuários e permissões
- [ ] Migrar pacientes
- [ ] Migrar agendamentos
- [ ] Migrar evoluções/avaliações
- [ ] Migrar documentos

#### 6.3 Validação
- [ ] Validar integridade dos dados
- [ ] Comparar contagens
- [ ] Testar funcionalidades com dados migrados
- [ ] Corrigir inconsistências

**Entregáveis**:
- Dados migrados com sucesso
- Validação completa
- Sistema pronto para uso

---

### Fase 7: Testes e Validação (Semana 13-14)

#### 7.1 Testes Funcionais
- [ ] Testar todas as funcionalidades
- [ ] Testar por role (Admin, Coordenador, Profissional, Secretária)
- [ ] Testar filtros de unidade
- [ ] Testar permissões
- [ ] Documentar bugs encontrados

#### 7.2 Testes de Performance
- [ ] Testar tempo de carregamento
- [ ] Testar queries do banco
- [ ] Testar upload de arquivos
- [ ] Otimizar se necessário

#### 7.3 Testes de Segurança
- [ ] Testar controle de acesso
- [ ] Testar proteção contra SQL injection
- [ ] Testar proteção contra XSS
- [ ] Revisar logs de auditoria

#### 7.4 Testes de Usabilidade
- [ ] Testar com usuários reais
- [ ] Coletar feedback
- [ ] Ajustar interface conforme necessário
- [ ] Criar guia de uso

**Entregáveis**:
- Sistema testado e validado
- Bugs corrigidos
- Documentação de testes

---

### Fase 8: Go-Live e Suporte (Semana 15+)

#### 8.1 Preparação para Produção
- [ ] Fazer backup final do sistema antigo
- [ ] Preparar plano de rollback
- [ ] Comunicar mudança aos usuários
- [ ] Preparar treinamento

#### 8.2 Go-Live
- [ ] Executar migração final
- [ ] Ativar sistema em produção
- [ ] Monitorar logs e erros
- [ ] Suporte imediato aos usuários

#### 8.3 Pós Go-Live
- [ ] Coletar feedback inicial
- [ ] Corrigir problemas críticos
- [ ] Otimizar conforme necessário
- [ ] Documentar lições aprendidas

**Entregáveis**:
- Sistema em produção
- Usuários treinados
- Suporte funcionando

---

## 📊 Métricas de Sucesso

### Funcionalidades
- [ ] 100% das funcionalidades essenciais funcionando
- [ ] Todos os roles com permissões corretas
- [ ] Filtros de unidade funcionando

### Performance
- [ ] Tempo de carregamento < 3 segundos
- [ ] Queries otimizadas
- [ ] Sistema responsivo

### Segurança
- [ ] Sem vulnerabilidades críticas
- [ ] Logs de auditoria funcionando
- [ ] Backups automáticos configurados

### Usabilidade
- [ ] Interface intuitiva
- [ ] Usuários conseguem usar sem treinamento extenso
- [ ] Feedback positivo dos usuários

---

## 🚨 Riscos e Mitigações

### Risco 1: Perda de Dados na Migração
**Mitigação**:
- Múltiplos backups
- Testes extensivos em ambiente de staging
- Plano de rollback preparado

### Risco 2: Funcionalidades Não Disponíveis no OpenEMR
**Mitigação**:
- Análise detalhada na Fase 1
- Identificar gaps cedo
- Planejar customizações necessárias

### Risco 3: Problemas de Performance
**Mitigação**:
- Testes de carga
- Otimização de queries
- Monitoramento contínuo

### Risco 4: Resistência dos Usuários
**Mitigação**:
- Comunicação clara
- Treinamento adequado
- Suporte durante transição

---

## 📚 Recursos Necessários

### Equipe
- 1 Desenvolvedor Backend (PHP)
- 1 DBA (para migração de dados)
- 1 QA (para testes)
- 1 DevOps (para Railway)

### Ferramentas
- Ambiente de desenvolvimento local
- Ambiente de staging
- Conta Railway
- Ferramentas de backup
- Ferramentas de monitoramento

### Tempo Estimado
- **Total**: 15-16 semanas
- **Desenvolvimento**: 10-11 semanas
- **Testes**: 2 semanas
- **Migração e Go-Live**: 2-3 semanas

---

## 📝 Documentação a Manter

- [ ] README principal
- [ ] Guia de customização
- [ ] Documentação de módulos
- [ ] Guia de deploy no Railway
- [ ] Scripts de setup
- [ ] Guia de migração de dados
- [ ] Manual do usuário
- [ ] Troubleshooting guide

---

## 🔄 Próximos Passos Imediatos

1. **Revisar este plano** com a equipe
2. **Ajustar cronograma** conforme necessário
3. **Iniciar Fase 1**: Download e instalação do OpenEMR
4. **Configurar ambiente** de desenvolvimento
5. **Começar análise** de funcionalidades

---

## 📞 Contatos e Suporte

- **Documentação OpenEMR**: https://www.open-emr.org/wiki/
- **Comunidade OpenEMR**: https://www.open-emr.org/forum/
- **Railway Support**: https://docs.railway.app/

---

**Última atualização**: [Data]
**Versão**: 1.0
**Status**: Planejamento

