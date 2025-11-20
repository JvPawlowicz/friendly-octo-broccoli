<div>
    <form wire:submit.prevent="salvar">
        <div class="space-y-6">
            <!-- Conflitos e Erros -->
            @if(!empty($conflitos))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Conflitos Detectados:</h4>
                    <ul class="list-disc list-inside text-sm text-yellow-700">
                        @foreach($conflitos as $conflito)
                            <li>{{ $conflito['mensagem'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!empty($erros))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-semibold text-red-800 mb-2">Erros:</h4>
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach($erros as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Campos do Formulário -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.form-select 
                    label="Paciente" 
                    wire:model="pacienteId" 
                    wire:change="verificarConflitos"
                    required>
                    <option value="">Selecione um paciente</option>
                    @foreach($pacientes as $paciente)
                        <option value="{{ $paciente->id }}">{{ $paciente->nome_completo }}</option>
                    @endforeach
                </x-ui.form-select>

                <x-ui.form-select 
                    label="Profissional" 
                    wire:model="userId" 
                    wire:change="verificarConflitos"
                    required>
                    <option value="">Selecione um profissional</option>
                    @foreach($profissionais as $prof)
                        <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                    @endforeach
                </x-ui.form-select>

                <x-ui.form-select 
                    label="Sala" 
                    wire:model="salaId" 
                    wire:change="verificarConflitos"
                    help="Opcional - selecione uma sala específica">
                    <option value="">Sem sala (online)</option>
                    @foreach($salas as $sala)
                        <option value="{{ $sala->id }}">{{ $sala->nome }}</option>
                    @endforeach
                </x-ui.form-select>

                <x-ui.form-select 
                    label="Status" 
                    wire:model="status"
                    required>
                    <option value="Agendado">Agendado</option>
                    <option value="Confirmado">Confirmado</option>
                    <option value="Check-in">Check-in</option>
                    <option value="Concluído">Concluído</option>
                    <option value="Cancelado">Cancelado</option>
                </x-ui.form-select>

                <x-ui.form-input 
                    label="Data/Hora Início" 
                    type="datetime-local"
                    wire:model="dataHoraInicio" 
                    wire:change="verificarConflitos"
                    required
                    help="Data e hora de início do atendimento" />

                <x-ui.form-input 
                    label="Data/Hora Fim" 
                    type="datetime-local"
                    wire:model="dataHoraFim" 
                    wire:change="verificarConflitos"
                    required
                    help="Data e hora de término do atendimento" />
            </div>

            <!-- Recorrência -->
            <div class="border-t border-gray-200 pt-4">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="repetir" class="form-checkbox">
                    <span class="ml-2 text-sm font-medium text-gray-700">Repetir atendimento</span>
                </label>

                @if($repetir)
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Recorrência</label>
                            <select wire:model="tipoRecorrencia" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="semanal">Semanal</option>
                                <option value="quinzenal">Quinzenal</option>
                                <option value="mensal">Mensal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Repetir quantas vezes?</label>
                            <input type="number" wire:model="vezesRepetir" min="2" max="52" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                @endif
            </div>

            <!-- Botões -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                <button type="button" 
                        wire:click="$dispatch('fechar-modal-atendimento')"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Cancelar
                </button>
                <x-ui.loading-button 
                    type="submit"
                    target="salvar"
                    loading-text="Salvando..."
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ $atendimentoId ? 'Atualizar' : 'Salvar' }}
                </x-ui.loading-button>
            </div>
        </div>
    </form>
</div>
