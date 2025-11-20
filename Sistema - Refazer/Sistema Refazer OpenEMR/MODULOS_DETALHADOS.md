# Módulos OpenEMR - Análise Detalhada

## 📊 Mapeamento de Funcionalidades

Este documento mapeia as funcionalidades do sistema atual (Equidade VPS) com os módulos do OpenEMR.

## ✅ Módulos a MANTER

### 1. **Appointments (Agendamentos)**
**Status**: ✅ MANTER  
**Prioridade**: CRÍTICA  
**Funcionalidades**:
- Gestão de agenda
- Agendamentos por profissional
- Agendamentos por sala
- Bloqueios de horário
- Feriados e indisponibilidades

**Mapeamento com sistema atual**:
- ✅ Agenda completa
- ✅ Filtros por unidade
- ✅ Gestão de salas
- ✅ Status de agendamentos

**Localização**: `interface/main/calendar/`

---

### 2. **Patients (Pacientes)**
**Status**: ✅ MANTER  
**Prioridade**: CRÍTICA  
**Funcionalidades**:
- Cadastro de pacientes
- Prontuário eletrônico
- Histórico de atendimentos
- Documentos do paciente
- Timeline de eventos

**Mapeamento com sistema atual**:
- ✅ Cadastro completo
- ✅ Prontuário
- ✅ Upload de documentos
- ✅ Timeline

**Localização**: `interface/patient_file/`

---

### 3. **Clinical (Clínico)**
**Status**: ✅ MANTER  
**Prioridade**: CRÍTICA  
**Funcionalidades**:
- Evoluções clínicas
- Avaliações
- Notas clínicas
- Histórico clínico

**Mapeamento com sistema atual**:
- ✅ Evoluções
- ✅ Avaliações
- ✅ Revisão de evoluções
- ✅ Templates de avaliação

**Localização**: `interface/forms/`

---

### 4. **Users & Access Control (Usuários e Permissões)**
**Status**: ✅ MANTER  
**Prioridade**: CRÍTICA  
**Funcionalidades**:
- Gestão de usuários
- Sistema de roles
- Permissões granulares
- ACL (Access Control List)

**Mapeamento com sistema atual**:
- ✅ Roles: Admin, Coordenador, Profissional, Secretária
- ✅ Permissões por unidade
- ✅ Controle de acesso

**Localização**: `interface/main/users/`

**Customização Necessária**:
- Adaptar roles do OpenEMR para os roles do sistema
- Configurar permissões por unidade

---

### 5. **Reports (Relatórios)**
**Status**: ✅ MANTER  
**Prioridade**: MÉDIA  
**Funcionalidades**:
- Relatórios básicos
- Relatórios de agendamentos
- Relatórios de pacientes
- Relatórios clínicos

**Mapeamento com sistema atual**:
- ✅ Relatórios de agenda
- ✅ Relatórios de atendimentos
- ✅ Relatórios por unidade

**Localização**: `interface/reports/`

---

### 6. **Documents (Documentos)**
**Status**: ✅ MANTER  
**Prioridade**: MÉDIA  
**Funcionalidades**:
- Upload de documentos
- Gestão de documentos
- Categorização
- Download seguro

**Mapeamento com sistema atual**:
- ✅ Upload de documentos
- ✅ Gestão por paciente
- ✅ Categorização

**Localização**: `interface/filemanager/`

---

## ❌ Módulos a REMOVER/DESATIVAR

### 1. **Billing (Faturamento)**
**Status**: ❌ REMOVER  
**Razão**: Não utilizado no sistema atual  
**Localização**: `interface/billing/`  
**Ação**: Desativar via interface admin ou remover arquivos

---

### 2. **Prescriptions (Prescrições)**
**Status**: ❌ REMOVER  
**Razão**: Não utilizado no sistema atual  
**Localização**: `interface/prescriptions/`  
**Ação**: Desativar via interface admin

---

