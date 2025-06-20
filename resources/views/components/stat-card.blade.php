@props([
    'title',
    'value',
    'icon',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg p-6 border border-primary/30']) }}>
    <div class="flex items-center">
        <div class="mr-4">
            <div class="rounded-lg bg-secondary-light/30 p-3">
                {{ $icon }}
            </div>
        </div>
        <div>
            <p class="text-sm text-gray-600">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
        </div>
    </div>
</div>
