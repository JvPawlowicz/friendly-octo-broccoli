<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">Gerenciar Templates de Avaliação</h1>
                            <p class="text-gray-600">Crie e gerencie templates de avaliação com suas perguntas</p>
                        </div>
                        <button wire:click="abrirModalTemplate" 
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Novo Template
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Templates -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($templates->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum template cadastrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece criando um novo template de avaliação.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($templates as $template)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $template->nome_template }}</h3>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    {{ $template->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $template->status ? 'Ativo' : 'Inativo' }}
                                                </span>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    {{ $template->perguntas_count }} pergunta(s)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <button wire:click="abrirModalTemplate({{ $template->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-medium rounded hover:bg-indigo-200 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button wire:click="deletarTemplate({{ $template->id }})"
                                                    onclick="return confirm('Tem certeza que deseja remover este template?')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-sm font-medium rounded hover:bg-red-200 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Deletar
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Perguntas do Template -->
                                    @php
                                        $perguntas = $template->perguntas;
                                    @endphp
                                    <div class="mt-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="text-sm font-medium text-gray-700">Perguntas</h4>
                                            <button wire:click="abrirModalPergunta({{ $template->id }})"
                                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                                + Adicionar Pergunta
                                            </button>
                                        </div>
                                        @if($perguntas->isEmpty())
                                            <p class="text-sm text-gray-500 py-2">Nenhuma pergunta cadastrada</p>
                                        @else
                                            <div class="space-y-2">
                                                @foreach($perguntas as $pergunta)
                                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                        <div class="flex-1">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-xs font-medium text-gray-500">#{{ $pergunta->ordem }}</span>
                                                                <span class="text-sm text-gray-900">{{ $pergunta->titulo_pergunta }}</span>
                                                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-blue-100 text-blue-800">
                                                                    {{ ucfirst(str_replace('_', ' ', $pergunta->tipo_campo)) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <button wire:click="abrirModalPergunta({{ $template->id }}, {{ $pergunta->id }})"
                                                                    class="text-indigo-600 hover:text-indigo-800">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </button>
                                                            <button wire:click="deletarPergunta({{ $template->id }}, {{ $pergunta->id }})"
                                                                    onclick="return confirm('Tem certeza que deseja remover esta pergunta?')"
                                                                    class="text-red-600 hover:text-red-800">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $templates->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Template -->
    @if($mostrarModalTemplate)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="fecharModalTemplate">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $templateId ? 'Editar' : 'Criar' }} Template
                    </h3>
                    <button wire:click="fecharModalTemplate" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form wire:submit.prevent="salvarTemplate">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Template *</label>
                        <input type="text" wire:model="nome_template" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nome_template') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="status" 
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label class="ml-2 text-sm text-gray-700">Template Ativo</label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="fecharModalTemplate" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:from-indigo-700 hover:to-purple-700 transition">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal de Pergunta -->
    @if($mostrarModalPergunta && $templateSelecionado)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="fecharModalPergunta">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white" wire:click.stop>
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $perguntaId ? 'Editar' : 'Adicionar' }} Pergunta
                    </h3>
                    <button wire:click="fecharModalPergunta" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4 p-3 bg-indigo-50 rounded-lg">
                    <p class="text-sm text-indigo-700">
                        <strong>Template:</strong> {{ $templateSelecionado->nome_template }}
                    </p>
                </div>
                
                <form wire:submit.prevent="salvarPergunta">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título da Pergunta *</label>
                        <input type="text" wire:model="titulo_pergunta" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('titulo_pergunta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Campo *</label>
                        <select wire:model="tipo_campo" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="texto_curto">Texto Curto</option>
                            <option value="texto_longo">Texto Longo</option>
                            <option value="data">Data</option>
                            <option value="sim_nao">Sim/Não</option>
                        </select>
                        @error('tipo_campo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordem *</label>
                        <input type="number" wire:model="ordem" min="0"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('ordem') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">A ordem determina a sequência das perguntas no formulário</p>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="fecharModalPergunta" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:from-indigo-700 hover:to-purple-700 transition">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

