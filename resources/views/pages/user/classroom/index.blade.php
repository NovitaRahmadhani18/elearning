<x-layouts.user-layout>
    <x-slot name="header">My Class</x-slot>
    <x-slot name="username">Novita</x-slot>
    <x-slot name="role">Student</x-slot>

    <x-slot name="navbar">
        <div class="flex space-x-8">
            <a href="#" class="flex items-center text-gray-600 hover:text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                    />
                </svg>
                Dashboard
            </a>
            <a href="#" class="flex items-center border-b-2 border-primary font-medium text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
                    />
                </svg>
                My Class
            </a>
            <a href="#" class="flex items-center text-gray-600 hover:text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        fill-rule="evenodd"
                        d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm9 4a1 1 0 10-2 0v6a1 1 0 102 0V7zm-3 2a1 1 0 10-2 0v4a1 1 0 102 0V9zm-3 3a1 1 0 10-2 0v1a1 1 0 102 0v-1z"
                        clip-rule="evenodd"
                    />
                </svg>
                Leaderboard
            </a>
            <a href="#" class="flex items-center text-gray-600 hover:text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        fill-rule="evenodd"
                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                    />
                </svg>
                Lencana
            </a>
        </div>
    </x-slot>

    <!-- Main Course Overview Card -->
    <div class="mb-8 rounded-lg bg-white p-6 shadow-sm">
        <div class="flex">
            <!-- Course Image Placeholder -->
            <div class="mr-6 h-28 w-28 flex-shrink-0 rounded-lg bg-gray-300"></div>

            <div class="flex-grow">
                <h2 class="text-xl font-semibold text-gray-900">Teknologi Informasi</h2>
                <p class="mb-4 text-sm text-gray-600">Perkenalan Bilangan</p>

                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Progres</span>
                    <span class="text-sm font-medium text-gray-900">65%</span>
                </div>

                <div class="h-2 w-full rounded-full bg-gray-200">
                    <div class="h-2 rounded-full bg-primary" style="width: 65%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Cards Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Course Card 1 -->
        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <div class="h-40 bg-gray-300"></div>
            <div class="p-4">
                <h3 class="mb-2 text-lg font-medium text-gray-900">Teknologi Informasi</h3>

                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Completed</span>
                    <span class="text-sm font-medium text-gray-900">25%</span>
                </div>

                <div class="mb-4 h-2 w-full rounded-full bg-gray-200">
                    <div class="h-2 rounded-full bg-primary" style="width: 25%"></div>
                </div>

                <button class="w-full rounded-md border border-gray-300 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    Detail
                </button>
            </div>
        </div>

        <!-- Course Card 2 -->
        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <div class="h-40 bg-gray-300"></div>
            <div class="p-4">
                <h3 class="mb-2 text-lg font-medium text-gray-900">Teknologi Informasi</h3>

                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Completed</span>
                    <span class="text-sm font-medium text-gray-900">25%</span>
                </div>

                <div class="mb-4 h-2 w-full rounded-full bg-gray-200">
                    <div class="h-2 rounded-full bg-primary" style="width: 25%"></div>
                </div>

                <button class="w-full rounded-md border border-gray-300 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    Detail
                </button>
            </div>
        </div>

        <!-- Course Card 3 -->
        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <div class="h-40 bg-gray-300"></div>
            <div class="p-4">
                <h3 class="mb-2 text-lg font-medium text-gray-900">Teknologi Informasi</h3>

                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Completed</span>
                    <span class="text-sm font-medium text-gray-900">25%</span>
                </div>

                <div class="mb-4 h-2 w-full rounded-full bg-gray-200">
                    <div class="h-2 rounded-full bg-primary" style="width: 25%"></div>
                </div>

                <button class="w-full rounded-md border border-gray-300 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    Detail
                </button>
            </div>
        </div>
    </div>
</x-layouts.user-layout>
