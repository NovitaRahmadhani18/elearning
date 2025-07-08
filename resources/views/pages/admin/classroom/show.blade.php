<x-layouts.admin-layout>
    <x-slot name="header">Classroom &gt; Show Classroom</x-slot>
    <div
        x-data="{
            activeTab: 'students',
            setActiveTab(tab) {
                this.activeTab = tab
            },
            isActiveTab(tab) {
                return this.activeTab === tab
            },
            link: '{{ route('user.classroom.join.form', $classroom->invite_code) }}',
            copied: false,
            copy() {
                $clipboard(this.link)

                this.copied = true
            },
        }"
    >
        <div class="mb-6 flex items-center justify-between">
            <span>{{ $classroom->students->count() }} Students</span>
            <div>
                <button
                    @click="$dispatch('open-modal', 'add-student')"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Add Student
                </button>
            </div>
        </div>

        <div class="mb-4 flex gap-2">
            <button
                x-on:click="setActiveTab('students')"
                :class="{ 'border-b border-b-1 border-b-primary': isActiveTab('students') }"
                class="px-4 py-2 text-sm font-medium"
            >
                Students
            </button>
            <button
                x-on:click="setActiveTab('contents')"
                :class="{ 'border-b border-b-1 border-b-primary': isActiveTab('contents') }"
                class="px-4 py-2 text-sm font-medium"
            >
                Contents
            </button>
        </div>

        <div x-transition>
            <div x-show="isActiveTab('students')">
                <x-reusable-table :tableData="$studentsTableData" title="Students" />
            </div>
            <div x-show="isActiveTab('contents')">
                @forelse ($contents as $content)
                    <div class="mb-4 rounded border border-primary/20 bg-white p-4">
                        <div class="flex items-center gap-2">
                            @if ($content->contentable instanceof \App\Models\Quiz)
                                <x-icon name="gmdi-library-books" class="h-4 w-4 text-primary" />
                            @elseif ($content->contentable instanceof \App\Models\Material)
                                <x-icon name="gmdi-menu-book" class="h-4 w-4 text-primary" />
                            @endif
                            <h3 class="text-lg font-semibold text-gray-900">{{ $content->contentable->title }}</h3>
                        </div>
                        <p class="text-sm text-gray-600">{{ $content->contentable->description }}</p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">
                                {{ $content->completedByUser->count() }} of {{ $classroom->students->count() }}
                                Students Completed
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500">No contents available.</div>
                @endforelse
            </div>
        </div>

        <x-modal name="add-student">
            <div class="p-4">
                <h3 class="mb-4 text-lg font-semibold">Share this link to student</h3>

                <div class="mb-4 space-y-2">
                    <p class="text-sm text-gray-600">
                        Share this link with students to allow them to join the classroom.
                    </p>
                    <input
                        type="text"
                        readonly
                        value="{{ route('user.classroom.join.form', $classroom->invite_code) }}"
                        class="w-full rounded-md border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-700 focus:border-primary focus:ring-primary"
                    />
                    <button
                        x-on:click="copy"
                        x-text="copied ? `Copied!` : `Copy link`"
                        class="rounded-md bg-primary px-4 py-2 text-white hover:bg-primary-dark"
                    >
                        Copy link
                    </button>
                </div>

                <div class="space-y-2">
                    <p class="text-sm text-gray-600">Enter secret code to join the classroom:</p>
                    <input
                        type="text"
                        readonly
                        value="{{ $classroom->secret_code }}"
                        class="w-full rounded-md border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-700 focus:border-primary focus:ring-primary"
                    />
                </div>
            </div>
        </x-modal>
    </div>
</x-layouts.admin-layout>
