@props(['active' => false, 'icon' => null])

@php
    $classes = $active
        ? 'flex items-center rounded-sm bg-secondary px-4 py-3 text-sm text-zinc-600 min-h-[44px] touch-manipulation'
        : 'flex items-center rounded-sm px-4 py-3 text-sm text-gray-600 transition-colors hover:bg-secondary min-h-[44px] touch-manipulation';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <span class="mr-3 flex-shrink-0">{{ $icon }}</span>
    @endif

    <span class="truncate">{{ $slot }}</span>
</a>
