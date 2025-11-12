@props(['documento'])

<div class="border-l-4 border-purple-500 pl-4 py-4">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center space-x-2 mb-2">
                <span class="px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-800">
                    Documento
                </span>
                @if($documento->categoria)
                    <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                        {{ $documento->categoria }}
                    </span>
                @endif
            </div>
            
            <h4 class="font-semibold text-gray-900 mb-1">
                {{ $documento->titulo_documento }}
            </h4>
            
            <p class="text-sm text-gray-600 mb-2">
                Upload por: {{ $documento->user->name ?? 'N/A' }}
            </p>
            
            <div class="mt-3">
                <a href="{{ asset('storage/' . $documento->path_arquivo) }}" 
                   target="_blank"
                   class="inline-flex items-center px-3 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded hover:bg-purple-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Abrir Documento
                </a>
            </div>
            
            <p class="text-xs text-gray-500 mt-2">
                {{ $documento->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
</div>

