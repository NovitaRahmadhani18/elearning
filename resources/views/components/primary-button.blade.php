<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full px-4 py-2 text-white font-semibold  bg-primary hover:bg-primary-dark transition duration-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-dark']) }}
>
    {{ $slot }}
</button>
