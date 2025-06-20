@props([
    'value',
])

<div class="flex items-center space-x-2">
    <div class="h-10 w-10 overflow-hidden rounded-full border border-primary/50 bg-gray-300">
        <img src="{{ $value->profilePhotoUrl }}" alt="{{ $value->name }}" class="h-full w-full object-fill" lazy />
    </div>
    <div>
        <div class="text-sm font-medium text-gray-900">{{ $value->fullName }}</div>
        <div class="text-xs text-gray-500">{{ $value->email }}</div>
    </div>
</div>
