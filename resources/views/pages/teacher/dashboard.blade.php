<x-layouts.teacher-layout>
    <x-slot name="header">Material Creation</x-slot>
    <x-slot name="username">Sarah</x-slot>
    <x-slot name="role">Teacher</x-slot>

    <x-slot name="sidebar">
        <x-sidebar-link
            href="#"
            :active="true"
            :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path d=\'M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z\' /></svg>'"
        >
            Material Creation
        </x-sidebar-link>
        <x-sidebar-link
            href="#"
            :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path d=\'M9 2a1 1 0 000 2h2a1 1 0 100-2H9z\' /><path fill-rule=\'evenodd\' d=\'M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z\' clip-rule=\'evenodd\' /></svg>'"
        >
            Quizzes
        </x-sidebar-link>
        <x-sidebar-link
            href="#"
            :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm9 4a1 1 0 10-2 0v6a1 1 0 102 0V7zm-3 2a1 1 0 10-2 0v4a1 1 0 102 0V9zm-3 3a1 1 0 10-2 0v1a1 1 0 102 0v-1z\' clip-rule=\'evenodd\' /></svg>'"
        >
            Student Tracking
        </x-sidebar-link>
    </x-slot>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
        <x-stat-card
            title="Total Materials"
            value="24"
            :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-6 w-6 text-gray-600\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253\' /></svg>'"
        />

        <x-stat-card
            title="Student Engagement"
            value="85%"
            :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-6 w-6 text-gray-600\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z\' /></svg>'"
        />
    </div>

    <!-- Course Materials -->
    <div class="mb-8 rounded-lg bg-white p-6 shadow-sm">
        <h2 class="mb-6 text-lg font-semibold text-gray-800">Course Materials</h2>

        <!-- Search Box -->
        <div class="relative mb-6">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg
                    class="h-5 w-5 text-gray-400"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <input
                type="text"
                class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-10 pr-3 leading-5 placeholder-gray-500 focus:border-primary focus:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary sm:text-sm"
                placeholder="Search materials..."
            />
        </div>

        <!-- Materials Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <x-material-card
                title="Teknologi Informasi"
                description="The basic of operating systems"
                updated="3 days ago"
            />

            <x-material-card
                title="Teknologi Informasi"
                description="The basic of operating systems"
                updated="3 days ago"
            />

            <x-material-card
                title="Teknologi Informasi"
                description="The basic of operating systems"
                updated="3 days ago"
            />
        </div>
    </div>
</x-layouts.teacher-layout>
