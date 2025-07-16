<x-layouts.admin-layout>
    <x-slot name="header">Class</x-slot>
    <!-- Header with Add Class Button -->
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">All Classes</h2>
        <a href="{{ route('admin.classroom.create') }}">
            <x-primary-button class="flex items-center justify-center">
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        fill-rule="evenodd"
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                        clip-rule="evenodd"
                    />
                </svg>
                Add Class
            </x-primary-button>
        </a>
    </div>

    <!-- Class Cards Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($classrooms as $classroom)
            <x-classroom-card :classroom="$classroom" />
        @empty
            <div class="col-span-full py-12 text-center">
                <x-gmdi-school class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No classes</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new class.</p>
                <div class="mt-6">
                    <a
                        href="{{ route('admin.classroom.create') }}"
                        class="inline-flex items-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-dark"
                    >
                        <x-gmdi-add class="-ml-1 mr-2 h-5 w-5" />
                        New Class
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</x-layouts.admin-layout>
