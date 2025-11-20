# Índice de Documentação - OpenEMR Customizado

## 📚 Documentação Completa

Este índice organiza toda a documentação criada para facilitar o desenvolvimento e adaptação do OpenEMR.

---

## 🎯 Documentos Principais

### 1. **README.md**
**Descrição**: Visão geral do projeto, objetivos e arquitetura  
**Quando usar**: Primeiro documento a ler para entender o projeto  
**Conteúdo**:
- Visão geral do OpenEMR
- Objetivos do projeto
- Stack tecnológica
- Módulos a manter/remover
- Estratégia de migração

---

### 2. **COMPONENTES_ESTRUTURA.md** ⭐
**Descrição**: Estrutura detalhada de componentes do OpenEMR  
**Quando usar**: Para entender quais componentes usar e onde estão localizados  
**Conteúdo**:
- Estrutura de diretórios completa
- Componentes a manter (com localização)
- Componentes a remover (com localização)
- Componentes a customizar
- Checklist de componentes
- Dependências entre componentes

---

### 3. **MAPEAMENTO_COMPONENTES.md** ⭐
**Descrição**: Mapeamento do sistema atual (Laravel) para OpenEMR  
**Quando usar**: Durante a migração para entender equivalências  
**Conteúdo**:
- Mapeamento completo por módulo
- Código de adaptação para cada componente
- Tabela de mapeamento
- Estratégia de migração por fases

---

### 4. **GUIA_CUSTOMIZACAO.md**
**Descrição**: Guia passo a passo para customizar o OpenEMR  
**Quando usar**: Ao customizar o sistema  
**Conteúdo**:
- Como desativar módulos
- Como remover módulos
- Como customizar roles
- Como adaptar interface
- Configurações de segurança

---

### 5. **GUIA_DESENVOLVIMENTO.md** ⭐
**Descrição**: Guia prático de desenvolvimento  
**Quando usar**: Durante o desenvolvimento de novas funcionalidades  
**Conteúdo**:
- Padrões de código OpenEMR
- Como criar componentes customizados
- Como modificar banco de dados
- Como implementar permissões
- Como customizar interface
- Testes

---

### 6. **MODULOS_DETALHADOS.md**
**Descrição**: Análise detalhada de cada módulo  
**Quando usar**: Para entender funcionalidades de cada módulo  
**Conteúdo**:
- Módulos a manter (detalhado)
- Módulos a remover (detalhado)
- Mapeamento com sistema atual
- Checklist de remoção
- Priorização

---

### 7. **SCRIPTS_REMOCAO.md** ⭐
**Descrição**: Scripts automatizados para remover componentes  
**Quando usar**: Ao remover módulos desnecessários  
**Conteúdo**:
- Scripts de remoção do banco
- Scripts de remoção de arquivos
- Scripts de limpeza de código
- Scripts de verificação
- Script completo de remoção

---

### 8. **SCRIPTS_SETUP.md**
**Descrição**: Scripts de setup e configuração  
**Quando usar**: Durante setup inicial e manutenção  
**Conteúdo**:
- Scripts de setup inicial
- Scripts de backup
- Scripts de configuração
- Scripts para Railway

---

### 9. **RAILWAY_SETUP.md**
**Descrição**: Guia completo de deploy no Railway  
**Quando usar**: Ao fazer deploy em produção  
**Conteúdo**:
- Configuração de serviços
- Variáveis de ambiente
- Volumes persistentes
- Troubleshooting

---

### 10. **PLANO_ACAO.md**
**Descrição**: Plano de ação completo do projeto  
**Quando usar**: Para planejamento e acompanhamento  
**Conteúdo**:
- Fases do projeto (8 fases)
- Cronograma estimado
- Métricas de sucesso
- Riscos e mitigações

---

## 🗂️ Organização por Tarefa

### Para Começar
1. **README.md** - Entender o projeto
2. **PLANO_ACAO.md** - Ver cronograma
3. **COMPONENTES_ESTRUTURA.md** - Entender estrutura

### Para Desenvolver
1. **GUIA_DESENVOLVIMENTO.md** - Padrões e práticas
2. **MAPEAMENTO_COMPONENTES.md** - Equivalências
3. **MODULOS_DETALHADOS.md** - Detalhes dos módulos

### Para Customizar
1. **GUIA_CUSTOMIZACAO.md** - Passo a passo
2. **COMPONENTES_ESTRUTURA.md** - O que customizar
3. **SCRIPTS_REMOCAO.md** - Remover componentes

### Para Deploy
1. **RAILWAY_SETUP.md** - Configuração Railway
2. **SCRIPTS_SETUP.md** - Scripts de setup
3. **railway.env.example** - Variáveis de ambiente

---

## 📋 Checklist de Uso

