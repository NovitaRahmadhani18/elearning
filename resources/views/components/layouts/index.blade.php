<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('app.name', 'Darma Bangsa E-Learning') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
            rel="stylesheet"
        />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @trixassets
        @livewireStyles
    </head>

    <body
        class="bg-primary antialiased"
        x-data="{
            notifications: [],
        }"
        @notify.window="
              notifications.push({
                  id: Date.now(),
                  type: $event.detail.type,
                  message: $event.detail.message
              });
              setTimeout(() => {
                  notifications = notifications.filter(n => n.id !== $event.detail.id);
              }, 5000);
          "
    >
        {{ $slot }}

        <div x-sync id="sync" class="hidden">
            @if ($event = Session::get('event'))
                <div x-init="$dispatch('{{ $event }}')"></div>
            @endif
        </div>

        @livewireScriptConfig
        @stack('scripts')
    </body>
</html>
