<x-layouts.admin-layout>
    <x-slot name="header">User Management</x-slot>

    <!-- Search and Add User -->
    <div class="mb-6 flex items-center justify-between">
        <div class="relative w-full max-w-md">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <x-gmdi-search class="h-5 w-5 text-gray-400" />
            </div>
            <form x-init x-target.replace="{{ $tableData->id }}">
                <input
                    type="search"
                    name="search"
                    id="search"
                    autocomplete="off"
                    @input.debounce="$el.form.requestSubmit()"
                    @search="$el.form.requestSubmit()"
                    class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-10 pr-3 leading-5 placeholder-gray-500 focus:border-primary focus:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary sm:text-sm"
                    placeholder="Search Users..."
                />
            </form>
        </div>
        <a href="{{ route('admin.users.create') }}">
            <button
                class="flex items-center space-x-2 rounded-md bg-primary px-4 py-2 text-white hover:bg-primary-dark"
            >
                <x-gmdi-add class="h-5 w-5 text-white" />
                <span>
                    <span class="hidden sm:inline">Add User</span>
                </span>
            </button>
        </a>
    </div>

    <!-- Role Stats Cards -->
    <div
        class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3"
        id="role-stats-cards"
        x-init
        x-on:user:deleted.window="$ajax(window.location.href, { focus: false })"
    >
        <x-card.role-stats-card
            :icon="svg('fas-user-shield', 'h-6 w-6 text-secondary-dark ml-1')"
            title="Administrator"
            value="{{$countUserByRole['admin']['count' ?? 0]}}"
        />

        <x-card.role-stats-card
            :icon="svg('fas-chalkboard-teacher', 'h-6 w-6 text-secondary-dark')"
            title="Teacher"
            value="{{$countUserByRole['teacher']['count'] ?? 0}}"
        />

        <x-card.role-stats-card
            :icon="svg('fas-user-graduate', 'h-6 w-6 text-secondary-dark')"
            title="Student"
            value="{{$countUserByRole['user']['count'] ?? 0}}"
        />
    </div>

    <x-reusable-table :tableData="$tableData" />
</x-layouts.admin-layout>
