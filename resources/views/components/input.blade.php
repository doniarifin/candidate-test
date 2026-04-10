@props([
    'label' => '',
    'name',
    'placeholder' => null,
    'type' => 'text',
    'disabled' => false,
    'required' => false,
    'model' => null,
])

<div >
    <label class="block text-sm text-gray-600 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder ?? 'Type ' . $label . '..' }}"
        x-model="{{ $model }}"
        @disabled($disabled)
        {{ $attributes->merge([
            'class' => 'w-full border border-gray-300 rounded-lg px-3 py-2 
                        focus:ring-2 focus:ring-green-500 focus:outline-none
                        disabled:bg-gray-100 disabled:cursor-not-allowed'
        ]) }}
    >

    <p class="text-red-500 text-sm" x-text="errors?.['{{ $name }}']?.[0]"></p>
</div>