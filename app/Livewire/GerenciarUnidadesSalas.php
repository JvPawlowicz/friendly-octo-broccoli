<?php

namespace App\Livewire;

use App\Models\Unidade;
use App\Models\Sala;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app')]
class GerenciarUnidadesSalas extends Component
{
    use WithFileUploads;

    public $unidades;
    public $unidadeSelecionada = null;
    public $mostrarModalUnidade = false;
    public $mostrarModalSala = false;
    
    // Campos da unidade
    public $unidadeId = null;
    public $nome = '';
    public $logo_unidade = null;
    public $logo_unidade_nova = null;
    public $cep = '';
    public $logradouro = '';
    public $numero = '';
    public $complemento = '';
    public $bairro = '';
    public $cidade = '';
    public $estado = '';
    public $telefone_principal = '';
    
    // Campos da sala
    public $salaId = null;
    public $nome_sala = '';
    public $capacidade = '';

    public function mount()
    {
        // Verifica permissão - Coordenador e Admin
        if (!Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para gerenciar unidades e salas.');
        }

        $this->carregarUnidades();
    }

    public function carregarUnidades()
    {
        $user = Auth::user();
        
        if ($user->hasRole('Admin')) {
            $this->unidades = Unidade::with('salas')->orderBy('nome')->get();
        } else {
            // Coordenador vê apenas suas unidades
            $this->unidades = $user->unidades()->with('salas')->orderBy('nome')->get();
        }
    }

    public function selecionarUnidade($unidadeId)
    {
        $this->unidadeSelecionada = $this->unidades->find($unidadeId);
    }

    public function abrirModalUnidade($unidadeId = null)
    {
        $this->unidadeId = $unidadeId;
        $this->mostrarModalUnidade = true;

        if ($unidadeId) {
            $unidade = Unidade::findOrFail($unidadeId);
            $this->nome = $unidade->nome;
            $this->logo_unidade = $unidade->logo_unidade;
            $this->cep = $unidade->cep;
            $this->logradouro = $unidade->logradouro;
            $this->numero = $unidade->numero;
            $this->complemento = $unidade->complemento;
            $this->bairro = $unidade->bairro;
            $this->cidade = $unidade->cidade;
            $this->estado = $unidade->estado;
            $this->telefone_principal = $unidade->telefone_principal;
        } else {
            $this->reset(['nome', 'logo_unidade', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'telefone_principal', 'logo_unidade_nova']);
        }
    }

    public function fecharModalUnidade()
    {
        $this->mostrarModalUnidade = false;
        $this->reset(['unidadeId', 'nome', 'logo_unidade', 'logo_unidade_nova', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'telefone_principal']);
    }

    public function salvarUnidade()
    {
        $this->validate([
            'nome' => 'required|string|max:255',
            'logo_unidade_nova' => 'nullable|image|max:2048',
            'cep' => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'telefone_principal' => 'nullable|string|max:20',
        ], [
            'nome.required' => 'O nome da unidade é obrigatório.',
        ]);

        $data = [
            'nome' => $this->nome,
            'cep' => $this->cep ?: null,
            'logradouro' => $this->logradouro ?: null,
            'numero' => $this->numero ?: null,
            'complemento' => $this->complemento ?: null,
            'bairro' => $this->bairro ?: null,
            'cidade' => $this->cidade ?: null,
            'estado' => $this->estado ?: null,
            'telefone_principal' => $this->telefone_principal ?: null,
        ];

        // Upload de logo
        if ($this->logo_unidade_nova) {
            // Deleta logo antiga se existir
            if ($this->unidadeId && $this->logo_unidade && Storage::disk('public')->exists($this->logo_unidade)) {
                Storage::disk('public')->delete($this->logo_unidade);
            }

            $path = $this->logo_unidade_nova->store('logos-unidades', 'public');
            $data['logo_unidade'] = $path;
        }

        if ($this->unidadeId) {
            $unidade = Unidade::findOrFail($this->unidadeId);
            $unidade->update($data);
            session()->flash('message', 'Unidade atualizada com sucesso!');
        } else {
            // Apenas Admin pode criar unidades
            if (!Auth::user()->hasRole('Admin')) {
                session()->flash('error', 'Apenas administradores podem criar novas unidades.');
                return;
            }
            Unidade::create($data);
            session()->flash('message', 'Unidade criada com sucesso!');
        }

        $this->carregarUnidades();
        $this->fecharModalUnidade();
    }

    public function abrirModalSala($unidadeId, $salaId = null)
    {
        $this->unidadeId = $unidadeId;
        $this->salaId = $salaId;
        $this->mostrarModalSala = true;

        if ($salaId) {
            $sala = Sala::where('id', $salaId)
                ->where('unidade_id', $unidadeId)
                ->firstOrFail();
            
            $this->nome_sala = $sala->nome;
            $this->capacidade = $sala->capacidade;
        } else {
            $this->reset(['nome_sala', 'capacidade']);
        }
    }

    public function fecharModalSala()
    {
        $this->mostrarModalSala = false;
        $this->reset(['salaId', 'nome_sala', 'capacidade']);
    }

    public function salvarSala()
    {
        if (!$this->unidadeId) {
            return;
        }

        $this->validate([
            'nome_sala' => 'required|string|max:255',
            'capacidade' => 'nullable|integer|min:1',
        ], [
            'nome_sala.required' => 'O nome da sala é obrigatório.',
        ]);

        if ($this->salaId) {
            $sala = Sala::where('id', $this->salaId)
                ->where('unidade_id', $this->unidadeId)
                ->firstOrFail();
            
            $sala->update([
                'nome' => $this->nome_sala,
                'capacidade' => $this->capacidade ?: null,
            ]);

            session()->flash('message', 'Sala atualizada com sucesso!');
        } else {
            Sala::create([
                'unidade_id' => $this->unidadeId,
                'nome' => $this->nome_sala,
                'capacidade' => $this->capacidade ?: null,
            ]);

            session()->flash('message', 'Sala criada com sucesso!');
        }

        $this->carregarUnidades();
        $this->fecharModalSala();
    }

    public function deletarSala($unidadeId, $salaId)
    {
        $sala = Sala::where('id', $salaId)
            ->where('unidade_id', $unidadeId)
            ->firstOrFail();

        // Verifica se há atendimentos nesta sala
        if ($sala->atendimentos()->count() > 0) {
            session()->flash('error', 'Não é possível deletar esta sala. Existem atendimentos agendados nela.');
            return;
        }

        $sala->delete();
        $this->carregarUnidades();
        session()->flash('message', 'Sala removida com sucesso!');
    }

    public function render()
    {
        return view('livewire.gerenciar-unidades-salas');
    }
}

