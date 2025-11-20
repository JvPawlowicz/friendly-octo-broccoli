@props(['target' => null, 'message' => 'Carregando...'])

@php
    $wireLoading = $target ? "wire:loading wire:target=\"{$target}\"" : 'wire:loading';
@endphp

<div {{ $wireLoading }} 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="bg-white rounded-lg shadow-xl p-6 flex flex-col items-center gap-4 min-w-[200px]">
        <x-ui.loading-spinner size="lg" color="indigo" />
        <p class="text-sm font-medium text-gray-700">{{ $message }}</p>
    </div>
</div>

