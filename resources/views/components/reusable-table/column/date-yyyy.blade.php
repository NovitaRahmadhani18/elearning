@props([
    'value',
])

<div>
    {{ \Carbon\Carbon::parse($value)->toDateTimeString() }}
</div>
