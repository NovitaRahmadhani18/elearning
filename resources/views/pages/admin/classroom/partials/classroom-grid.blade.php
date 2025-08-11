<!-- Classroom Cards Grid -->
<div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
    @forelse ($classrooms as $classroom)
        <x-classroom-card :classroom="$classroom" />
    @empty
        <div class="col-span-full py-12 text-center">
            @if (request('search'))
                <!-- No Search Results -->
                <x-gmdi-search-off class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No classrooms found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    No classrooms match your search "{{ request('search') }}".
                </p>
                <div class="mt-6">
                    <button @click="$refs.searchInput.value = ''; $refs.searchForm.requestSubmit();"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                        <x-gmdi-clear class="-ml-1 mr-2 h-5 w-5" />
                        Clear Search
                    </button>
                    <a href="{{ route('admin.classroom.create') }}"
                        class="ml-3 inline-flex items-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-dark">
                        <x-gmdi-add class="-ml-1 mr-2 h-5 w-5" />
                        Create New Class
                    </a>
                </div>
            @else
                <!-- No Classes at All -->
                <x-gmdi-school class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No classes</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new class.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.classroom.create') }}"
                        class="inline-flex items-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-dark">
                        <x-gmdi-add class="-ml-1 mr-2 h-5 w-5" />
                        New Class
                    </a>
                </div>
            @endif
        </div>
    @endforelse
</div>

@if ($classrooms->count() > 0)
    <!-- Results Summary -->
    <div class="mt-6 flex items-center justify-between text-sm text-gray-600">
        <div>
            @if (request('search'))
                Showing {{ $classrooms->count() }} {{ Str::plural('result', $classrooms->count()) }} for
                "<strong>{{ request('search') }}</strong>"
            @else
                Total: {{ $classrooms->count() }} {{ Str::plural('classroom', $classrooms->count()) }}
            @endif
        </div>
        <div class="flex items-center gap-4">
            <span>{{ $classrooms->sum('students_count') }} total students</span>
            <span>{{ $classrooms->sum('contents_count') }} total contents</span>
        </div>
    </div>
@endif
