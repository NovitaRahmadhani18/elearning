<div class="hidden sm:ms-6 sm:flex sm:items-center">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button
                class="inline-flex items-center rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
            >
                <div class="mr-2 h-8 w-8 rounded-full bg-gray-200"></div>
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
