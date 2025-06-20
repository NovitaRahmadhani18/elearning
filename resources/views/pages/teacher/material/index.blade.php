<x-layouts.teacher-layout>
    <x-slot name="header">Material Creation</x-slot>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
        <x-stat-card title="Total Materials" value="24" :icon="svg('fas-book', 'h-6 w-6 text-gray-600')" />
        <x-stat-card title="Student Engagement" value="85%" :icon="svg('fas-chart-line', 'h-6 w-6 text-gray-600')" />
    </div>

    <!-- Course Materials -->
    <div class="mb-8 rounded-lg border border-primary/20 bg-white p-6">
        <h2 class="mb-6 text-lg font-semibold text-gray-800">Course Materials</h2>

        <div class="mb-2 flex w-full items-end justify-end">
            <a href="{{ route('teacher.material.create') }}">
                <button
                    class="flex items-center space-x-2 rounded-md bg-primary px-4 py-2 text-white hover:bg-primary-dark"
                >
                    <x-gmdi-add class="h-5 w-5 text-white" />
                    <span>
                        <span class="hidden sm:inline">Add Material</span>
                    </span>
                </button>
            </a>
        </div>

        <!-- Search Box -->
        <div class="relative mb-6">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <x-gmdi-search class="h-5 w-5 text-gray-400" />
            </div>
            <input
                type="text"
                class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-10 pr-3 leading-5 placeholder-gray-500 focus:border-primary focus:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary sm:text-sm"
                placeholder="Search materials..."
            />
        </div>

        <!-- Materials Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($materials as $material)
                <x-material-card :$material />
            @empty
                <div class="col-span-1 text-center md:col-span-2 lg:col-span-3">
                    <p class="text-gray-500">No materials found. Start by adding a new material.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.teacher-layout>
