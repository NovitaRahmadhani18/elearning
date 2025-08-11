<x-layouts.admin-layout>
    <x-slot name="header">Classroom Management</x-slot>

    <div x-data="classroomSearch()" x-init="init()">
        <!-- Header with Search and Add Class Button -->
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-gray-800">All Classes</h2>
                <p class="text-sm text-gray-600">Manage and organize your classrooms</p>
            </div>
            <a href="{{ route('admin.classroom.create') }}">
                <x-primary-button class="flex items-center justify-center">
                    <x-gmdi-add class="mr-2 h-4 w-4" />
                    Add Class
                </x-primary-button>
            </a>
        </div>

        <!-- Simple Search Section -->
        <div class="mb-6 rounded-lg bg-white p-4 shadow-sm border border-gray-200">
            <form x-ref="searchForm" @submit.prevent="performSearch()" class="flex items-center gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Search Classrooms
                        <span class="text-xs text-gray-500 ml-2">Press Ctrl+K to focus</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-gmdi-search class="h-5 w-5 text-gray-400" />
                        </div>
                        <input x-ref="searchInput" x-model="searchTerm" @input.debounce.500ms="performSearch()"
                            @keydown.escape="clearSearch()" type="search" name="search" id="search"
                            placeholder="Search by title, description, category, or teacher..."
                            class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm" />
                        <div x-show="searchTerm.length > 0" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <button @click="clearSearch()" type="button" class="text-gray-400 hover:text-gray-600">
                                <x-gmdi-close class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Loading Indicator -->
                <div x-show="loading" class="flex items-center text-sm text-gray-500">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Searching...
                </div>
            </form>
        </div>

        <!-- Results Container -->
        <div x-ref="resultsContainer" id="classroom-results">
            @include('pages.admin.classroom.partials.classroom-grid', ['classrooms' => $classrooms])
        </div>
    </div>

    @push('scripts')
        <script>
            function classroomSearch() {
                return {
                    searchTerm: '{{ request('search') }}',
                    loading: false,

                    init() {
                        // Focus search input with Ctrl+K or Cmd+K
                        document.addEventListener('keydown', (e) => {
                            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                                e.preventDefault();
                                this.$refs.searchInput.focus();
                                this.$refs.searchInput.select();
                            }
                        });
                    },

                    async performSearch() {
                        this.loading = true;

                        try {
                            const response = await fetch(
                                `{{ route('admin.classroom.index') }}?search=${encodeURIComponent(this.searchTerm)}`, {
                                    method: 'GET',
                                    headers: {
                                        'X-Alpine-Request': 'true',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'text/html'
                                    }
                                });

                            if (response.ok) {
                                const html = await response.text();
                                this.$refs.resultsContainer.innerHTML = html;

                                // Update URL without page reload
                                const url = new URL(window.location);
                                if (this.searchTerm) {
                                    url.searchParams.set('search', this.searchTerm);
                                } else {
                                    url.searchParams.delete('search');
                                }
                                window.history.replaceState({}, '', url);
                            }
                        } catch (error) {
                            console.error('Search error:', error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    clearSearch() {
                        this.searchTerm = '';
                        this.performSearch();
                        this.$refs.searchInput.focus();
                    }
                }
            }
        </script>
    @endpush
</x-layouts.admin-layout>
