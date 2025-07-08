<x-layouts.user-layout>
    <x-slot name="header"></x-slot>

    @isset($classrooms[0])
        <div class="mb-8 rounded-lg bg-white p-6 shadow-sm">
            <div class="flex">
                <!-- Course Image Placeholder -->
                <div class="mr-6 h-28 w-28 flex-shrink-0 rounded-lg bg-gray-300"></div>

                <div class="flex-grow">
                    <a
                        class="text-xl font-semibold text-gray-900"
                        href="{{ route('user.classroom.show', $classrooms[0]) }}"
                    >
                        {{ $classrooms[0]->title }}
                    </a>
                    <p class="mb-4 text-sm text-gray-600">Perkenalan Bilangan</p>

                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-sm text-gray-600">Progres</span>
                        <span class="text-sm font-medium text-gray-900">{{ $classrooms[0]->pivot->progress }}%</span>
                    </div>

                    <div class="h-2 w-full rounded-full bg-gray-200">
                        <div
                            class="h-2 rounded-full bg-primary"
                            style="width: {{ $classrooms[0]->pivot->progress }}%"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    @endisset

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($classrooms as $classroom)
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="h-40 bg-gray-300"></div>
                <div class="p-4">
                    <h3 class="mb-2 text-lg font-medium text-gray-900">{{ $classroom->title }}</h3>

                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-sm text-gray-600">Completed</span>
                        <span class="text-sm font-medium text-gray-900">{{ $classroom->pivot->progress }}%</span>
                    </div>

                    <div class="mb-4 h-2 w-full rounded-full bg-gray-200">
                        <div
                            class="h-2 rounded-full bg-primary"
                            style="width: {{ $classroom->pivot->progress }}%"
                        ></div>
                    </div>

                    <button
                        class="w-full rounded-md border border-gray-300 py-2 text-sm text-gray-700 hover:bg-gray-50"
                    >
                        Detail
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-1 text-center md:col-span-2 lg:col-span-3">
                <p class="text-gray-500">No courses available</p>
            </div>
        @endforelse
    </div>
</x-layouts.user-layout>
