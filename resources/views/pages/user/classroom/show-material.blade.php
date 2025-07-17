<x-layouts.user-layout>
    <x-slot name="header"></x-slot>

    <div class="mx-auto w-full max-w-5xl px-4 py-6">
        <!-- Back Navigation -->
        <div class="mb-6">
            <a href="{{ route('user.classroom.show', $classroom->id) }}"
                class="inline-flex items-center text-sm text-gray-600 hover:text-secondary transition-colors">
                <x-gmdi-arrow-back class="mr-1 h-4 w-4" />
                Back to Classroom
            </a>
        </div>

        <!-- Material Card -->
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <!-- Material Header -->
            <div class="relative h-32 bg-gradient-to-br from-blue-50 to-blue-100 overflow-hidden">
                <div class="absolute inset-0 bg-material-pattern opacity-10"></div>
                <div class="relative flex h-full items-center justify-center">
                    <div class="text-center">
                        <x-gmdi-menu-book class="mx-auto h-12 w-12 text-blue-600 mb-2" />
                        <div class="text-sm font-medium text-blue-700">Learning Material</div>
                    </div>
                </div>

                <!-- Points Badge -->
                <div class="absolute top-4 right-4">
                    <div
                        class="flex items-center rounded-full bg-secondary/90 px-3 py-1 text-sm font-medium text-white shadow-sm">
                        <x-gmdi-stars class="mr-1 h-4 w-4" />
                        <span>{{ $material->points ?? 10 }} points</span>
                    </div>
                </div>
            </div>

            <!-- Material Content -->
            <div class="p-6">
                <!-- Material Title -->
                <div class="mb-6 border-b border-gray-200 pb-4">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $material->title }}</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        <x-gmdi-schedule class="mr-1 h-4 w-4 inline" />
                        Last updated {{ $material->updated_at->diffForHumans() }}
                    </p>
                </div>

                <!-- Material Content -->
                <div class="prose prose-lg max-w-none">
                    {!! $material->trixRender('content') !!}
                </div>
            </div>
        </div>

        <!-- Completion Status -->
        <div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-4">
            <div class="flex items-center">
                <x-gmdi-check-circle class="h-5 w-5 text-green-600 mr-2" />
                <div>
                    <h3 class="text-sm font-medium text-green-900">Material Completed!</h3>
                    <p class="text-sm text-green-800">
                        You've earned {{ $material->points ?? 10 }} points for reading this material.
                        Great job on your learning journey!
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation Actions -->
        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('user.classroom.show', $classroom->id) }}"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                <x-gmdi-arrow-back class="mr-2 h-4 w-4" />
                Back to Course
            </a>

            <!-- User XP Progress -->
            <div class="text-right">
                <x-user-xp-progress />
            </div>
        </div>
    </div>
</x-layouts.user-layout>
