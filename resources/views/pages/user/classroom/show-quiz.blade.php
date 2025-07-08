<x-layouts.user-layout>
    <x-slot name="header"></x-slot>
    <div>
        <div class="mx-auto w-full max-w-5xl border border-primary/20 bg-white p-4">
            <div class="mb-4 flex items-center justify-between">
                <a href="{{ route('user.classroom.show', $classroom->id) }}" class="text-primary hover:underline">
                    Back to Classroom
                </a>
            </div>
            <h1 class="mb-2 text-2xl font-bold">{{ $quiz->title }}</h1>
            <p class="mb-1 text-gray-600">
                Total questions:
                <span class="font-semibold">
                    {{ $quiz->questions->count() }}
                </span>
            </p>
            <p class="mb-1 text-gray-600">
                Quiz opened at:
                <span class="font-semibold">
                    {{ $quiz->formatted_start_time }}
                </span>
            </p>

            <p class="mb-1 text-gray-600">
                Due date:
                <span class="font-semibold">
                    {{ $quiz->formatted_due_time }}
                </span>
            </p>

            <p class="mb-4 text-gray-600">
                Duration:
                <span class="font-semibold">
                    {{ $quiz->time_limit_in_minutes }}
                </span>
            </p>

            <a
                href="{{ route('user.classroom.quiz.start', ['classroom' => $classroom->id, 'quiz' => $quiz->id]) }}"
                class="inline-block rounded bg-primary px-4 py-2 text-white hover:bg-primary/90"
            >
                Start Quiz
            </a>
        </div>
    </div>
</x-layouts.user-layout>
