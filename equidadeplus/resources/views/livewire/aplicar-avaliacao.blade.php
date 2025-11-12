<div>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Aplicar Avaliação</h2>
                <p class="mt-2 text-sm text-gray-600">Selecione o paciente e o template de avaliação para começar</p>
            </div>
        </div>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-xl border border-gray-100">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if($passoAtual === 1)
                            Passo 1: Seleção
                        @else
                            Passo 2: Preenchimento
                        @endif
                    </h3>
                </div>
                <div class="p-6 text-gray-900">
                        @if($passoAtual === 1)
                            <!-- Passo 1: Seleção -->
                            <div class="space-y-6">
                                <div>
                                    <label for="pacienteId" class="block text-sm font-medium text-gray-700">Paciente *</label>
                                    <select wire:model="pacienteId" 
                                            id="pacienteId"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Selecione um paciente</option>
                                        @foreach($pacientes as $paciente)
                                            <option value="{{ $paciente->id }}">{{ $paciente->nome_completo }}</option>
                                        @endforeach
                                    </select>
                                    @error('pacienteId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="templateId" class="block text-sm font-medium text-gray-700">Template de Avaliação *</label>
                                    <select wire:model.live="templateId" 
                                            id="templateId"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Selecione um template</option>
                                        @foreach($templates as $template)
                                            <option value="{{ $template->id }}">{{ $template->nome_template }}</option>
                                        @endforeach
                                    </select>
                                    @error('templateId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                @if($templateId)
                                    <div class="p-3 bg-blue-50 rounded-lg">
                                        <p class="text-sm text-blue-800">
                                            Template selecionado. Clique em "Próximo Passo" para preencher as perguntas.
                                        </p>
                                    </div>
                                @endif

                                @if($templateId && $pacienteId)
                                    <button wire:click="avancarPasso1" 
                                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105">
                                        <span>Próximo Passo</span>
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @elseif($template)
                            <!-- Passo 2: Respostas -->
                            <div class="space-y-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold">{{ $template->nome_template }}</h3>
                                    <button wire:click="voltarPasso1" 
                                            class="text-sm text-gray-600 hover:text-gray-900">
                                        ← Voltar
                                    </button>
                                </div>

                                @if($avaliacao)
                                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-sm text-yellow-800">
                                            <strong>Modo Edição:</strong> Esta avaliação está em rascunho. Continue preenchendo ou finalize.
                                        </p>
                                    </div>
                                @endif

                                @foreach($template->perguntas as $pergunta)
                                    <div class="border-b border-gray-200 pb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $pergunta->titulo_pergunta }}
                                        </label>
                                        
                                        @if($pergunta->tipo_campo === 'texto_curto')
                                            <input type="text" 
                                                   wire:model.live.debounce.500ms="respostas.{{ $pergunta->id }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @elseif($pergunta->tipo_campo === 'texto_longo')
                                            <textarea wire:model.live.debounce.500ms="respostas.{{ $pergunta->id }}"
                                                      rows="4"
                                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                        @elseif($pergunta->tipo_campo === 'data')
                                            <input type="date" 
                                                   wire:model.live.debounce.500ms="respostas.{{ $pergunta->id }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @elseif($pergunta->tipo_campo === 'sim_nao')
                                            <div class="mt-2">
                                                <label class="inline-flex items-center">
                                                    <input type="radio" wire:model.live.debounce.500ms="respostas.{{ $pergunta->id }}" value="Sim" class="form-radio">
                                                    <span class="ml-2">Sim</span>
                                                </label>
                                                <label class="inline-flex items-center ml-6">
                                                    <input type="radio" wire:model.live.debounce.500ms="respostas.{{ $pergunta->id }}" value="Não" class="form-radio">
                                                    <span class="ml-2">Não</span>
                                                </label>
                                            </div>
                                        @endif
                                        
                                        @error('respostas.' . $pergunta->id) 
                                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                @endforeach

                                <div class="flex space-x-4 pt-6 border-t border-gray-200">
                                    <button wire:click="salvarRascunho" 
                                            class="inline-flex items-center px-6 py-3 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        Salvar Rascunho
                                    </button>

                                    <button wire:click="finalizarAvaliacao" 
                                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Finalizar Avaliação
                                    </button>
                                </div>
                            </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
