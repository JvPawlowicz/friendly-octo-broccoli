# OpenEMR Customizado - Equidade VPS

## 📋 Visão Geral

Este projeto utiliza o **OpenEMR** como base open source para o sistema de gestão de saúde da Equidade VPS. O objetivo é aproveitar a robustez e funcionalidades do OpenEMR, removendo módulos desnecessários e mantendo apenas o essencial para as necessidades do sistema.

## 🎯 Objetivos

1. **Utilizar OpenEMR como base**: Sistema open source maduro e amplamente utilizado
2. **Remover módulos desnecessários**: Manter apenas funcionalidades essenciais
3. **Manter deploy no Railway**: Preservar a infraestrutura atual de deploy
4. **Customização mínima**: Ajustar apenas o necessário para atender aos requisitos

## 🏗️ Arquitetura

### Stack Tecnológica
- **Backend**: PHP (OpenEMR é baseado em PHP)
- **Banco de Dados**: MySQL/MariaDB
- **Frontend**: HTML/CSS/JavaScript (jQuery) - Interface nativa do OpenEMR
- **Deploy**: Railway (com suporte a PHP e MySQL)

### Estrutura do OpenEMR
```
openemr/
├── interface/          # Interface do usuário
│   ├── main/          # Interface principal
│   ├── forms/         # Formulários customizados
│   └── modules/       # Módulos adicionais
├── library/           # Bibliotecas e classes
├── sql/               # Scripts SQL
├── sites/             # Configurações por site
└── documents/         # Documentos dos pacientes
```

## 📦 Módulos a Manter

Baseado nas necessidades do sistema atual (ver `Roles_Permissoes_Detalhadas.md`):

### ✅ Módulos Essenciais
- **Agendamentos (Appointments)**: Gestão de agenda e consultas
- **Pacientes (Patients)**: Cadastro e gestão de pacientes
- **Prontuário Eletrônico (EHR)**: Evoluções e avaliações
- **Usuários e Permissões**: Sistema de roles (Admin, Coordenador, Profissional, Secretária)
- **Relatórios Básicos**: Relatórios essenciais
- **Documentos**: Upload e gestão de documentos

### ❌ Módulos a Remover/Desativar
- **Faturamento (Billing)**: Se não for necessário
- **Prescrições (Prescriptions)**: Se não for necessário
- **Laboratórios (Labs)**: Se não for necessário
- **Imagens (Imaging)**: Se não for necessário
- **Farmácia (Pharmacy)**: Se não for necessário
- **Telemedicina (Telemedicine)**: Se não for necessário
- **Módulos de Integração**: APIs externas não utilizadas

## 🚀 Deploy no Railway

### Pré-requisitos
- Conta no Railway
- Repositório Git configurado
- Banco de dados MySQL/MariaDB no Railway

### Configuração
1. **Criar serviço PHP** no Railway
2. **Criar serviço MySQL** no Railway
3. **Configurar variáveis de ambiente** (ver `railway.env.example`)
4. **Deploy automático** via Git push

## 📚 Documentação Completa

### 📖 Documentos Principais
- **[Índice de Documentação](./INDICE_DOCUMENTACAO.md)** ⭐ - Comece aqui para navegar toda a documentação
- **[Estrutura de Componentes](./COMPONENTES_ESTRUTURA.md)** ⭐ - Estrutura detalhada de componentes do OpenEMR
- **[Mapeamento de Componentes](./MAPEAMENTO_COMPONENTES.md)** ⭐ - Mapeamento do sistema atual para OpenEMR
- **[Guia de Desenvolvimento](./GUIA_DESENVOLVIMENTO.md)** ⭐ - Guia prático de desenvolvimento

### 🔧 Guias de Customização
- [Guia de Customização](./GUIA_CUSTOMIZACAO.md) - Passo a passo para customizar o OpenEMR
- [Módulos Detalhados](./MODULOS_DETALHADOS.md) - Análise detalhada de cada módulo
- [Scripts de Remoção](./SCRIPTS_REMOCAO.md) - Scripts automatizados para remover componentes

### 🚀 Deploy e Setup
- [Configuração Railway](./RAILWAY_SETUP.md) - Guia completo de deploy no Railway
- [Scripts de Setup](./SCRIPTS_SETUP.md) - Scripts de setup e configuração
- [Plano de Ação](./PLANO_ACAO.md) - Plano completo do projeto (8 fases)

### ⚙️ Configurações
- [nixpacks.toml](./nixpacks.toml) - Configuração de build para Railway
- [railway.env.example](./railway.env.example) - Exemplo de variáveis de ambiente

## 🔄 Migração do Sistema Atual

### Dados a Migrar
- Usuários e permissões
- Pacientes
- Agendamentos
- Evoluções/Avaliações
- Documentos

### Estratégia
1. **Fase 1**: Setup do OpenEMR limpo
2. **Fase 2**: Remoção de módulos desnecessários
3. **Fase 3**: Customização de interface e roles
4. **Fase 4**: Migração de dados
5. **Fase 5**: Testes e validação
6. **Fase 6**: Deploy em produção

## 📝 Notas Importantes

- **Backup**: Sempre fazer backup antes de remover módulos
- **Testes**: Testar cada remoção de módulo isoladamente
- **Documentação**: Documentar todas as customizações realizadas
- **Segurança**: Manter atualizações de segurança do OpenEMR

## 🔗 Links Úteis

- [OpenEMR Official](https://www.open-emr.org/)
- [OpenEMR Documentation](https://www.open-emr.org/wiki/)
- [OpenEMR GitHub](https://github.com/openemr/openemr)
- [Railway Documentation](https://docs.railway.app/)

