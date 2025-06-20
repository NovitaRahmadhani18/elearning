@props(['title', 'icon'])

<div class="flex flex-col items-center">
    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mb-2">
        {!! $icon !!}
    </div>
    <p class="text-xs text-center text-gray-700">{{ $title }}</p>
</div> 