<x-layouts.admin-layout>
    <x-slot name="header">Class</x-slot>

    <!-- Header with Add Class Button -->
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">All Classes</h2>
        <a href="{{ route('admin.classroom.create') }}">
            <button class="flex items-center rounded-md bg-primary px-4 py-2 text-white hover:bg-primary-dark">
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        fill-rule="evenodd"
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                        clip-rule="evenodd"
                    />
                </svg>
                Add Class
            </button>
        </a>
    </div>

    <!-- Class Cards Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($classrooms as $classroom)
            <div
                class="overflow-hidden rounded-lg border border-primary/20 bg-white"
                id="classroom-{{ $classroom->id }}"
            >
                <div class="h-40 border-b border-b-primary/20 bg-gray-200">
                    @if ($classroom->image_url)
                        <img
                            src="{{ $classroom->image_url }}"
                            alt="{{ $classroom->title }}"
                            class="h-full w-full object-cover"
                        />
                    @else
                        <div class="flex h-full items-center justify-center text-gray-400">
                            <span>No Image</span>
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <a href="{{ route('admin.classroom.show', $classroom->id) }}" class="hover:underline">
                            {{ $classroom->title }}
                        </a>
                    </h3>
                    <div class="mt-2 flex items-center text-primary">
                        <x-gmdi-person class="mr-2 h-5 w-5 text-gray-500" />
                        <span>{{ $classroom->teacher->name }}</span>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <a class="text-yellow-500" href="{{ route('admin.classroom.edit', $classroom->id) }}">
                            <x-gmdi-edit class="h-5 w-5" />
                        </a>
                        <form
                            action="{{ route('admin.classroom.destroy', $classroom->id) }}"
                            method="post"
                            x-init
                            x-target="classroom-{{ $classroom->id }}"
                        >
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500" type="submit">
                                <x-gmdi-delete class="h-5 w-5" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-1 text-center md:col-span-2 lg:col-span-3">
                <p class="text-gray-500">
                    No classes available.
                    <a href="{{ route('admin.classroom.create') }}" class="text-primary hover:underline">
                        Create a new class
                    </a>
                    .
                </p>
            </div>
        @endforelse
    </div>
</x-layouts.admin-layout>
