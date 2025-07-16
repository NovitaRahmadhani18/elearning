@props(['active' => false, 'icon' => null])

@php
    $classes = $active
        ? 'flex items-center rounded-sm bg-secondary px-4 py-2 text-zinc-600'
        : 'flex items-center rounded-sm px-4 py-2 text-gray-600 transition-colors hover:bg-secondary';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <span class="mr-3">{{ $icon }}</span>
    @endif

    <span>{{ $slot }}</span>
</a>
