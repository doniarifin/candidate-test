@props([
    'label' => '',
    'name',
    'options' => [], // array ['value' => 'Label']
    'placeholder' => 'Select option',
    'model' => null,
    'required' => false,
    'disabled' => false,
    'errorKey' => null
])

<div class="mb-4">
  
    <label class="block text-sm text-gray-600 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <select 
        name="{{ $name }}"
        x-model="{{ $model ?? 'form.' . $name }}"
        @disabled($disabled)

        :class="errors?.['{{ $errorKey ?? $name }}']?.length 
            ? 'border-red-500 focus:ring-red-500' 
            : 'border-gray-300 focus:ring-green-500'"

        class="w-full border rounded-lg px-3 py-2 
               focus:ring-2 focus:outline-none
               disabled:bg-gray-100 disabled:cursor-not-allowed"
    >
        <option :value="null">{{ $placeholder }}</option>

        <!-- Options -->
        @foreach($options as $value => $text)
            <option value="{{ $value }}">
                {{ $text }}
            </option>
        @endforeach
    </select>

    <p 
        class="text-red-500 text-sm mt-1"
        x-show="errors?.['{{ $errorKey ?? $name }}']?.length"
        x-text="errors?.['{{ $errorKey ?? $name }}']?.[0]"
    ></p>
</div>