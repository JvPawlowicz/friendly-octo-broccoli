@props(['avaliacao'])

<div class="border-l-4 border-green-500 pl-4 py-4">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center space-x-2 mb-2">
                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                    Avaliação
                </span>
                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                    Finalizado
                </span>
            </div>
            
            <h4 class="font-semibold text-gray-900 mb-1">
                {{ $avaliacao->template->nome_template }}
            </h4>
            
            <p class="text-sm text-gray-600 mb-2">
                Profissional: {{ $avaliacao->profissional->name }}
            </p>
            
            @if($avaliacao->respostas && $avaliacao->respostas->count() > 0)
                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-600 uppercase mb-2">Respostas:</p>
                    <div class="space-y-2">
                        @foreach($avaliacao->respostas->take(3) as $resposta)
                            <div class="text-sm">
                                <span class="font-medium text-gray-700">{{ $resposta->pergunta->titulo_pergunta ?? 'Pergunta #' . $resposta->avaliacao_pergunta_id }}:</span>
                                <span class="text-gray-600">{{ Str::limit($resposta->resposta, 100) }}</span>
                            </div>
                        @endforeach
                        @if($avaliacao->respostas->count() > 3)
                            <p class="text-xs text-gray-500 italic">... e mais {{ $avaliacao->respostas->count() - 3 }} resposta(s)</p>
                        @endif
                    </div>
                </div>
            @endif
            
            <p class="text-xs text-gray-500 mt-2">
                {{ $avaliacao->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
</div>

