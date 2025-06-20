@props([
    'value',
])

<div>
    {{ \Carbon\Carbon::parse($value)->format('M d, Y') }}
</div>