### 3. **Labs (Laboratórios)**
**Status**: ❌ REMOVER  
**Razão**: Não utilizado no sistema atual  
**Localização**: `interface/labs/`  
**Ação**: Desativar via interface admin

---

### 4. **Imaging (Imagens Médicas)**
**Status**: ❌ REMOVER  
**Razão**: Não utilizado no sistema atual  
**Localização**: `interface/imaging/`  
**Ação**: Desativar via interface admin

---

### 5. **Pharmacy (Farmácia)**
**Status**: ❌ REMOVER  
**Razão**: Não utilizado no sistema atual  
**Localização**: `interface/pharmacy/`  
**Ação**: Desativar via interface admin

---

### 6. **Telemedicine (Telemedicina)**
**Status**: ❌ REMOVER  
**Razão**: Não utilizado no sistema atual  
**Localização**: `interface/telemedicine/`  
**Ação**: Desativar via interface admin

---

### 7. **HL7 Integration**
**Status**: ❌ REMOVER  
**Razão**: Não utilizado no sistema atual  
**Localização**: `interface/hl7/`  
**Ação**: Desativar via interface admin

---

### 8. **FHIR API**
**Status**: ⚠️ AVALIAR  
**Razão**: Pode ser útil no futuro, mas não é essencial agora  
**Localização**: `api/fhir/`  
**Ação**: Manter desativado, pode ativar depois se necessário

---

## 🔧 Módulos a CUSTOMIZAR

### 1. **ACL (Access Control List)**
**Status**: 🔧 CUSTOMIZAR  
**Ação Necessária**:
- Configurar roles customizados
- Mapear permissões por unidade
- Implementar filtros de unidade

---

### 2. **Dashboard**
**Status**: 🔧 CUSTOMIZAR  
**Ação Necessária**:
- Personalizar widgets
- Filtrar por unidade
- Adaptar métricas

---

### 3. **Menu System**
**Status**: 🔧 CUSTOMIZAR  
**Ação Necessária**:
- Remover links para módulos desativados
- Reorganizar menu por role
- Simplificar navegação

---

## 📋 Checklist de Remoção

### Passo 1: Backup
- [ ] Backup completo do banco de dados
- [ ] Backup dos arquivos
- [ ] Documentar versão atual do OpenEMR

### Passo 2: Desativação via Interface
- [ ] Acessar Administração > Módulos
- [ ] Desativar módulos não utilizados
- [ ] Testar sistema após desativação

### Passo 3: Remoção de Arquivos (Opcional)
- [ ] Remover pastas de módulos desativados
- [ ] Limpar referências no código
- [ ] Atualizar menu

### Passo 4: Limpeza do Banco de Dados
- [ ] Remover entradas da tabela `registry`
- [ ] Remover configurações relacionadas
- [ ] Limpar cache

### Passo 5: Testes
- [ ] Testar funcionalidades mantidas
- [ ] Verificar permissões
- [ ] Validar relatórios
- [ ] Testar agendamentos
- [ ] Testar cadastro de pacientes

---

## 🎯 Priorização

### Fase 1 - Essenciais (Implementar Primeiro)
1. Appointments
2. Patients
3. Clinical
4. Users & Access Control

### Fase 2 - Importantes (Implementar Depois)
5. Reports
6. Documents

### Fase 3 - Customizações (Ajustar Conforme Necessário)
7. ACL customizado
8. Dashboard personalizado
9. Menu simplificado

---

## 📝 Notas de Implementação

### Desativação Segura
1. **Sempre desativar primeiro** via interface admin
2. **Testar completamente** antes de remover arquivos
3. **Manter backup** por pelo menos 30 dias
4. **Documentar** todas as remoções

### Customização de Roles
O OpenEMR usa um sistema de ACL diferente. Será necessário:
- Mapear roles do sistema atual para ACL do OpenEMR
- Criar grupos de permissões customizados
- Implementar filtros de unidade no código

### Performance
- Remover módulos não utilizados melhora performance
- Reduz tamanho do banco de dados
- Simplifica manutenção

