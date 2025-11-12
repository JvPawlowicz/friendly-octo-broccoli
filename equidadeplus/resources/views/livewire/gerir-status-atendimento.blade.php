<div>
    <div class="p-4">
        <h3 class="text-lg font-semibold mb-4">Gerenciar Status do Atendimento</h3>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-2">
                <strong>Paciente:</strong> {{ $atendimento->paciente->nome_completo }}
            </p>
            <p class="text-sm text-gray-600 mb-2">
                <strong>Data/Hora:</strong> {{ $atendimento->data_hora_inicio->format('d/m/Y H:i') }}
            </p>
            <p class="text-sm text-gray-600 mb-4">
                <strong>Status Atual:</strong> 
                <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusAtual === 'Concluído' ? 'bg-green-100 text-green-800' : ($statusAtual === 'Cancelado' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ $statusAtual }}
                </span>
            </p>
        </div>

        <div class="space-y-2">
            <button wire:click="mudarStatus('Agendado')" 
                    class="w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded-md text-sm font-medium text-blue-700 {{ $statusAtual === 'Agendado' ? 'ring-2 ring-blue-500' : '' }}">
                Agendado
            </button>
            
            <button wire:click="mudarStatus('Confirmado')" 
                    class="w-full text-left px-4 py-2 bg-yellow-50 hover:bg-yellow-100 rounded-md text-sm font-medium text-yellow-700 {{ $statusAtual === 'Confirmado' ? 'ring-2 ring-yellow-500' : '' }}">
                Confirmado
            </button>
            
            <button wire:click="mudarStatus('Check-in')" 
                    class="w-full text-left px-4 py-2 bg-orange-50 hover:bg-orange-100 rounded-md text-sm font-medium text-orange-700 {{ $statusAtual === 'Check-in' ? 'ring-2 ring-orange-500' : '' }}">
                Check-in
            </button>
            
            <button wire:click="mudarStatus('Concluído')" 
                    class="w-full text-left px-4 py-2 bg-green-50 hover:bg-green-100 rounded-md text-sm font-medium text-green-700 {{ $statusAtual === 'Concluído' ? 'ring-2 ring-green-500' : '' }}">
                Concluído
            </button>
            
            <button wire:click="mudarStatus('Cancelado')" 
                    class="w-full text-left px-4 py-2 bg-red-50 hover:bg-red-100 rounded-md text-sm font-medium text-red-700 {{ $statusAtual === 'Cancelado' ? 'ring-2 ring-red-500' : '' }}">
                Cancelado
            </button>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <button wire:click="fecharModal" 
                    class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-sm font-medium text-gray-700">
                Fechar
            </button>
        </div>
    </div>
</div>
