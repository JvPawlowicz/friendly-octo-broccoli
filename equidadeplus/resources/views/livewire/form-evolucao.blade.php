<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ $tituloModal }}</h2>
        </div>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($paciente)
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-lg font-semibold">{{ $paciente->nome_completo }}</h3>
                            <p class="text-sm text-gray-600">Paciente</p>
                        </div>
                    @endif

                    @if($isFinalizado && !$isAdendo)
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-800 font-semibold">Esta evolução está finalizada e não pode ser editada.</p>
                        </div>
                    @endif

                    <form wire:submit.prevent="{{ $isAdendo ? 'salvarAdendo' : ($isFinalizado ? '' : 'finalizar') }}">
                        <div class="space-y-6">
                            <div>
                                <label for="relato_clinico" class="block text-sm font-medium text-gray-700">Relato Clínico *</label>
                                <textarea wire:model.blur="relato_clinico" 
                                          id="relato_clinico" 
                                          rows="6"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          {{ $isFinalizado && !$isAdendo ? 'disabled' : '' }}
                                          required></textarea>
                                @error('relato_clinico') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="conduta" class="block text-sm font-medium text-gray-700">Conduta</label>
                                <textarea wire:model.blur="conduta" 
                                          id="conduta" 
                                          rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          {{ $isFinalizado && !$isAdendo ? 'disabled' : '' }}></textarea>
                            </div>

                            <div>
                                <label for="objetivos" class="block text-sm font-medium text-gray-700">Objetivos</label>
                                <textarea wire:model.blur="objetivos" 
                                          id="objetivos" 
                                          rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          {{ $isFinalizado && !$isAdendo ? 'disabled' : '' }}></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between">
                            @if ($isAdendo)
                                <button type="submit" 
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 transition">
                                    Salvar Adendo
                                </button>
                            @else
                                @if(!$isFinalizado)
                                    <button type="button" 
                                            wire:click="salvarRascunho"
                                            class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 transition">
                                        Salvar Rascunho
                                    </button>
                                    <button type="submit" 
                                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 transition">
                                        Finalizar
                                    </button>
                                @endif
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
