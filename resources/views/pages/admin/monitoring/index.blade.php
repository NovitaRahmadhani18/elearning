<x-layouts.admin-layout>
    <x-slot name="header">Monitoring</x-slot>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Daily Active Users Card -->
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="text-sm font-medium text-gray-600">Daily Active Users</h3>
            <p class="mt-2 text-3xl font-bold text-gray-800">{{ $dailyActiveUsers }}</p>
        </div>

        <!-- Course Completion Rate Card -->
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="text-sm font-medium text-gray-600">Course Completion Rate</h3>
            <p class="mt-2 text-3xl font-bold text-gray-800">54%</p>
        </div>
    </div>

    <x-reusable-table :$tableData title="Activity Logs" />
</x-layouts.admin-layout>
