<x-layouts.teacher-layout>
    <x-slot name="header">Student Tracking</x-slot>
    <x-slot name="username">Sarah</x-slot>
    <x-slot name="role">Teacher</x-slot>

    <x-slot name="sidebar">
        <x-sidebar-link
            href="#"
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
            :active="true"
            :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm9 4a1 1 0 10-2 0v6a1 1 0 102 0V7zm-3 2a1 1 0 10-2 0v4a1 1 0 102 0V9zm-3 3a1 1 0 10-2 0v1a1 1 0 102 0v-1z\' clip-rule=\'evenodd\' /></svg>'"
        >
            Student Tracking
        </x-sidebar-link>
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-8">
        <!-- Total Students Card -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Total Students</h3>
            <p class="text-4xl font-bold text-gray-900">600</p>
        </div>

        <!-- Average Completion Rate Card -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Average Completion Rate</h3>
            <p class="text-4xl font-bold text-gray-900">76%</p>
        </div>
    </div>

    <!-- Student Performance Table -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-medium text-gray-900 mb-6">Student Performance</h2>
        
        <!-- Actual HTML Table -->
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="text-left text-sm font-medium text-gray-700 pb-4">Student Name</th>
                    <th class="text-left text-sm font-medium text-gray-700 pb-4">Progress</th>
                    <th class="text-left text-sm font-medium text-gray-700 pb-4">Completion</th>
                    <th class="text-left text-sm font-medium text-gray-700 pb-4">Last Activity</th>
                </tr>
            </thead>
            <tbody class="border-t border-gray-200">
                <!-- Student 1 -->
                <tr class="border-b border-gray-100">
                    <td class="py-4 text-sm font-medium text-gray-900">Alice Cooper</td>
                    <td class="py-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gray-400 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </td>
                    <td class="py-4 text-sm font-medium text-gray-900">75%</td>
                    <td class="py-4 text-sm text-gray-500">2 hours ago</td>
                </tr>
                
                <!-- Student 2 -->
                <tr class="border-b border-gray-100">
                    <td class="py-4 text-sm font-medium text-gray-900">Alice Cooper</td>
                    <td class="py-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gray-400 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </td>
                    <td class="py-4 text-sm font-medium text-gray-900">75%</td>
                    <td class="py-4 text-sm text-gray-500">2 hours ago</td>
                </tr>
                
                <!-- Student 3 -->
                <tr class="border-b border-gray-100">
                    <td class="py-4 text-sm font-medium text-gray-900">Alice Cooper</td>
                    <td class="py-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gray-400 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </td>
                    <td class="py-4 text-sm font-medium text-gray-900">75%</td>
                    <td class="py-4 text-sm text-gray-500">2 hours ago</td>
                </tr>
            </tbody>
        </table>
    </div>
</x-layouts.teacher-layout>
