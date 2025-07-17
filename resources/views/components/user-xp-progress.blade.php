@props(['user' => null])

@php
    $currentUser = $user ?? auth()->user();
    $currentPoints = $currentUser->getPoints() ?? 0;
@endphp

<div class="flex items-center space-x-2 rounded-lg bg-gradient-to-r from-secondary/10 to-secondary/5 px-4 py-2">
    <!-- Points Display -->
    <div class="flex items-center space-x-1">
        <x-gmdi-stars class="h-5 w-5 text-secondary" />
        <span class="text-sm font-medium text-secondary-dark">{{ number_format($currentPoints) }} points</span>
    </div>
</div>
