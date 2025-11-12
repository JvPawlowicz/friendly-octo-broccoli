<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">Gerenciar Unidades e Salas</h1>
                            <p class="text-gray-600">Gerencie as unidades e suas salas de atendimento</p>
                        </div>
                        @if(Auth::user()->hasRole('Admin'))
                        <button wire:click="abrirModalUnidade" 
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Nova Unidade
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lista de Unidades -->
            <div class="space-y-6">
                @foreach($unidades as $unidade)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-4">
                                    @if($unidade->logo_unidade)
                                        <img src="{{ asset('storage/' . $unidade->logo_unidade) }}" 
                                             alt="{{ $unidade->nome }}" 
                                             class="h-12 w-12 rounded-lg object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($unidade->nome, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $unidade->nome }}</h3>
                                        @if($unidade->telefone_principal)
                                            <p class="text-sm text-gray-600">{{ $unidade->telefone_principal }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="abrirModalUnidade({{ $unidade->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-medium rounded hover:bg-indigo-200 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </button>
                                    <button wire:click="abrirModalSala({{ $unidade->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 text-sm font-medium rounded hover:bg-green-200 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Nova Sala
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($unidade->logradouro || $unidade->cidade)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">
                                    {{ $unidade->logradouro ?? '' }}
                                    {{ $unidade->numero ? ', ' . $unidade->numero : '' }}
                                    {{ $unidade->complemento ? ' - ' . $unidade->complemento : '' }}<br>
                                    {{ $unidade->bairro ?? '' }}
                                    {{ $unidade->cidade ? ' - ' . $unidade->cidade : '' }}
                                    {{ $unidade->estado ? '/' . $unidade->estado : '' }}<br>
                                    {{ $unidade->cep ? 'CEP: ' . $unidade->cep : '' }}
                                </p>
                            </div>
                            @endif

                            <h4 class="text-sm font-medium text-gray-700 mb-3">Salas ({{ $unidade->salas->count() }})</h4>
                            @if($unidade->salas->isEmpty())
                                <p class="text-sm text-gray-500 py-2">Nenhuma sala cadastrada</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($unidade->salas as $sala)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <h5 class="font-medium text-gray-900">{{ $sala->nome }}</h5>
                                                    @if($sala->capacidade)
                                                        <p class="text-sm text-gray-500 mt-1">Capacidade: {{ $sala->capacidade }} pessoa(s)</p>
                                                    @endif
                                                </div>
                                                <div class="flex items-center space-x-2 ml-2">
                                                    <button wire:click="abrirModalSala({{ $unidade->id }}, {{ $sala->id }})"
                                                            class="text-indigo-600 hover:text-indigo-800">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="deletarSala({{ $unidade->id }}, {{ $sala->id }})"
                                                            onclick="return confirm('Tem certeza que deseja remover esta sala?')"
                                                            class="text-red-600 hover:text-red-800">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal de Unidade -->
    @if($mostrarModalUnidade)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="fecharModalUnidade">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white" wire:click.stop>
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $unidadeId ? 'Editar' : 'Criar' }} Unidade
                    </h3>
                    <button wire:click="fecharModalUnidade" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form wire:submit.prevent="salvarUnidade">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Unidade *</label>
                            <input type="text" wire:model="nome" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('nome') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                            @if($logo_unidade && !$logo_unidade_nova)
                                <img src="{{ asset('storage/' . $logo_unidade) }}" alt="Logo" class="h-20 w-20 rounded-lg mb-2">
                            @endif
                            <input type="file" wire:model="logo_unidade_nova" 
                                   accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('logo_unidade_nova') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                            <input type="text" wire:model="cep" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefone Principal</label>
                            <input type="text" wire:model="telefone_principal" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                            <input type="text" wire:model="logradouro" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                            <input type="text" wire:model="numero" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                            <input type="text" wire:model="complemento" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                            <input type="text" wire:model="bairro" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                            <input type="text" wire:model="cidade" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <input type="text" wire:model="estado" maxlength="2"
                                   placeholder="UF"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="fecharModalUnidade" 
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

    <!-- Modal de Sala -->
    @if($mostrarModalSala)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="fecharModalSala">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $salaId ? 'Editar' : 'Adicionar' }} Sala
                    </h3>
                    <button wire:click="fecharModalSala" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form wire:submit.prevent="salvarSala">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Sala *</label>
                        <input type="text" wire:model="nome_sala" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nome_sala') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacidade</label>
                        <input type="number" wire:model="capacidade" min="1"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('capacidade') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="fecharModalSala" 
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

