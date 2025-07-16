<x-layouts.user-layout>
    <x-slot name="header">{{ $classroom->title }}</x-slot>

    <div
        class="mx-auto w-full max-w-7xl rounded-md border border-primary/20 bg-white px-4 py-6 sm:px-6 lg:px-8"
        x-data
        @pageshow.window="if ($event.persisted) window.location.reload()"
    >
        @php
            $previousContentId = null;
            $isLocked = false;
        @endphp

        <div class="space-y-4 rounded-md bg-primary p-4">
            @forelse ($classroom->contents as $item)
                @php
                    // Konten pertama tidak pernah terkunci
                    if ($loop->first) {
                        $isLocked = false;
                    } else {
                        // Konten terkunci jika konten sebelumnya belum selesai
                        $isLocked = ! in_array($previousContentId, $completedContents);
                    }
                @endphp

                @if ($item->contentable instanceof \App\Models\Material)
                    <div class="flex gap-2 rounded-md border-primary/20 bg-white p-4">
                        <div>
                            <x-icon name="gmdi-menu-book" class="h-6 w-6 text-primary" />
                        </div>
                        <div class="flex flex-col items-start justify-center gap-2">
                            <p class="text-sm text-gray-600">Materi {{ $item->contentable->title }}</p>

                            <a
                                href="{{ $isLocked ? '#' : route('user.classroom.material.show', [$classroom->id, $item->contentable->id]) }}"
                                @class(['rounded-md bg-secondary px-6 py-1 text-sm', 'cursor-not-allowed bg-secondary/30' => $isLocked])
                            >
                                Detail
                            </a>
                        </div>
                    </div>
                @elseif ($item->contentable instanceof \App\Models\Quiz)
                    <div class="flex gap-2 rounded-md border-primary/20 bg-white p-4">
                        <div>
                            <x-icon name="gmdi-library-books" class="h-6 w-6 text-primary" />
                        </div>
                        <div class="flex flex-col items-start justify-center gap-2">
                            <p class="text-sm text-gray-600">Quiz {{ $item->contentable->title }}</p>
                            <div>
                                <p class="text-sm text-gray-600">
                                    <span class="font-semibold">Opened:</span>
                                    {{ $item->contentable->formatted_start_time }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-semibold">Due:</span>
                                    {{ $item->contentable->formatted_due_time }}
                                </p>
                            </div>

                            @if ($item->contentable->hasUserSubmitted(auth()->user()->id))
                                <a
                                    href="{{ $isLocked ? '#' : route('user.classroom.quiz.start', [$classroom->id, $item->contentable->id]) }}"
                                    @class(['rounded-md bg-secondary px-6 py-1 text-sm', 'cursor-not-allowed bg-secondary/30' => $isLocked])
                                >
                                    Show Results
                                </a>
                            @else
                                <a
                                    href="{{ $isLocked ? '#' : route('user.classroom.quiz.show', [$classroom->id, $item->contentable->id]) }}"
                                    @class(['rounded-md bg-secondary px-6 py-1 text-sm', 'cursor-not-allowed bg-secondary/30' => $isLocked])
                                >
                                    Start Quiz
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
                @php
                    // Simpan ID konten saat ini untuk pengecekan di iterasi berikutnya
                    $previousContentId = $item->id;
                @endphp
            @empty
                <div class="text-center text-gray-500">
                    <p>No materials available for this classroom.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.user-layout>
