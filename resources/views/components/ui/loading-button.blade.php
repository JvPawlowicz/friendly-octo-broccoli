@props(['target' => null, 'loadingText' => null, 'spinnerColor' => 'white'])

@php
    // Se target não foi especificado, tenta extrair de wire:click
    if (!$target && $attributes->has('wire:click')) {
        $target = $attributes->get('wire:click');
    }
    
    $wireLoading = $target ? "wire:loading wire:target=\"{$target}\"" : 'wire:loading';
    $wireLoadingRemove = $target ? "wire:loading.remove wire:target=\"{$target}\"" : 'wire:loading.remove';
    $wireLoadingClass = $target ? "wire:loading.class=\"opacity-50 cursor-not-allowed\" wire:target=\"{$target}\"" : 'wire:loading.class="opacity-50 cursor-not-allowed"';
@endphp

<button {{ $attributes->merge(['class' => 'inline-flex items-center justify-center']) }} {{ $wireLoadingClass }}>
    <span {{ $wireLoadingRemove }}>{{ $slot }}</span>
    
    <span {{ $wireLoading }} class="inline-flex items-center">
        <x-ui.loading-spinner size="sm" color="{{ $spinnerColor }}" />
        @if($loadingText)
            <span class="ml-2">{{ $loadingText }}</span>
        @endif
    </span>
</button>

