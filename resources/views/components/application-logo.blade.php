@php
    $logoPath = 'images/logo.png';
    $logoExists = file_exists(public_path($logoPath));
@endphp

@if($logoExists)
    <img src="{{ asset($logoPath) }}" alt="{{ config('app.name', 'Equidade') }}" {{ $attributes->merge(['class' => 'h-auto']) }} style="max-height: 60px;">
@else
    {{-- Fallback: SVG do Laravel ou texto --}}
    <div {{ $attributes->merge(['class' => 'text-2xl font-bold text-indigo-600']) }}>
        {{ config('app.name', 'Equidade') }}
    </div>
@endif
