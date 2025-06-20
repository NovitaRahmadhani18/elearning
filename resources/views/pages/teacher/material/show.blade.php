<x-layouts.teacher-layout>
    <x-slot name="header">Material Creation</x-slot>

    <div class="prose mx-auto w-full max-w-5xl border border-primary/20 bg-white p-4">
        {!! $material->trixRender('content') !!}
    </div>
</x-layouts.teacher-layout>
