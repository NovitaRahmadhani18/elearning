<x-layouts.user-layout>
    <x-slot name="header">Lencana</x-slot>
    <x-slot name="username">{{ auth()->user()->name }}</x-slot>
    <x-slot name="role">{{ ucfirst(auth()->user()->first_role) }}</x-slot>

    <x-slot name="navbar">
        <div class="flex space-x-8">
            <a href="{{ route('user.dashboard') }}" class="flex items-center text-gray-600 hover:text-primary">
                <x-gmdi-home class="mr-2 h-5 w-5" />
                Dashboard
            </a>
            <a href="#" class="flex items-center text-gray-600 hover:text-primary">
                <x-gmdi-book class="mr-2 h-5 w-5" />
                My Class
            </a>
            <a href="{{ route('user.leaderboard.index') }}" class="flex items-center text-gray-600 hover:text-primary">
                <x-gmdi-bar-chart class="mr-2 h-5 w-5" />
                Leaderboard
            </a>
            <a href="{{ route('user.lencana.index') }}" class="flex items-center border-b-2 border-primary font-medium text-primary">
                <x-gmdi-emoji-events class="mr-2 h-5 w-5" />
                Lencana
            </a>
        </div>
    </x-slot>

    <livewire:user-achievements />
</x-layouts.user-layout>
