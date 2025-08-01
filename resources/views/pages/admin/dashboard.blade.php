<x-layouts.admin-layout>
    <x-slot name="header">Dashboard</x-slot>
    <x-slot name="username">Joshua</x-slot>
    <x-slot name="role">Administrator</x-slot>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <x-stat-card title="Total Users" value="215" :icon="svg('gmdi-people', 'h-6 w-6 text-secondary-dark')" />
        <x-stat-card title="Active Class" value="3" :icon="svg('fas-book', 'h-6 w-6 text-secondary-dark')" />
        <x-stat-card
            title="Completions"
            value="215"
            :icon="svg('fas-graduation-cap', 'h-6 w-6 text-secondary-dark')"
        />
    </div>

    <!-- Recent Activities -->
    <div class="rounded-lg border border-primary/20 bg-white p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-800">Recent Activities</h2>

        <div class="border-t border-gray-100">
            <x-activity-item user="Sarah" action="completed Teknologi Informasi" time="2 hours ago" />
            <x-activity-item user="Sarah" action="completed Teknologi Informasi" time="2 hours ago" />
            <x-activity-item user="Sarah" action="completed Teknologi Informasi" time="2 hours ago" />
        </div>
    </div>
</x-layouts.admin-layout>
