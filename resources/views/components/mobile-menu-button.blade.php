@props(['isOpen' => false])

<button
    {{ $attributes->merge(['class' => 'relative inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200']) }}
    aria-label="Toggle menu" type="button">
    <span class="sr-only">Open main menu</span>

    <!-- Hamburger Icon -->
    <div class="relative h-6 w-6">
        <!-- Top Line -->
        <span
            :class="(typeof isOpen !== 'undefined' ? isOpen : {{ $isOpen ? 'true' : 'false' }}) ? 'rotate-45 translate-y-2' :
            'rotate-0 translate-y-0'"
            class="absolute left-0 top-1 block h-0.5 w-6 transform rounded-full bg-current transition-all duration-300 ease-out"></span>

        <!-- Middle Line -->
        <span
            :class="(typeof isOpen !== 'undefined' ? isOpen : {{ $isOpen ? 'true' : 'false' }}) ? 'opacity-0' : 'opacity-100'"
            class="absolute left-0 top-3 block h-0.5 w-6 transform rounded-full bg-current transition-all duration-300 ease-out"></span>

        <!-- Bottom Line -->
        <span
            :class="(typeof isOpen !== 'undefined' ? isOpen : {{ $isOpen ? 'true' : 'false' }}) ? '-rotate-45 -translate-y-2' :
            'rotate-0 translate-y-0'"
            class="absolute left-0 top-5 block h-0.5 w-6 transform rounded-full bg-current transition-all duration-300 ease-out"></span>
    </div>
</button>
