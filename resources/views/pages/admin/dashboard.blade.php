<x-layouts.admin-layout>
    <x-slot name="header">Dashboard</x-slot>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <x-stat-card
            title="Total Users"
            :value="$totalUsers"
            :icon="svg('gmdi-people', 'h-6 w-6 text-secondary-dark')"
        />
        <x-stat-card
            title="Active Class"
            :value="$totalClassrooms"
            :icon="svg('fas-book', 'h-6 w-6 text-secondary-dark')"
        />
        <x-stat-card
            title="Completions"
            :value="$totalCompletedContents"
            :icon="svg('fas-graduation-cap', 'h-6 w-6 text-secondary-dark')"
        />
    </div>

    <!-- Recent Activities -->
    <div class="rounded-lg border border-primary/20 bg-white p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-800">Recent Activities</h2>

        <div class="border-t border-gray-100">
            @forelse ($activities as $activity)
                <x-activity-item
                    :user="$activity?->causer"
                    :action="$activity->description"
                    :time="$activity->created_at->diffForHumans()"
                />
            @empty
                <p class="text-gray-500">No recent activities found.</p>
            @endforelse
        </div>
    </div>
</x-layouts.admin-layout>
