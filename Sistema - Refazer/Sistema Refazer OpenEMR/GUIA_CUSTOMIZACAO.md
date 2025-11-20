# Guia de Customização do OpenEMR

## 🎯 Objetivo

Este guia detalha como customizar o OpenEMR para atender às necessidades do sistema Equidade VPS, removendo módulos desnecessários e adaptando funcionalidades.

## 📋 Pré-requisitos

- OpenEMR instalado e funcionando
- Acesso ao banco de dados MySQL
- Acesso ao sistema de arquivos
- Backup completo realizado

## 🔧 Passo 1: Desativar Módulos via Interface

### 1.1 Acessar Administração de Módulos

1. Faça login como administrador
2. Navegue até: **Administração** > **Módulos** > **Gerenciar Módulos**
3. Você verá a lista de todos os módulos disponíveis

### 1.2 Desativar Módulos Indesejados

Para cada módulo a ser removido:
1. Localize o módulo na lista
2. Clique em **Desativar**
3. Confirme a ação

**Módulos a desativar**:
- Billing (Faturamento)
- Prescriptions (Prescrições)
- Labs (Laboratórios)
- Imaging (Imagens Médicas)
- Pharmacy (Farmácia)
- Telemedicine (Telemedicina)
- HL7 Integration

### 1.3 Verificar Desativação

Após desativar, verifique:
- [ ] Menu não mostra mais links para módulos desativados
- [ ] Sistema continua funcionando normalmente
- [ ] Funcionalidades essenciais estão intactas

---

## 🗑️ Passo 2: Remover Módulos do Sistema de Arquivos (Opcional)

**⚠️ ATENÇÃO**: Só faça isso após testar completamente a desativação via interface.

### 2.1 Localizar Arquivos dos Módulos

Os módulos geralmente estão em:
- `interface/[nome_do_modulo]/`
- `library/[nome_do_modulo]/`
- `sites/[site]/[nome_do_modulo]/`

### 2.2 Fazer Backup

```bash
# Criar backup antes de remover
cp -r interface/billing interface/billing.backup
cp -r interface/prescriptions interface/prescriptions.backup
# ... etc
```

### 2.3 Remover Pastas

```bash
# Remover módulos desativados
rm -rf interface/billing
rm -rf interface/prescriptions
rm -rf interface/labs
rm -rf interface/imaging
rm -rf interface/pharmacy
rm -rf interface/telemedicine
```

---

## 🗄️ Passo 3: Limpar Banco de Dados

### 3.1 Remover Entradas da Tabela `registry`

```sql
-- Conectar ao banco de dados
USE openemr;

-- Verificar módulos registrados
SELECT * FROM registry WHERE state = 0;

-- Remover módulos desativados (exemplo)
DELETE FROM registry WHERE directory = 'billing';
DELETE FROM registry WHERE directory = 'prescriptions';
DELETE FROM registry WHERE directory = 'labs';
DELETE FROM registry WHERE directory = 'imaging';
DELETE FROM registry WHERE directory = 'pharmacy';
DELETE FROM registry WHERE directory = 'telemedicine';
```

### 3.2 Remover Configurações Relacionadas

```sql
-- Remover configurações dos módulos removidos
DELETE FROM globals WHERE gl_name LIKE 'billing_%';
DELETE FROM globals WHERE gl_name LIKE 'prescriptions_%';
-- ... etc
```

### 3.3 Limpar Cache

```bash
# Limpar cache do OpenEMR
rm -rf sites/*/documents/cache/*
rm -rf sites/*/documents/temp/*
```

---

## 👥 Passo 4: Customizar Sistema de Roles e Permissões

### 4.1 Entender o Sistema ACL do OpenEMR

O OpenEMR usa um sistema ACL (Access Control List) diferente do sistema atual. É necessário mapear:

**Roles do Sistema Atual**:
- Admin
- Coordenador
- Profissional
- Secretária

**ACL do OpenEMR**:
- Administrators
- Physicians
- Nurses
- Receptionists
- etc.

### 4.2 Criar Grupos de Permissões Customizados

1. Acesse: **Administração** > **Usuários** > **Grupos de Acesso**
2. Crie novos grupos correspondentes aos roles:
   - `admin` (equivalente a Administrators)
   - `coordenador` (novo grupo)
   - `profissional` (equivalente a Physicians)
   - `secretaria` (equivalente a Receptionists)

### 4.3 Configurar Permissões por Grupo

Para cada grupo, configure:
- **Appointments**: Ver, Criar, Editar, Cancelar
- **Patients**: Ver, Criar, Editar
- **Clinical**: Ver, Criar, Editar, Revisar
- **Reports**: Ver (apenas para Admin e Coordenador)
- **Documents**: Ver, Upload

### 4.4 Implementar Filtro por Unidade

Como o OpenEMR não tem suporte nativo para "unidades", será necessário:

1. **Adicionar campo `unit_id`** nas tabelas relevantes
2. **Criar middleware** para filtrar por unidade
3. **Adaptar queries** para incluir filtro de unidade

**Exemplo de adaptação**:

