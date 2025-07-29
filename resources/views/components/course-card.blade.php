@props([
    'classroom' => null,
])

<div class="mb-4 rounded-lg bg-white p-4">
    <div class="flex items-start">
        <div class="mr-4 h-16 w-16 rounded-md bg-gray-200">
            @if ($classroom->thumbnail_path)
                <img
                    src="{{ Storage::url($classroom->thumbnail_path) }}"
                    alt="{{ $classroom->title }}"
                    class="h-full w-full rounded-md object-cover"
                />
            @endif
        </div>
        <div class="flex-1">
            <a class="font-semibold text-gray-800" href="{{ route('user.classroom.show', $classroom->id) }}">
                {{ $classroom->title }} {{ $classroom->category ? ' - ' . $classroom->category : '' }}
            </a>
            <p class="text-sm text-gray-600">{{ $classroom->contents()->count() }} lesson</p>
            <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                <div class="h-full rounded-full bg-secondary" style="width: {{ $classroom->pivot->progress }}%"></div>
            </div>
        </div>
    </div>
</div>
