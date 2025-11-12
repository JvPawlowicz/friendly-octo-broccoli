# Menu de Pacientes e Colaboradores - Implementado

## ✅ Funcionalidades Criadas

### 1. Menu de Pacientes (Acessível a Todas as Roles)
**Rota:** `/app/pacientes`

**Componente:** `ListaPacientes`

**Funcionalidades:**
- ✅ Lista todos os pacientes com foto, nome, CPF, status e unidade
- ✅ Filtro por busca (nome, CPF, email)
- ✅ Filtro por status (Ativo, Inativo, Em espera)
- ✅ Filtro por unidade (apenas Admin)
- ✅ Filtro automático por unidade do usuário logado
- ✅ Paginação
- ✅ Link para ver prontuário
- ✅ Link para editar (dependendo da role)
- ✅ Botão para criar novo paciente

**Acesso:**
- Todas as roles com permissão `ver_pacientes` podem acessar
- Profissionais, Coordenadores, Secretaria e Admin podem ver

### 2. Formulário de Paciente (Para Secretaria e Outros)
**Rotas:**
- `/app/pacientes/criar` - Criar novo paciente
- `/app/pacientes/{id}/editar` - Editar paciente existente

**Componente:** `FormPaciente`

**Funcionalidades:**
- ✅ Upload de foto de perfil
- ✅ Dados principais (nome, CPF, data nascimento, status)
- ✅ Contato (email, telefone)
- ✅ Endereço completo
- ✅ Plano de saúde
- ✅ Dados clínicos (diagnóstico, plano de crise, alergias, etc.)
- ✅ Seleção de unidade padrão
- ✅ Validações completas

**Acesso:**
- Secretaria pode criar e editar pacientes através deste formulário
- Admin e Coordenador podem usar Filament ou este formulário

### 3. Menu de Colaboradores (Apenas Admin)
**Rota:** `/app/colaboradores`

**Componente:** `ListaColaboradores`

**Funcionalidades:**
- ✅ Visualização em cards com foto, nome, email
- ✅ Exibição de perfil (role) com cores diferentes
- ✅ Status (Ativo/Inativo)
- ✅ Unidades vinculadas
- ✅ Cargo do colaborador
- ✅ Filtro por busca (nome, email, cargo)
- ✅ Filtro por perfil (role)
- ✅ Filtro por status
- ✅ Filtro por unidade
- ✅ Paginação
- ✅ Link para editar no Filament

**Acesso:**
- Apenas Admin pode acessar
- Aparece no menu apenas para Admin

## 📋 Estrutura de Navegação

### Menu Principal (app.blade.php)
1. **Agenda** - Todas as roles
2. **Pacientes** - Todas as roles (NOVO)
3. **Evoluções** - Todas as roles
4. **Avaliações** - Todas as roles
5. **Relatórios** - Roles com permissão
6. **Colaboradores** - Apenas Admin (NOVO)

## 🔐 Regras de Acesso

### Pacientes
- **Ver:** Todas as roles com `ver_pacientes`
- **Criar:** Roles com `criar_paciente` (Secretaria, Admin, Coordenador)
- **Editar:** 
  - Admin/Coordenador: Via Filament
  - Secretaria: Via formulário Livewire
- **Filtro:** Automático por unidade do usuário (exceto Admin)

### Colaboradores
- **Ver:** Apenas Admin
- **Editar:** Apenas Admin (via Filament)

## 📁 Arquivos Criados

1. `app/Livewire/ListaPacientes.php` - Lista de pacientes
2. `app/Livewire/FormPaciente.php` - Formulário de criação/edição
3. `app/Livewire/ListaColaboradores.php` - Lista de colaboradores
4. `resources/views/livewire/lista-pacientes.blade.php` - View da lista
5. `resources/views/livewire/form-paciente.blade.php` - View do formulário
6. `resources/views/livewire/lista-colaboradores.blade.php` - View de colaboradores

## 📁 Arquivos Modificados

1. `routes/web.php` - Rotas adicionadas
2. `resources/views/components/layouts/app.blade.php` - Menu atualizado

## 🎨 Interface

### Lista de Pacientes
- Tabela responsiva
- Fotos em círculo
- Badges de status coloridos
- Filtros em tempo real
- Paginação

### Lista de Colaboradores
- Cards em grid responsivo
- Fotos grandes
- Badges de perfil coloridos (Admin=roxo, Coordenador=azul, Profissional=verde)
- Status visual
- Lista de unidades

### Formulário de Paciente
- Layout organizado em seções
- Upload de foto com preview
- Campos organizados em grid
- Validação em tempo real
- Botões de ação

## ✅ Testes Recomendados

1. **Login como Profissional:**
   - Verificar que aparece menu "Pacientes"
   - Verificar que só vê pacientes de suas unidades
   - Verificar que não aparece menu "Colaboradores"
   - Verificar que não pode criar pacientes (sem permissão)

2. **Login como Secretaria:**
   - Verificar que pode criar pacientes via formulário
   - Verificar que pode editar pacientes via formulário
   - Verificar que não aparece menu "Colaboradores"

3. **Login como Admin:**
   - Verificar que aparece menu "Pacientes"
   - Verificar que aparece menu "Colaboradores"
   - Verificar que vê todos os pacientes
   - Verificar que vê todos os colaboradores
   - Verificar filtros funcionando

4. **Seleção de Unidade:**
   - Verificar que ao selecionar unidade, pacientes mudam
   - Verificar que a seleção persiste entre páginas

