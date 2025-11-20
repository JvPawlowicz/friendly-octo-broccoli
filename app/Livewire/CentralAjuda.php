<?php

namespace App\Livewire;

use App\Models\Feedback;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Concerns\HandlesToasts;

#[Layout('components.layouts.app')]
class CentralAjuda extends Component
{
    use HandlesToasts, WithPagination;

    public $assunto = '';
    public $mensagem = '';

    protected $rules = [
        'assunto' => 'required|string|min:3|max:255',
        'mensagem' => 'required|string|min:10|max:2000',
    ];

    protected $messages = [
        'assunto.required' => 'O assunto é obrigatório.',
        'assunto.min' => 'O assunto deve ter pelo menos 3 caracteres.',
        'assunto.max' => 'O assunto não pode ter mais de 255 caracteres.',
        'mensagem.required' => 'A mensagem é obrigatória.',
        'mensagem.min' => 'A mensagem deve ter pelo menos 10 caracteres.',
        'mensagem.max' => 'A mensagem não pode ter mais de 2000 caracteres.',
    ];

    public function salvar()
    {
        $this->validate();

        Feedback::create([
            'user_id' => Auth::id(),
            'assunto' => $this->assunto,
            'mensagem' => $this->mensagem,
            'status' => 'pendente',
        ]);

        $this->toastSuccess('Feedback enviado com sucesso! Entraremos em contato em breve.');
        $this->reset(['assunto', 'mensagem']);
        $this->resetValidation();
    }

    public function getMeusFeedbacksProperty()
    {
        return Feedback::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.central-ajuda', [
            'meusFeedbacks' => $this->meusFeedbacks,
        ]);
    }
}

