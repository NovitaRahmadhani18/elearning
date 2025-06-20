@props([
    'value',
])

<div>
    @if ($value)
        {{ \Carbon\Carbon::parse($value)->diffForHumans() }}
    @endif
</div>
