<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Unidade;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
class ListaColaboradores extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $unidadeFilter = '';
    
    public $mostrarModal = false;
    public $colaboradorId = null;
    
    // Campos do formulário
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $cargo = '';
    public $conselho_profissional = '';
    public $numero_conselho = '';
    public $especialidades = '';
    public $status = true;
    public $foto_perfil_nova = null;
    public $roles_selecionados = [];
    public $unidades_selecionadas = [];

    public function mount()
    {
        // Apenas Admin pode acessar
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Apenas administradores podem acessar esta página.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingUnidadeFilter()
    {
        $this->resetPage();
    }

    public function abrirModal($colaboradorId = null)
    {
        $this->colaboradorId = $colaboradorId;
        $this->mostrarModal = true;

        if ($colaboradorId) {
            $colaborador = User::with(['roles', 'unidades'])->findOrFail($colaboradorId);
            
            $this->name = $colaborador->name;
            $this->email = $colaborador->email;
            $this->cargo = $colaborador->cargo;
            $this->conselho_profissional = $colaborador->conselho_profissional;
            $this->numero_conselho = $colaborador->numero_conselho;
            $this->especialidades = $colaborador->especialidades;
            $this->status = $colaborador->status;
            $this->roles_selecionados = $colaborador->roles->pluck('id')->toArray();
            $this->unidades_selecionadas = $colaborador->unidades->pluck('id')->toArray();
        } else {
            $this->reset(['name', 'email', 'password', 'password_confirmation', 'cargo', 'conselho_profissional', 'numero_conselho', 'especialidades', 'status', 'foto_perfil_nova', 'roles_selecionados', 'unidades_selecionadas']);
            $this->status = true;
        }
    }

    public function fecharModal()
    {
        $this->mostrarModal = false;
        $this->reset(['colaboradorId', 'name', 'email', 'password', 'password_confirmation', 'cargo', 'conselho_profissional', 'numero_conselho', 'especialidades', 'status', 'foto_perfil_nova', 'roles_selecionados', 'unidades_selecionadas']);
        $this->status = true;
    }

    public function salvar()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->colaboradorId ?? 'NULL'),
            'cargo' => 'nullable|string|max:255',
            'conselho_profissional' => 'nullable|string|max:255',
            'numero_conselho' => 'nullable|string|max:255',
            'especialidades' => 'nullable|string|max:500',
            'status' => 'boolean',
            'roles_selecionados' => 'required|array|min:1',
            'foto_perfil_nova' => 'nullable|image|max:2048',
        ];

        if (!$this->colaboradorId) {
            $rules['password'] = 'required|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|min:8|confirmed';
        }

        $this->validate($rules, [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.unique' => 'Este email já está em uso.',
            'password.required' => 'A senha é obrigatória ao criar um novo colaborador.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'roles_selecionados.required' => 'Selecione pelo menos um perfil (role).',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'cargo' => $this->cargo ?: null,
            'conselho_profissional' => $this->conselho_profissional ?: null,
            'numero_conselho' => $this->numero_conselho ?: null,
            'especialidades' => $this->especialidades ?: null,
            'status' => $this->status,
        ];

        // Upload de foto
        if ($this->foto_perfil_nova) {
            if ($this->colaboradorId) {
                $colaborador = User::find($this->colaboradorId);
                if ($colaborador && $colaborador->foto_perfil && Storage::disk('public')->exists($colaborador->foto_perfil)) {
                    Storage::disk('public')->delete($colaborador->foto_perfil);
                }
            }
            $path = $this->foto_perfil_nova->store('avatars', 'public');
            $data['foto_perfil'] = $path;
        }

        // Atualiza senha se fornecida
        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->colaboradorId) {
            $colaborador = User::findOrFail($this->colaboradorId);
            $colaborador->update($data);
            
            // Atualiza roles
            $colaborador->syncRoles($this->roles_selecionados);
            
            // Atualiza unidades
            $colaborador->unidades()->sync($this->unidades_selecionadas);
            
            session()->flash('message', 'Colaborador atualizado com sucesso!');
        } else {
            $colaborador = User::create($data);
            
            // Atribui roles
            $colaborador->assignRole($this->roles_selecionados);
            
            // Vincula unidades
            $colaborador->unidades()->sync($this->unidades_selecionadas);
            
            session()->flash('message', 'Colaborador criado com sucesso!');
        }

        $this->fecharModal();
    }

    public function deletar($colaboradorId)
    {
        $colaborador = User::findOrFail($colaboradorId);
        
        // Não permite deletar a si mesmo
        if ($colaborador->id === Auth::id()) {
            session()->flash('error', 'Você não pode deletar seu próprio usuário.');
            return;
        }

        // Deleta foto se existir
        if ($colaborador->foto_perfil && Storage::disk('public')->exists($colaborador->foto_perfil)) {
            Storage::disk('public')->delete($colaborador->foto_perfil);
        }

        $colaborador->delete();
        session()->flash('message', 'Colaborador removido com sucesso!');
    }

    public function toggleStatus($colaboradorId)
    {
        $colaborador = User::findOrFail($colaboradorId);
        
        // Não permite desativar a si mesmo
        if ($colaborador->id === Auth::id()) {
            session()->flash('error', 'Você não pode desativar seu próprio usuário.');
            return;
        }

        $colaborador->update(['status' => !$colaborador->status]);
        session()->flash('message', 'Status do colaborador atualizado!');
    }

    public function render()
    {
        $query = User::query();

        // Filtro de busca
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('cargo', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por role
        if ($this->roleFilter) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->roleFilter);
            });
        }

        // Filtro por status
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter === '1' ? true : false);
        }

        // Filtro por unidade
        if ($this->unidadeFilter) {
            $query->whereHas('unidades', function ($q) {
                $q->where('unidades.id', $this->unidadeFilter);
            });
        }

        $colaboradores = $query->with(['roles', 'unidades'])
            ->orderBy('name')
            ->paginate(15);

        $roles = Role::orderBy('name')->get();
        $unidades = Unidade::orderBy('nome')->get();

        return view('livewire.lista-colaboradores', [
            'colaboradores' => $colaboradores,
            'roles' => $roles,
            'unidades' => $unidades,
        ]);
    }
}

