<x-layouts.admin-layout>
    <x-slot name="header">Classroom &gt; Show Classroom</x-slot>
    <div x-data="{
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
    }">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-neutral-700">{{ $classroom->students->count() }} Students</span>
                @if ($classroom->category)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary text-neutral-900">
                        {{ $classroom->category }}
                    </span>
                @endif
            </div>
            <button @click="$dispatch('open-modal', 'add-student')"
                class="inline-flex items-center rounded-md bg-primary text-neutral-900 px-4 py-2 text-sm font-medium hover:bg-primary-dark transition-colors">
                Add Student
            </button>
        </div>

        <div class="mb-6 border-b border-primary-dark">
            <nav class="flex space-x-8">
                <button x-on:click="setActiveTab('students')"
                    :class="isActiveTab('students') ? 'border-primary-dark text-primary-dark' :
                        'border-transparent text-gray-600 hover:text-gray-800'"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    Students
                </button>
                <button x-on:click="setActiveTab('contents')"
                    :class="isActiveTab('contents') ? 'border-primary-dark text-primary-dark' :
                        'border-transparent text-gray-600 hover:text-gray-800'"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    Contents
                </button>
            </nav>
        </div>

        <div x-transition>
            <div x-show="isActiveTab('students')">
                <x-reusable-table :tableData="$studentsTableData" title="Students" />
            </div>
            <div x-show="isActiveTab('contents')">
                @forelse ($contents as $content)
                    <div class="mb-4 rounded-lg border border-neutral-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            @if ($content->contentable instanceof \App\Models\Quiz)
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                                    <x-icon name="gmdi-library-books" class="h-4 w-4 text-primary" />
                                </div>
                            @elseif ($content->contentable instanceof \App\Models\Material)
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary/10">
                                    <x-icon name="gmdi-menu-book" class="h-4 w-4 text-secondary-dark" />
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-neutral-900 truncate">
                                    {{ $content->contentable->title }}</h3>
                                <p class="text-sm text-neutral-600 mt-1">{{ $content->contentable->description }}</p>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm text-neutral-500">
                                {{ $content->completedByUser->count() }} of {{ $classroom->students->count() }}
                                completed
                            </span>
                            <div class="flex items-center">
                                <div class="w-24 bg-neutral-200 rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full"
                                        style="width: {{ $classroom->students->count() > 0 ? ($content->completedByUser->count() / $classroom->students->count()) * 100 : 0 }}%">
                                    </div>
                                </div>
                                <span
                                    class="ml-2 text-xs text-neutral-500">{{ $classroom->students->count() > 0 ? round(($content->completedByUser->count() / $classroom->students->count()) * 100) : 0 }}%</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-neutral-500">
                        <x-icon name="gmdi-folder-open" class="h-12 w-12 mx-auto mb-3 text-neutral-400" />
                        <p>No contents available.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <x-modal name="add-student">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-semibold text-neutral-900">Share Classroom Link</h3>

                <div class="mb-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">
                            Invitation Link
                        </label>
                        <div class="flex rounded-md shadow-sm">
                            <input type="text" readonly
                                value="{{ route('user.classroom.join.form', $classroom->invite_code) }}"
                                class="flex-1 rounded-l-md border-neutral-300 bg-neutral-50 px-3 py-2 text-sm text-neutral-700 focus:border-primary focus:ring-primary" />
                            <button x-on:click="copy" x-text="copied ? 'Copied!' : 'Copy'"
                                class="inline-flex items-center rounded-r-md bg-primary px-4 py-2 text-sm font-medium text-neutral-900 hover:bg-primary-dark border border-l-0 border-primary transition-colors">
                                Copy
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">
                            Secret Code
                        </label>
                        <input type="text" readonly value="{{ $classroom->secret_code }}"
                            class="w-full rounded-md border-neutral-300 bg-neutral-50 px-3 py-2 text-sm text-neutral-700 focus:border-primary focus:ring-primary" />
                        <p class="mt-1 text-xs text-neutral-500">Students can use this code to join the classroom</p>
                    </div>
                </div>
            </div>
        </x-modal>
    </div>
</x-layouts.admin-layout>
