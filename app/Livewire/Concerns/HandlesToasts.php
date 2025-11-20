<?php

namespace App\Livewire\Concerns;

trait HandlesToasts
{
    /**
     * Envia uma notificação toast para o frontend
     *
     * @param string $mensagem Mensagem a ser exibida
     * @param string $tipo Tipo do toast: 'success', 'error', 'warning', 'info'
     * @param int $duracao Duração em milissegundos (padrão: 4000)
     * @return void
     */
    protected function enviarToast(string $mensagem, string $tipo = 'info', int $duracao = 4000): void
    {
        $this->dispatch('app:toast', message: $mensagem, type: $tipo, duration: $duracao);
    }

    /**
     * Toast de sucesso
     */
    protected function toastSuccess(string $mensagem, int $duracao = 4000): void
    {
        $this->enviarToast($mensagem, 'success', $duracao);
    }

    /**
     * Toast de erro
     */
    protected function toastError(string $mensagem, int $duracao = 5000): void
    {
        $this->enviarToast($mensagem, 'error', $duracao);
    }

    /**
     * Toast de aviso
     */
    protected function toastWarning(string $mensagem, int $duracao = 4000): void
    {
        $this->enviarToast($mensagem, 'warning', $duracao);
    }

    /**
     * Toast informativo
     */
    protected function toastInfo(string $mensagem, int $duracao = 4000): void
    {
        $this->enviarToast($mensagem, 'info', $duracao);
    }
}

