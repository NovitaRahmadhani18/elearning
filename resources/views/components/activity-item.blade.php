@props([
    'user',
    'action',
    'time',
])

<div class="flex items-center py-4">
    <div class="mr-4 h-10 w-10 flex-shrink-0 rounded-full bg-gray-200">
        <img
            class="rounded-full border border-neutral-200 object-cover"
            src="{{ $user->profile_photo_url }}"
            alt="{{ $user->name }}"
        />
    </div>
    <div>
        <p class="text-gray-800">{{ $user->name }} {{ $action }}</p>
        <p class="text-sm text-gray-500">{{ $time }}</p>
    </div>
</div>
