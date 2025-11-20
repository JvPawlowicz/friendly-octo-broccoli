<div>
    @push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('modal-aberto', () => {
                document.body.style.overflow = 'hidden';
            });
            
            Livewire.on('modal-fechado', () => {
                document.body.style.overflow = '';
            });
        });
    </script>
    @endpush
    
    <!-- Modal de Feedback -->
    @if($mostrarModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black bg-opacity-75" 
         wire:key="modal-overlay"
         wire:click="fecharModal"
         style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;">
        <div class="relative bg-white rounded-lg shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto" 
             wire:key="modal-content"
             wire:click.stop>
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Fale Conosco</h3>
                    <p class="text-sm text-gray-500 mt-1">Envie sua dúvida, sugestão ou reporte um problema. Responderemos o mais rápido possível!</p>
                </div>
                <button type="button"
                        wire:click="fecharModal" 
                        class="text-gray-400 hover:text-gray-500 transition cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="salvar" class="space-y-6">
                <!-- Assunto -->
                <div>
                    <label for="assunto" class="block text-sm font-medium text-gray-700 mb-2">
                        Assunto *
                    </label>
                    <input 
                        type="text" 
                        id="assunto"
                        wire:model.live="assunto"
                        autocomplete="off"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('assunto') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Ex: Dúvida sobre agenda, Sugestão de melhoria, Problema técnico..."
                    />
                    @error('assunto')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Mensagem -->
                <div>
                    <label for="mensagem" class="block text-sm font-medium text-gray-700 mb-2">
                        Mensagem *
                    </label>
                    <textarea 
                        id="mensagem"
                        wire:model.live="mensagem"
                        rows="6"
                        autocomplete="off"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('mensagem') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Descreva sua dúvida, sugestão ou problema com o máximo de detalhes possível..."
                    ></textarea>
                    @error('mensagem')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        Mínimo de 10 caracteres. Máximo de 2000 caracteres.
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button 
                        type="button" 
                        wire:click="fecharModal"
                        class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition cursor-pointer"
                    >
                        Cancelar
                    </button>
                    <x-ui.loading-button 
                        type="submit"
                        wire:target="salvar"
                        class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-medium transition shadow-lg hover:shadow-xl"
                    >
                        Enviar Feedback
                    </x-ui.loading-button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
