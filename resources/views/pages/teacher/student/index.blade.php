<x-layouts.teacher-layout>
    <x-slot name="header">Student Tracking</x-slot>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Total Students Card -->
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="mb-2 text-sm font-medium text-gray-500">Total Students</h3>
            <p class="text-4xl font-bold text-gray-900">{{ $studentCount }}</p>
        </div>

        <!-- Average Completion Rate Card -->
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="mb-2 text-sm font-medium text-gray-500">Average Completion Rate</h3>
            <p class="text-4xl font-bold text-gray-900">{{ $completionRate }} %</p>
        </div>
    </div>

    <!-- Student Performance Table -->
    <x-reusable-table :tableData="$tableData" title="Student Performance" />
</x-layouts.teacher-layout>
