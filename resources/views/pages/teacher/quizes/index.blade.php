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
        @forelse ($quizes as $quiz)
            <div class="relative rounded-lg border border-primary/20 bg-white p-6" id="quiz-{{ $quiz->id }}" x-init>
                <x-dropdown align="right">
                    <x-slot name="trigger">
                        <button class="absolute right-0 top-0 text-gray-400 hover:text-gray-600">
                            <x-gmdi-more-vert-o class="h-7 w-7" />
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('teacher.quizes.show', $quiz->id)">View Quiz</x-dropdown-link>
                        <x-dropdown-link :href="route('teacher.quizes.edit', $quiz->id)">Edit Quiz</x-dropdown-link>
                        <form
                            method="POST"
                            action="{{ route('teacher.quizes.destroy', $quiz->id) }}"
                            x-init
                            x-target="quiz-{{ $quiz->id }}"
                        >
                            @csrf
                            @method('DELETE')
                            <x-dropdown-link href="#" x-on:click.prevent="$el.closest('form').submit()">
                                Delete Quiz
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                <a class="text-xl font-semibold text-gray-800" href="{{ route('teacher.quizes.show', $quiz->id) }}">
                    {{ $quiz->title }}
                </a>
                <h4 class="mb-4 text-sm text-gray-800">{{ $quiz->classroom->title }}</h4>

                <p class="mb-6 text-gray-600">{{ $quiz->description }}</p>

                <div class="flex items-center justify-between">
                    <span class="text-gray-700">50 points</span>
                    <span class="text-gray-700">
                        Due
                        {{ $quiz->due_time->diffForHumans() }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-1 text-center md:col-span-2 lg:col-span-3">
                <p class="text-gray-500">No quizzes available. Start by creating a new quiz.</p>
            </div>
        @endforelse
    </div>
</x-layouts.teacher-layout>
