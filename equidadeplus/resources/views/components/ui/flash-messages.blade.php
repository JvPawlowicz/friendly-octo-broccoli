@php
    $alerts = [
        'message' => ['color' => 'green', 'icon' => 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z'],
        'error' => ['color' => 'red', 'icon' => 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z'],
    ];
@endphp

<div class="fixed top-20 right-6 z-40 space-y-2 pointer-events-none">
    @foreach ($alerts as $key => $style)
        @if(session($key))
            <div class="pointer-events-auto bg-white border-l-4 border-{{ $style['color'] }}-500 shadow-xl rounded-xl p-4 w-80 animate-slide-in-right" role="alert">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-{{ $style['color'] }}-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="{{ $style['icon'] }}" clip-rule="evenodd" />
                    </svg>
                    <div class="flex-1 text-sm text-gray-700 leading-relaxed">{{ session($key) }}</div>
                    <button type="button" onclick="this.closest('div[role=alert]').remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    @endforeach
</div>