### Fase 1: Preparação
- [ ] Ler README.md
- [ ] Revisar PLANO_ACAO.md
- [ ] Estudar COMPONENTES_ESTRUTURA.md
- [ ] Configurar ambiente de desenvolvimento

### Fase 2: Análise
- [ ] Revisar MODULOS_DETALHADOS.md
- [ ] Estudar MAPEAMENTO_COMPONENTES.md
- [ ] Identificar componentes a manter/remover
- [ ] Criar plano de customização

### Fase 3: Remoção
- [ ] Fazer backup completo
- [ ] Usar SCRIPTS_REMOCAO.md
- [ ] Verificar dependências
- [ ] Testar sistema após remoção

### Fase 4: Customização
- [ ] Seguir GUIA_CUSTOMIZACAO.md
- [ ] Usar GUIA_DESENVOLVIMENTO.md
- [ ] Implementar customizações
- [ ] Testar cada customização

### Fase 5: Desenvolvimento
- [ ] Seguir padrões do GUIA_DESENVOLVIMENTO.md
- [ ] Usar MAPEAMENTO_COMPONENTES.md como referência
- [ ] Desenvolver funcionalidades
- [ ] Testar funcionalidades

### Fase 6: Deploy
- [ ] Seguir RAILWAY_SETUP.md
- [ ] Configurar variáveis de ambiente
- [ ] Usar SCRIPTS_SETUP.md
- [ ] Testar em produção

---

## 🔍 Busca Rápida

### "Como fazer X?"

| Tarefa | Documento | Seção |
|--------|-----------|-------|
| Remover módulo | SCRIPTS_REMOCAO.md | Scripts de remoção |
| Criar componente | GUIA_DESENVOLVIMENTO.md | Criando Componentes |
| Customizar menu | GUIA_CUSTOMIZACAO.md | Passo 5 |
| Deploy Railway | RAILWAY_SETUP.md | Passo 7 |
| Mapear componente | MAPEAMENTO_COMPONENTES.md | Mapeamento por Módulo |
| Entender estrutura | COMPONENTES_ESTRUTURA.md | Estrutura de Diretórios |
| Configurar permissões | GUIA_DESENVOLVIMENTO.md | Sistema de Permissões |
| Adaptar banco dados | GUIA_DESENVOLVIMENTO.md | Modificando Banco |

---

## 📝 Documentos de Referência

### Arquivos de Configuração
- `nixpacks.toml` - Configuração de build Railway
- `railway.env.example` - Exemplo de variáveis de ambiente

### Scripts
- Ver `SCRIPTS_SETUP.md` para scripts de setup
- Ver `SCRIPTS_REMOCAO.md` para scripts de remoção

---

## 🎯 Fluxo Recomendado de Leitura

### Para Desenvolvedores Novos no Projeto
1. README.md
2. COMPONENTES_ESTRUTURA.md
3. GUIA_DESENVOLVIMENTO.md
4. MAPEAMENTO_COMPONENTES.md

### Para Desenvolvedores Migrando Sistema
1. README.md
2. MAPEAMENTO_COMPONENTES.md
3. MODULOS_DETALHADOS.md
4. GUIA_CUSTOMIZACAO.md

### Para DevOps/Deploy
1. README.md
2. RAILWAY_SETUP.md
3. SCRIPTS_SETUP.md
4. railway.env.example

---

## 🔗 Links Úteis

### Documentação OpenEMR
- [OpenEMR Official](https://www.open-emr.org/)
- [OpenEMR Wiki](https://www.open-emr.org/wiki/)
- [OpenEMR GitHub](https://github.com/openemr/openemr)

### Railway
- [Railway Documentation](https://docs.railway.app/)
- [Railway PHP Guide](https://docs.railway.app/guides/php)

---

## 📞 Suporte

Para dúvidas sobre:
- **Estrutura do OpenEMR**: Ver COMPONENTES_ESTRUTURA.md
- **Desenvolvimento**: Ver GUIA_DESENVOLVIMENTO.md
- **Customização**: Ver GUIA_CUSTOMIZACAO.md
- **Deploy**: Ver RAILWAY_SETUP.md
- **Mapeamento**: Ver MAPEAMENTO_COMPONENTES.md

---

## ✅ Status da Documentação

- [x] README.md
- [x] COMPONENTES_ESTRUTURA.md
- [x] MAPEAMENTO_COMPONENTES.md
- [x] GUIA_CUSTOMIZACAO.md
- [x] GUIA_DESENVOLVIMENTO.md
- [x] MODULOS_DETALHADOS.md
- [x] SCRIPTS_REMOCAO.md
- [x] SCRIPTS_SETUP.md
- [x] RAILWAY_SETUP.md
- [x] PLANO_ACAO.md
- [x] nixpacks.toml
- [x] railway.env.example
- [x] INDICE_DOCUMENTACAO.md

---

**Última atualização**: [Data]  
**Versão**: 1.0

