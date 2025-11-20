<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Central de Ajuda</h1>
                            <p class="text-gray-600 mt-1">Envie sua dúvida, sugestão ou reporte um problema. Estamos aqui para ajudar!</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Formulário de Feedback -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Enviar Feedback</h2>
                        
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
                                    rows="8"
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
                            <div class="flex justify-end">
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

                <!-- Meus Feedbacks -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Meus Feedbacks</h2>
                        
                        @if($meusFeedbacks->isEmpty())
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500">Você ainda não enviou nenhum feedback.</p>
                                <p class="text-sm text-gray-400 mt-2">Seus feedbacks aparecerão aqui após o envio.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($meusFeedbacks as $feedback)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-semibold text-gray-900">{{ $feedback->assunto }}</h3>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($feedback->status === 'pendente') bg-yellow-100 text-yellow-800
                                                @elseif($feedback->status === 'em_andamento') bg-blue-100 text-blue-800
                                                @elseif($feedback->status === 'resolvido') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $feedback->statusLabel }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $feedback->mensagem }}</p>
                                        <div class="flex justify-between items-center text-xs text-gray-500">
                                            <span>{{ $feedback->created_at->format('d/m/Y H:i') }}</span>
                                            @if($feedback->resposta)
                                                <span class="text-indigo-600 font-medium">✓ Respondido</span>
                                            @endif
                                        </div>
                                        @if($feedback->resposta)
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <p class="text-xs font-medium text-gray-700 mb-1">Resposta:</p>
                                                <p class="text-sm text-gray-600">{{ $feedback->resposta }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                {{ $meusFeedbacks->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informações Úteis -->
            <div class="mt-6 bg-gradient-to-br from-indigo-50 to-purple-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informações Úteis</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Tempo de Resposta</h3>
                            </div>
                            <p class="text-sm text-gray-600">Respondemos em até 48 horas úteis.</p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Acompanhamento</h3>
                            </div>
                            <p class="text-sm text-gray-600">Acompanhe o status dos seus feedbacks nesta página.</p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Privacidade</h3>
                            </div>
                            <p class="text-sm text-gray-600">Seus dados são tratados com total confidencialidade.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