```php
// Adicionar campo unit_id na tabela appointments
ALTER TABLE openemr.appointments ADD COLUMN unit_id INT;

// Criar tabela de unidades
CREATE TABLE units (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

// Adicionar unit_id aos usuários
ALTER TABLE openemr.users ADD COLUMN unit_id INT;
```

---

## 🎨 Passo 5: Customizar Interface

### 5.1 Remover Links do Menu

Edite o arquivo de menu principal:
- `interface/main/navigation.php`
- `interface/main/menu_data.php`

Remova ou comente links para módulos desativados:

```php
// Remover link de Billing
// $menu_item = array(
//     'label' => 'Billing',
//     'url' => 'billing/',
//     ...
// );
```

### 5.2 Personalizar Dashboard

1. Acesse: **Administração** > **Layout** > **Dashboard**
2. Remova widgets de módulos desativados
3. Adicione widgets relevantes:
   - Agendamentos do dia
   - Evoluções pendentes
   - Pacientes recentes

### 5.3 Simplificar Navegação

Reorganize o menu principal para mostrar apenas:
- Dashboard
- Agenda
- Pacientes
- Evoluções
- Relatórios (apenas Admin/Coordenador)
- Configurações

---

## 📊 Passo 6: Adaptar Relatórios

### 6.1 Remover Relatórios de Módulos Desativados

1. Acesse: **Relatórios** > **Gerenciar Relatórios**
2. Desative ou remova relatórios relacionados a:
   - Billing
   - Prescriptions
   - Labs
   - etc.

### 6.2 Criar Relatórios Customizados

Crie relatórios específicos para o sistema:
- Relatório de Agendamentos por Unidade
- Relatório de Evoluções Pendentes
- Relatório de Atendimentos por Profissional

---

## 🔐 Passo 7: Configurar Segurança

### 7.1 Revisar Permissões de Arquivos

```bash
# Configurar permissões corretas
chmod 755 interface/
chmod 644 interface/*.php
chmod 755 sites/
chmod 700 sites/*/documents/
```

### 7.2 Configurar HTTPS

No Railway, configure:
- Certificado SSL automático
- Redirecionamento HTTP → HTTPS
- Headers de segurança

### 7.3 Revisar Configurações de Segurança

Acesse: **Administração** > **Configurações** > **Segurança**

Configure:
- Política de senhas
- Timeout de sessão
- Logs de auditoria
- Proteção contra CSRF

---

## ✅ Passo 8: Testes

### 8.1 Testes Funcionais

Teste cada funcionalidade mantida:
- [ ] Login com diferentes roles
- [ ] Agendamentos (criar, editar, cancelar)
- [ ] Cadastro de pacientes
- [ ] Evoluções (criar, editar, finalizar)
- [ ] Upload de documentos
- [ ] Relatórios
- [ ] Permissões por role

### 8.2 Testes de Performance

- [ ] Tempo de carregamento das páginas
- [ ] Performance de queries
- [ ] Uso de memória
- [ ] Tempo de resposta do banco

### 8.3 Testes de Segurança

- [ ] Acesso não autorizado bloqueado
- [ ] Filtros de unidade funcionando
- [ ] Permissões respeitadas
- [ ] Logs de auditoria funcionando

---

## 📝 Passo 9: Documentação

### 9.1 Documentar Customizações

Crie um documento listando:
- Módulos removidos
- Customizações realizadas
- Alterações no banco de dados
- Arquivos modificados
- Configurações alteradas

### 9.2 Criar Guia de Uso

Documente:
- Como usar cada funcionalidade
- Permissões por role
- Fluxos de trabalho
- Troubleshooting

---

## 🚀 Passo 10: Preparar para Deploy

### 10.1 Verificar Configurações

- [ ] Variáveis de ambiente configuradas
- [ ] Banco de dados configurado
- [ ] Permissões de arquivos corretas
- [ ] Cache limpo

### 10.2 Testar em Ambiente de Staging

Antes de fazer deploy em produção:
1. Teste em ambiente de staging
2. Valide todas as funcionalidades
3. Teste migração de dados (se aplicável)

### 10.3 Preparar Scripts de Deploy

Veja `RAILWAY_SETUP.md` para configuração no Railway.

---

## 🔄 Manutenção Contínua

### Atualizações do OpenEMR

Ao atualizar o OpenEMR:
1. **Fazer backup completo**
2. **Aplicar atualização**
3. **Reaplicar customizações** (se necessário)
4. **Testar funcionalidades**
5. **Atualizar documentação**

### Monitoramento

Monitore:
- Logs de erro
- Performance
- Uso de recursos
- Segurança

---

## ⚠️ Avisos Importantes

1. **Sempre faça backup** antes de qualquer alteração
2. **Teste em ambiente de desenvolvimento** primeiro
3. **Documente todas as alterações**
4. **Mantenha atualizações de segurança**
5. **Não remova módulos críticos** sem entender dependências

---

## 📚 Recursos Adicionais

- [OpenEMR Documentation](https://www.open-emr.org/wiki/)
- [OpenEMR Customization Guide](https://www.open-emr.org/wiki/index.php/Customization)
- [OpenEMR ACL System](https://www.open-emr.org/wiki/index.php/ACL_System)

