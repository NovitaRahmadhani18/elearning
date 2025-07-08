<div
    class="mx-auto rounded-lg border border-primary/20 bg-white p-10"
    x-data="{
        showNotification: false,
        notificationMessage: '',
        notificationType: 'success',
    }"
    @student-added.window="
        showNotification = true;
        notificationMessage = $event.detail[0].message;
        notificationType = 'success';
        setTimeout(() => showNotification = false, 3000);
    "
    @student-removed.window="
        showNotification = true;
        notificationMessage = $event.detail[0].message;
        notificationType = 'info';
        setTimeout(() => showNotification = false, 3000);
    "
    @student-error.window="
        showNotification = true;
        notificationMessage = $event.detail[0].message;
        notificationType = 'error';
        setTimeout(() => showNotification = false, 5000);
    "
>
    <!-- Notification -->
    <div
        x-show="showNotification"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="translate-y-2 transform opacity-0"
        x-transition:enter-end="translate-y-0 transform opacity-100"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-start="translate-y-0 transform opacity-100"
        x-transition:leave-end="translate-y-2 transform opacity-0"
        class="fixed right-4 top-4 z-50 w-full max-w-md rounded-md p-4 text-sm shadow-lg"
        :class="{
            'bg-green-50 text-green-700 border border-green-200': notificationType === 'success',
            'bg-blue-50 text-blue-700 border border-blue-200': notificationType === 'info',
            'bg-red-50 text-red-700 border border-red-200': notificationType === 'error'
        }"
        style="display: none"
    >
        <span x-text="notificationMessage"></span>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Manage Students</h2>
        <div class="text-sm text-gray-600">
            <span class="font-medium">{{ $selectedStudentsCount }}</span>
            of {{ $totalStudents }} students selected
        </div>
    </div>

    <!-- Search Input -->
    <div class="mb-6">
        <x-input-label for="search_student" value="Search Students" />
        <x-text-input
            id="search_student"
            type="text"
            placeholder="Type student name or email to search..."
            wire:model.live.debounce.300ms="searchQuery"
            class="w-full"
        />
    </div>

    <!-- Students Grid -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($filteredStudents as $student)
            <div class="flex items-center rounded-lg border border-gray-200 p-3 transition hover:bg-gray-50">
                <input
                    type="checkbox"
                    id="student-{{ $student['id'] }}"
                    wire:click="toggleStudent({{ $student['id'] }})"
                    @checked($this->isStudentSelected($student['id']))
                    class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary"
                />
                <div class="ml-3 flex-1 truncate">
                    <label for="student-{{ $student['id'] }}" class="cursor-pointer text-ellipsis">
                        <p class="text-ellipsis text-sm font-medium text-gray-900">{{ $student['name'] }}</p>
                        <p class="text-ellipsis text-xs text-gray-500">{{ $student['email'] }}</p>
                    </label>
                </div>
            </div>
        @empty
            <div class="col-span-full py-8 text-center text-gray-500">
                @if (! empty($searchQuery))
                    <p>
                        No students found matching "
                        <strong>{{ $searchQuery }}</strong>
                        "
                    </p>
                @else
                    <p>No students available</p>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Summary -->
    @if ($selectedStudentsCount > 0)
        <div class="mt-6 max-w-md rounded-lg bg-primary/10 p-4">
            <h3 class="font-medium text-gray-900">Selected Students ({{ $selectedStudentsCount }})</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach ($filteredStudents as $student)
                    @if ($this->isStudentSelected($student['id']))
                        <span
                            class="inline-flex items-center rounded-full bg-primary px-3 py-1 text-xs font-medium text-white"
                        >
                            {{ $student['name'] }}
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
