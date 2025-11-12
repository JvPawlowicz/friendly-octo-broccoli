<?php

namespace App\Livewire;

use App\Models\Paciente;
use App\Models\PlanoSaude;
use App\Models\Unidade;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class FormPaciente extends Component
{
    use WithFileUploads;

    public ?int $pacienteId = null;
    public $foto_perfil;
    public string $nome_completo = '';
    public ?string $nome_social = null;
    public ?string $cpf = null;
    public ?string $data_nascimento = null;
    public string $status = 'Ativo';
    public ?string $email_principal = null;
    public ?string $telefone_principal = null;
    
    // Endereço
    public ?string $cep = null;
    public ?string $logradouro = null;
    public ?string $numero = null;
    public ?string $complemento = null;
    public ?string $bairro = null;
    public ?string $cidade = null;
    public ?string $estado = null;
    
    // Plano de Saúde
    public ?int $plano_saude_id = null;
    public ?string $numero_carteirinha = null;
    public ?string $validade_carteirinha = null;
    
    // Dados Clínicos
    public ?int $unidade_padrao_id = null;
    public ?string $diagnostico_condicao = null;
    public ?string $plano_de_crise = null;
    public ?string $alergias_medicacoes = null;
    public ?string $metodo_comunicacao = null;
    public ?string $informacoes_escola = null;
    public ?string $informacoes_medicas_adicionais = null;

    public function mount(?int $pacienteId = null)
    {
        // Verifica permissão
        if ($pacienteId) {
            if (!Auth::user()->can('editar_paciente') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria'])) {
                abort(403, 'Você não tem permissão para editar pacientes.');
            }
        } else {
            if (!Auth::user()->can('criar_paciente') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria'])) {
                abort(403, 'Você não tem permissão para criar pacientes.');
            }
        }

        if ($pacienteId) {
            $paciente = Paciente::findOrFail($pacienteId);
            $this->pacienteId = $paciente->id;
            $this->nome_completo = $paciente->nome_completo;
            $this->nome_social = $paciente->nome_social;
            $this->cpf = $paciente->cpf;
            $this->data_nascimento = $paciente->data_nascimento?->format('Y-m-d');
            $this->status = $paciente->status;
            $this->email_principal = $paciente->email_principal;
            $this->telefone_principal = $paciente->telefone_principal;
            $this->cep = $paciente->cep;
            $this->logradouro = $paciente->logradouro;
            $this->numero = $paciente->numero;
            $this->complemento = $paciente->complemento;
            $this->bairro = $paciente->bairro;
            $this->cidade = $paciente->cidade;
            $this->estado = $paciente->estado;
            $this->plano_saude_id = $paciente->plano_saude_id;
            $this->numero_carteirinha = $paciente->numero_carteirinha;
            $this->validade_carteirinha = $paciente->validade_carteirinha?->format('Y-m-d');
            $this->unidade_padrao_id = $paciente->unidade_padrao_id;
            $this->diagnostico_condicao = $paciente->diagnostico_condicao;
            $this->plano_de_crise = $paciente->plano_de_crise;
            $this->alergias_medicacoes = $paciente->alergias_medicacoes;
            $this->metodo_comunicacao = $paciente->metodo_comunicacao;
            $this->informacoes_escola = $paciente->informacoes_escola;
            $this->informacoes_medicas_adicionais = $paciente->informacoes_medicas_adicionais;
        } else {
            // Define unidade padrão se usuário tiver apenas uma
            $user = Auth::user();
            if (!$user->hasRole('Admin')) {
                $unidades = $user->unidades;
                if ($unidades->count() === 1) {
                    $this->unidade_padrao_id = $unidades->first()->id;
                }
            }
        }
    }

    public function salvar()
    {
        $this->validate([
            'nome_completo' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:14',
            'data_nascimento' => 'nullable|date',
            'status' => 'required|in:Ativo,Inativo,Em espera',
            'email_principal' => 'nullable|email|max:255',
            'telefone_principal' => 'nullable|string|max:20',
            'foto_perfil' => 'nullable|image|max:2048',
            'unidade_padrao_id' => 'nullable|exists:unidades,id',
            'plano_saude_id' => 'nullable|exists:plano_saudes,id',
        ]);

        $data = [
            'nome_completo' => $this->nome_completo,
            'nome_social' => $this->nome_social,
            'cpf' => $this->cpf,
            'data_nascimento' => $this->data_nascimento ? \Carbon\Carbon::parse($this->data_nascimento) : null,
            'status' => $this->status,
            'email_principal' => $this->email_principal,
            'telefone_principal' => $this->telefone_principal,
            'cep' => $this->cep,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'complemento' => $this->complemento,
            'bairro' => $this->bairro,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'plano_saude_id' => $this->plano_saude_id,
            'numero_carteirinha' => $this->numero_carteirinha,
            'validade_carteirinha' => $this->validade_carteirinha ? \Carbon\Carbon::parse($this->validade_carteirinha) : null,
            'unidade_padrao_id' => $this->unidade_padrao_id,
            'diagnostico_condicao' => $this->diagnostico_condicao,
            'plano_de_crise' => $this->plano_de_crise,
            'alergias_medicacoes' => $this->alergias_medicacoes,
            'metodo_comunicacao' => $this->metodo_comunicacao,
            'informacoes_escola' => $this->informacoes_escola,
            'informacoes_medicas_adicionais' => $this->informacoes_medicas_adicionais,
        ];

        // Upload de foto
        if ($this->foto_perfil) {
            $data['foto_perfil'] = $this->foto_perfil->store('avatars-pacientes', 'public');
        }

        if ($this->pacienteId) {
            $paciente = Paciente::findOrFail($this->pacienteId);
            $paciente->update($data);
            session()->flash('message', 'Paciente atualizado com sucesso!');
        } else {
            $paciente = Paciente::create($data);
            session()->flash('message', 'Paciente criado com sucesso!');
        }

        return redirect()->route('app.pacientes');
    }

    public function render()
    {
        $planosSaude = PlanoSaude::orderBy('nome_plano')->get();
        
        $unidades = Auth::user()->hasRole('Admin') 
            ? Unidade::orderBy('nome')->get()
            : Auth::user()->unidades;

        return view('livewire.form-paciente', [
            'planosSaude' => $planosSaude,
            'unidades' => $unidades,
        ]);
    }
}

