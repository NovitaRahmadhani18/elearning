<x-layouts.user-layout>
    <x-slot name="header"></x-slot>

    <div class="mx-auto w-full max-w-4xl px-4 py-6">
        <!-- Back Navigation -->
        <div class="mb-6">
            <a
                href="{{ route('user.classroom.show', $classroom->id) }}"
                class="inline-flex items-center text-sm text-gray-600 transition-colors hover:text-secondary"
            >
                <x-gmdi-arrow-back class="mr-1 h-4 w-4" />
                Back to Classroom
            </a>
        </div>

        <!-- Quiz Card -->
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <!-- Quiz Header -->
            <div class="relative h-32 overflow-hidden bg-gradient-to-br from-purple-50 to-purple-100">
                <div class="bg-quiz-pattern absolute inset-0 opacity-10"></div>
                <div class="relative flex h-full items-center justify-center">
                    <div class="text-center">
                        <x-gmdi-quiz class="mx-auto mb-2 h-12 w-12 text-purple-600" />
                        <div class="text-sm font-medium text-purple-700">Ready to Start Quiz</div>
                    </div>
                </div>
            </div>

            <!-- Quiz Content -->
            <div class="p-6">
                <!-- Quiz Title -->
                <h1 class="mb-4 text-2xl font-bold text-gray-900">{{ $quiz->title }}</h1>

                <!-- Quiz Info Grid -->
                <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Questions Count -->
                    <div class="flex items-center rounded-lg bg-gray-50 p-4">
                        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-full bg-purple-100">
                            <x-gmdi-help class="h-5 w-5 text-purple-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Questions</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $quiz->questions->count() }}</p>
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="flex items-center rounded-lg bg-gray-50 p-4">
                        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-full bg-orange-100">
                            <x-gmdi-timer class="h-5 w-5 text-orange-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Duration</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $quiz->time_limit }} minute</p>
                        </div>
                    </div>

                    <!-- Opens At -->
                    <div class="flex items-center rounded-lg bg-gray-50 p-4">
                        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
                            <x-gmdi-schedule class="h-5 w-5 text-green-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Opens At</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $quiz->formatted_start_time }}</p>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div class="flex items-center rounded-lg bg-gray-50 p-4">
                        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                            <x-gmdi-event class="h-5 w-5 text-red-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Due Date</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $quiz->formatted_due_time }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quiz Description or Instructions -->
                @if ($quiz->description)
                    <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <h3 class="mb-2 flex items-center text-sm font-medium text-blue-900">
                            <x-gmdi-info class="mr-1 h-4 w-4" />
                            Instructions
                        </h3>
                        <p class="text-sm text-blue-800">{{ $quiz->description }}</p>
                    </div>
                @endif

                <!-- Important Notes -->
                <div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                    <h3 class="mb-2 flex items-center text-sm font-medium text-yellow-900">
                        <x-gmdi-warning class="mr-1 h-4 w-4" />
                        Important Notes
                    </h3>
                    <ul class="space-y-1 text-sm text-yellow-800">
                        <li>• Make sure you have a stable internet connection</li>
                        <li>• You cannot pause the quiz once started</li>
                        <li>• Answer all questions before time runs out</li>
                        <li>• You can only submit the quiz once</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span class="flex items-center">
                            <x-gmdi-access-time class="mr-1 h-4 w-4" />
                            Ready to begin when you are
                        </span>
                    </div>

                    <a
                        href="{{ route('user.classroom.quiz.start', ['classroom' => $classroom->id, 'quiz' => $quiz->id]) }}"
                        class="inline-flex transform items-center rounded-md bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-3 text-base font-medium text-white shadow-sm transition-all duration-200 hover:scale-105 hover:from-purple-700 hover:to-purple-800 hover:shadow-md"
                    >
                        <x-gmdi-play-arrow class="mr-2 h-5 w-5" />
                        Start Quiz
                    </a>
                </div>
            </div>
        </div>

        <!-- Quiz Tips -->
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-4">
            <h3 class="mb-3 flex items-center text-sm font-medium text-gray-900">
                <x-gmdi-lightbulb class="mr-1 h-4 w-4 text-yellow-500" />
                Tips for Success
            </h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="flex items-start">
                    <x-gmdi-check-circle class="mr-2 mt-0.5 h-4 w-4 text-green-500" />
                    <span class="text-sm text-gray-600">Read each question carefully</span>
                </div>
                <div class="flex items-start">
                    <x-gmdi-check-circle class="mr-2 mt-0.5 h-4 w-4 text-green-500" />
                    <span class="text-sm text-gray-600">Manage your time wisely</span>
                </div>
                <div class="flex items-start">
                    <x-gmdi-check-circle class="mr-2 mt-0.5 h-4 w-4 text-green-500" />
                    <span class="text-sm text-gray-600">Review answers before submitting</span>
                </div>
                <div class="flex items-start">
                    <x-gmdi-check-circle class="mr-2 mt-0.5 h-4 w-4 text-green-500" />
                    <span class="text-sm text-gray-600">Stay calm and focused</span>
                </div>
            </div>
        </div>
    </div>
</x-layouts.user-layout>
