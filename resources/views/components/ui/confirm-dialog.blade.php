@props(['id', 'title', 'message', 'confirmText' => 'Confirmar', 'cancelText' => 'Cancelar', 'type' => 'danger'])

@php
    $colors = [
        'danger' => ['bg' => 'bg-red-600', 'hover' => 'hover:bg-red-700', 'text' => 'text-white'],
        'warning' => ['bg' => 'bg-yellow-600', 'hover' => 'hover:bg-yellow-700', 'text' => 'text-white'],
        'info' => ['bg' => 'bg-blue-600', 'hover' => 'hover:bg-blue-700', 'text' => 'text-white'],
    ];
    $color = $colors[$type] ?? $colors['danger'];
@endphp

<div id="{{ $id }}" 
     class="hidden fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             onclick="document.getElementById('{{ $id }}').classList.add('hidden')"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full {{ $color['bg'] }} sm:mx-0 sm:h-10 sm:w-10">
                        @if($type === 'danger')
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @elseif($type === 'warning')
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $title }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                {{ $message }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        onclick="window.confirmDialogCallback && window.confirmDialogCallback(); document.getElementById('{{ $id }}').classList.add('hidden');"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 {{ $color['bg'] }} text-base font-medium {{ $color['text'] }} {{ $color['hover'] }} focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ $confirmText }}
                </button>
                <button type="button" 
                        onclick="document.getElementById('{{ $id }}').classList.add('hidden'); window.confirmDialogCallback = null;"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ $cancelText }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.showConfirmDialog = function(dialogId, callback) {
        window.confirmDialogCallback = callback;
        document.getElementById(dialogId).classList.remove('hidden');
    };
</script>

