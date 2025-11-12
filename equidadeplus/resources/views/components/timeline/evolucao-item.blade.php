@props(['evolucao'])

<div class="border-l-4 border-blue-500 pl-4 py-4">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center space-x-2 mb-2">
                <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                    Evolução Clínica
                </span>
                @if($evolucao->status === 'Finalizado')
                    <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                        Finalizado
                    </span>
                @else
                    <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">
                        Rascunho
                    </span>
                @endif
            </div>
            
            <h4 class="font-semibold text-gray-900 mb-1">
                Evolução Clínica
            </h4>
            
            <p class="text-sm text-gray-600 mb-2">
                Profissional: {{ $evolucao->profissional->name }}
            </p>
            
            @if($evolucao->atendimento)
                <p class="text-xs text-gray-500 mb-2">
                    Atendimento: {{ $evolucao->atendimento->data_hora_inicio->format('d/m/Y H:i') }}
                </p>
            @endif
            
            @if($evolucao->relato_clinico)
                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-1">Relato Clínico:</p>
                    <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ Str::limit($evolucao->relato_clinico, 200) }}</p>
                </div>
            @endif
            
            @if($evolucao->conduta)
                <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-1">Conduta:</p>
                    <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ Str::limit($evolucao->conduta, 150) }}</p>
                </div>
            @endif
            
            @if($evolucao->objetivos)
                <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-1">Objetivos:</p>
                    <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ Str::limit($evolucao->objetivos, 150) }}</p>
                </div>
            @endif
            
            <p class="text-xs text-gray-500 mt-2">
                {{ ($evolucao->finalizado_em ?? $evolucao->created_at)->format('d/m/Y H:i') }}
                @if($evolucao->finalizado_em)
                    <span class="text-green-600">• Finalizado</span>
                @endif
            </p>
            
            <!-- Adendos Aninhados -->
            @if($evolucao->adendos && $evolucao->adendos->count() > 0)
                <div class="mt-4 ml-4 border-l-2 border-gray-300 pl-4 space-y-3">
                    <p class="text-xs font-semibold text-gray-600 uppercase">Adendos:</p>
                    @foreach($evolucao->adendos as $adendo)
                        <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">
                                    Adendo
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $adendo->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            @if($adendo->relato_clinico)
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ Str::limit($adendo->relato_clinico, 200) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
            
            @if($evolucao->status === 'Finalizado')
                <div class="mt-3">
                    <a href="{{ route('app.evolucoes.create', ['evolucao_pai_id' => $evolucao->id]) }}" 
                       class="inline-flex items-center px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded hover:bg-yellow-200">
                        + Adicionar Adendo
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

