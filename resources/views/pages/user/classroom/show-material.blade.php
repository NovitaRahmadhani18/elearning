<x-layouts.user-layout>
    <x-slot name="header"></x-slot>

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('user.classroom.show', $classroom->id) }}" class="text-primary hover:underline">
            Back to Classroom
        </a>
    </div>
    <div class="prose mx-auto w-full max-w-5xl border border-primary/20 bg-white p-4">
        <h1 class="mb-2 text-2xl font-bold">{{ $material->title }}</h1>
    </div>
</x-layouts.user-layout>
