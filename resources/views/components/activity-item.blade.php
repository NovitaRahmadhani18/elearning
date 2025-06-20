@props(['user', 'action', 'time'])

<div class="flex items-center py-4">
    <div class="w-10 h-10 bg-gray-200 rounded-full mr-4 flex-shrink-0"></div>
    <div>
        <p class="text-gray-800">{{ $user }} {{ $action }}</p>
        <p class="text-sm text-gray-500">{{ $time }}</p>
    </div>
</div> 