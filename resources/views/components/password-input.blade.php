@props(['id', 'name', 'required' => false, 'placeholder' => '', 'autocomplete' => '', 'class' => ''])
<div x-data="{ show: false }" class="relative">
    <input
        :type="show ? 'text' : 'password'"
        id="{{ $id }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $autocomplete ? 'autocomplete=' . $autocomplete : '' }}
        placeholder="{{ $placeholder }}"
        class="w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{$class}} pr-12"
    />
    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary focus:outline-none">
        <template x-if="!show">
            <x-gmdi-visibility-off-o class="h-5 w-5" />
        </template>
        <template x-if="show">
            <x-gmdi-visibility-o class="h-5 w-5" />
        </template>
    </button>
</div> 