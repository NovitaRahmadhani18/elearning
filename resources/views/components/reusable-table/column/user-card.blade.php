@props([
    'value',
])

<div class="flex items-center space-x-2">
    <div class="h-10 w-10 overflow-hidden rounded-full border border-primary/50 bg-gray-300">
        <img
            class="rounded-full border border-neutral-200 object-cover"
            src="{{ $value?->profile_photo_url }}"
            alt="{{ $value?->name }}"
        />
    </div>
    <div>
        <div class="text-sm font-medium text-gray-900">{{ $value?->fullName }}</div>
        <div class="text-xs text-gray-500">{{ $value?->email }}</div>
    </div>
</div>
