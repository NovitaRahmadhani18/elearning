<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8 text-center">
        <h1 class="mb-2 text-2xl font-bold text-gray-800">Welcome back</h1>
        <p class="text-gray-600">Please enter your details to sign in</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-6">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-6">
            <x-input-label for="password" :value="__('Password')" />
            <x-password-input id="password" name="password" required autocomplete="current-password" class="mt-1" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me and Forgot Password -->
        <div class="mb-6 flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <x-checkbox id="remember_me" name="remember" />
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    class="text-sm text-primary-dark hover:text-primary-dark focus:underline focus:outline-none"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot password') }}
                </a>
            @endif
        </div>

        <x-primary-button>
            {{ __('Sign in') }}
        </x-primary-button>

        @if (Route::has('register'))
            <div class="mt-6 text-center">
                <a
                    href="{{ route('register') }}"
                    class="text-sm text-gray-600 hover:text-primary focus:underline focus:outline-none"
                >
                    {{ __('Create an account') }}
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
