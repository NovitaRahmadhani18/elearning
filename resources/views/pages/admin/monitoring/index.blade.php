<x-layouts.admin-layout>
    <x-slot name="header">Class</x-slot>

    <!-- Time Filter -->
    <div class="mb-6">
        <button
            class="flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            <x-gmdi-calendar-month-r class="h-5 w-5" />

            Last 7 days
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Daily Active Users Card -->
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="text-sm font-medium text-gray-600">Daily Active Users</h3>
            <p class="mt-2 text-3xl font-bold text-gray-800">580</p>
        </div>

        <!-- Course Completion Rate Card -->
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="text-sm font-medium text-gray-600">Course Completion Rate</h3>
            <p class="mt-2 text-3xl font-bold text-gray-800">54%</p>
        </div>
    </div>

    <x-reusable-table :$tableData title="Activity Logs" />
</x-layouts.admin-layout>
