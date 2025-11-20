@props(['label', 'name', 'type' => 'text', 'required' => false, 'help' => null])

@php
    $fieldName = $name ?? $attributes->get('wire:model');
    $hasError = $errors->has($fieldName);
    $inputClasses = 'mt-1 block w-full rounded-md shadow-sm transition-colors ' . 
                    ($hasError 
                        ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                        : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500');
@endphp

<div>
    <label for="{{ $fieldName }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <input 
        type="{{ $type }}"
        id="{{ $fieldName }}"
        name="{{ $fieldName }}"
        {{ $attributes->merge(['class' => $inputClasses]) }}
    />
    
    @if($help && !$hasError)
        <p class="mt-1 text-sm text-gray-500">{{ $help }}</p>
    @endif
    
    @error($fieldName)
        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>

