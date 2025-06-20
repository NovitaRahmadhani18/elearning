@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary']) !!}>
