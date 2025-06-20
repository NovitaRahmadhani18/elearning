@props([
    'icon',
    'title',
    'value',
])
<div class="flex items-center rounded-lg border border-primary/30 bg-white p-6">
    <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-secondary/30">
        {{ $icon }}
    </div>
    <div>
        <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
        <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
    </div>
</div>
