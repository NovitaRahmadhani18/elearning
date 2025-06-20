<x-layouts.teacher-layout>
    <x-slot name="header">Quizzes</x-slot>

    <!-- Create Quiz Button -->
    <div class="mb-6">
        <a
            class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
            href="{{ route('teacher.quizes.create') }}"
        >
            Create Quiz
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Quiz Card 1 -->
        @forelse ([1, 2, 3, 4, 5] as $item)
            <div class="relative rounded-lg bg-white p-6 shadow-sm">
                <button class="absolute right-6 top-6 text-gray-400 hover:text-gray-600">
                    <x-gmdi-more-vert-o class="h-7 w-7" />
                </button>

                <h3 class="mb-4 text-xl font-semibold text-gray-800">Teknologi Informasi</h3>

                <p class="mb-6 text-gray-600">Explore the world of TIK in a fun way!</p>

                <div class="flex items-center justify-between">
                    <span class="text-gray-700">50 points</span>
                    <span class="text-gray-700">Due in 5 days</span>
                </div>
            </div>
        @empty
            <div class="col-span-1 text-center md:col-span-2 lg:col-span-3">
                <p class="text-gray-500">No quizzes available. Start by creating a new quiz.</p>
            </div>
        @endforelse
    </div>
</x-layouts.teacher-layout>
