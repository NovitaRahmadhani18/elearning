@props([
    'value',
])

<div>
    @if ($value)
        <span
            class="inline-flex items-center rounded-full bg-green-100 px-4 py-1 text-xs font-semibold leading-5 text-green-800"
        >
            Active
        </span>
    @else
        <span
            class="inline-flex items-center rounded-full bg-red-100 px-4 py-1 text-xs font-semibold leading-5 text-red-800"
        >
            Inactive
        </span>
    @endif
</div>
