@props([
    'label',
    'color',
])

<div
    @class([
        'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white',
        'bg-green-500' => $label == 'Admin',
        'bg-red-500' => $label == 'Teacher',
        'bg-cyan-500' => $label == 'User',
    ])
>
    {{ $label }}
</div>
