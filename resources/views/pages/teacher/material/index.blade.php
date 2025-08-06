<x-layouts.teacher-layout>
    <x-slot name="header">Material Management</x-slot>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
        <x-stat-card
            title="Total Materials"
            value="{{ $materials->count() }}"
            :icon="svg('fas-book', 'h-6 w-6 text-secondary-dark')"
        />
        <x-stat-card
            title="Student engagement"
            value="{{ $studentEngagement }}"
            :icon="svg('fas-chart-bar', 'h-6 w-6 text-secondary-dark')"
        />
    </div>

    <!-- Course Materials -->
    <div class="mb-8 rounded-lg border border-primary/20 bg-white p-6">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Course Materials</h2>
            <a href="{{ route('teacher.material.create') }}">
                <x-primary-button class="flex items-center justify-center gap-2">
                    <x-gmdi-add class="h-5 w-5 text-white" />
                    <span>Add Material</span>
                </x-primary-button>
            </a>
        </div>

        <!-- Search Box -->
        <div class="relative mb-6 max-w-md">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <x-gmdi-search class="h-5 w-5 text-gray-400" />
            </div>
            <form x-init x-target.replace="materials-grid">
                <input
                    type="text"
                    type="search"
                    name="search"
                    id="search"
                    autocomplete="off"
                    @input.debounce="$el.form.requestSubmit()"
                    @search="$el.form.requestSubmit()"
                    class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-10 pr-3 leading-5 placeholder-gray-500 focus:border-primary focus:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary sm:text-sm"
                    placeholder="Search materials..."
                />
            </form>
        </div>

        <!-- Materials Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3" id="materials-grid">
            @forelse ($materials as $material)
                <x-material-card :material="$material" />
            @empty
                <div class="col-span-1 py-12 text-center md:col-span-2 lg:col-span-3">
                    <x-gmdi-description class="mx-auto mb-4 h-16 w-16 text-gray-400" />
                    <h3 class="mb-2 text-lg font-medium text-gray-800">No materials found</h3>
                    <p class="mb-4 text-gray-500">Start by adding your first material to get started.</p>
                    <a href="{{ route('teacher.material.create') }}">
                        <x-primary-button class="flex items-center justify-center gap-2">
                            <x-gmdi-add class="h-5 w-5 text-white" />
                            <span>Create Material</span>
                        </x-primary-button>
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.teacher-layout>
