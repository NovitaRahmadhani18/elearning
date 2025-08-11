<!-- Mobile Profile Button -->
<div class="block sm:hidden">
    <div x-data="{ open: false }" class="relative">
        <button
            @click="open = !open"
            class="flex items-center rounded-lg p-2 text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300"
        >
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300">
                <span class="text-xs font-medium text-gray-700">
                    @if (auth()->user()->profile_photo_path)
                        <img
                            src="{{ auth()->user()->profile_photo_url }}"
                            alt="{{ auth()->user()->name }}"
                            class="h-8 w-8 rounded-full object-cover"
                        />
                    @else
                        {{ auth()->user()->initials() }}
                    @endif
                </span>
            </div>
        </button>

        <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition duration-200 ease-out"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition duration-150 ease-in"
            x-transition:leave-start="scale-100 opacity-100"
            x-transition:leave-end="scale-95 opacity-0"
            class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        >
            <div class="border-b border-gray-200 px-4 py-3">
                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'User' }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->firstRole ?? '' }}</p>
            </div>

            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                    onclick="event.preventDefault(); this.closest('form').submit();"
                >
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Desktop Profile Dropdown -->
<div class="hidden sm:ms-6 sm:flex sm:items-center">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button
                class="inline-flex items-center rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
            >
                <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-full bg-gray-200">
                    @if (
                    auth()->user()->profile_photo_path                    )
                        <img
                            src="{{ auth()->user()->profile_photo_url }}"
                            alt="{{ auth()->user()->name }}"
                            class="h-8 w-8 rounded-full object-cover"
                        />
                    @else
                        <span class="text-xs font-medium text-gray-700">
                            {{ auth()->user()->initials() }}
                        </span>
                    @endif
                </div>
                <div class="text-left">
                    <p class="text-sm font-medium">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->firstRole ?? '' }}</p>
                </div>

                <div class="ms-1">
                    <x-gmdi-keyboard-arrow-down-o class="h-5 w-5" />
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit')">
                {{ __('Profile') }}
            </x-dropdown-link>

            <!-- Authentication -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-dropdown-link
                    :href="route('logout')"
                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                >
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</div>
