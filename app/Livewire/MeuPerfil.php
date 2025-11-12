<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app')]
class MeuPerfil extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $cargo;
    public $conselho_profissional;
    public $numero_conselho;
    public $especialidades;
    public $foto_perfil;
    public $foto_perfil_nova = null;
    public $password;
    public $password_confirmation;
    public $mostrarSenha = false;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->cargo = $user->cargo;
        $this->conselho_profissional = $user->conselho_profissional;
        $this->numero_conselho = $user->numero_conselho;
        $this->especialidades = $user->especialidades;
        $this->foto_perfil = $user->foto_perfil;
    }

    public function salvar()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'cargo' => 'nullable|string|max:255',
            'conselho_profissional' => 'nullable|string|max:255',
            'numero_conselho' => 'nullable|string|max:255',
            'especialidades' => 'nullable|string|max:500',
            'foto_perfil_nova' => 'nullable|image|max:2048',
            'password' => 'nullable|min:8|confirmed',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.unique' => 'Este email já está em uso.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        $user = Auth::user();

        // Upload de nova foto
        if ($this->foto_perfil_nova) {
            // Deleta foto antiga se existir
            if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }

            $path = $this->foto_perfil_nova->store('fotos-perfil', 'public');
            $user->foto_perfil = $path;
        }

        // Atualiza dados
        $user->name = $this->name;
        $user->email = $this->email;
        $user->cargo = $this->cargo;
        $user->conselho_profissional = $this->conselho_profissional;
        $user->numero_conselho = $this->numero_conselho;
        $user->especialidades = $this->especialidades;

        // Atualiza senha se fornecida
        if ($this->password) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        // Atualiza foto_perfil local
        $this->foto_perfil = $user->foto_perfil;
        $this->foto_perfil_nova = null;
        $this->password = '';
        $this->password_confirmation = '';

        session()->flash('message', 'Perfil atualizado com sucesso!');
    }

    public function render()
    {
        $user = Auth::user();
        return view('livewire.meu-perfil', [
            'unidades' => $user->unidades,
            'roles' => $user->roles,
        ]);
    }
}

