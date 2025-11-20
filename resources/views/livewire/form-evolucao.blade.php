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
                                <label for="relato_clinico" class="block text-sm font-medium text-gray-700 mb-2">
                                    Relato Clínico *
                                </label>
                                <div class="border border-gray-300 rounded-md shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 overflow-hidden">
                                    <div id="editor-relato" 
                                         class="min-h-[200px] p-4 prose prose-sm max-w-none focus:outline-none"
                                         wire:ignore></div>
                                </div>
                                <textarea wire:model="relato_clinico" 
                                          id="relato_clinico" 
                                          class="hidden"
                                          required></textarea>
                                @error('relato_clinico') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="conduta" class="block text-sm font-medium text-gray-700 mb-2">Conduta</label>
                                <div class="border border-gray-300 rounded-md shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 overflow-hidden">
                                    <div id="editor-conduta" 
                                         class="min-h-[150px] p-4 prose prose-sm max-w-none focus:outline-none"
                                         wire:ignore></div>
                                </div>
                                <textarea wire:model="conduta" 
                                          id="conduta" 
                                          class="hidden"></textarea>
                            </div>

                            <div>
                                <label for="objetivos" class="block text-sm font-medium text-gray-700 mb-2">Objetivos</label>
                                <div class="border border-gray-300 rounded-md shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 overflow-hidden">
                                    <div id="editor-objetivos" 
                                         class="min-h-[150px] p-4 prose prose-sm max-w-none focus:outline-none"
                                         wire:ignore></div>
                                </div>
                                <textarea wire:model="objetivos" 
                                          id="objetivos" 
                                          class="hidden"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between">
                            @if ($isAdendo)
                                <x-ui.loading-button 
                                    type="submit"
                                    target="salvarAdendo"
                                    loading-text="Salvando..."
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    Salvar Adendo
                                </x-ui.loading-button>
                            @else
                                @if(!$isFinalizado)
                                    <x-ui.loading-button 
                                        type="button"
                                        wire:click="salvarRascunho"
                                        target="salvarRascunho"
                                        loading-text="Salvando..."
                                        class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        Salvar Rascunho
                                    </x-ui.loading-button>
                                    <x-ui.loading-button 
                                        type="submit"
                                        target="finalizar"
                                        loading-text="Finalizando..."
                                        class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        Finalizar
                                    </x-ui.loading-button>
                                @endif
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.initTiptapEditor) {
            console.warn('Tiptap não está disponível. Usando textarea padrão.');
            return;
        }

        const wireId = @this.id;
        const component = window.Livewire.find(wireId);

        // Inicializar editores com toolbar
        const editorRelato = window.initTiptapEditor(
            'editor-relato',
            @js($relato_clinico),
            'Digite o relato clínico...',
            component,
            'relato_clinico'
        );

        const editorConduta = window.initTiptapEditor(
            'editor-conduta',
            @js($conduta),
            'Digite a conduta...',
            component,
            'conduta'
        );

        const editorObjetivos = window.initTiptapEditor(
            'editor-objetivos',
            @js($objetivos),
            'Digite os objetivos...',
            component,
            'objetivos'
        );

        // Sincronizar com textareas ocultos
        if (editorRelato) {
            editorRelato.on('update', () => {
                const html = editorRelato.getHTML();
                document.getElementById('relato_clinico').value = html;
                component.set('relato_clinico', html);
            });
        }

        if (editorConduta) {
            editorConduta.on('update', () => {
                const html = editorConduta.getHTML();
                document.getElementById('conduta').value = html;
                component.set('conduta', html);
            });
        }

        if (editorObjetivos) {
            editorObjetivos.on('update', () => {
                const html = editorObjetivos.getHTML();
                document.getElementById('objetivos').value = html;
                component.set('objetivos', html);
            });
        }

        // Atualizar editores quando Livewire atualizar
        Livewire.hook('morph.updated', ({ el, component }) => {
            if (editorRelato && component.get('relato_clinico')) {
                editorRelato.commands.setContent(component.get('relato_clinico'));
            }
            if (editorConduta && component.get('conduta')) {
                editorConduta.commands.setContent(component.get('conduta'));
            }
            if (editorObjetivos && component.get('objetivos')) {
                editorObjetivos.commands.setContent(component.get('objetivos'));
            }
        });
    });
</script>
@endpush
