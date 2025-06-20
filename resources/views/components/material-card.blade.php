@props([
    'material',
])

<div
    class="flex flex-col overflow-hidden rounded-lg border border-primary/20 bg-white"
    x-init
    id="material-{{ $material->id }}"
>
    <div class="h-32 bg-gray-200">
        @if (! empty($material->thumbnailUrl))
            <img
                src="{{ $material->thumbnailUrl }}"
                alt="{{ $material->title }}"
                class="h-full w-full object-cover"
                loading="lazy"
            />
        @endif
    </div>
    <div class="p-4">
        <a class="font-semibold text-gray-800" href="{{ route('teacher.material.show', $material->id) }}">
            {{ $material->classroom->title }}
        </a>
        <p class="text-sm text-gray-600">{{ $material->title }}</p>

        <div class="mt-4 flex w-full items-center justify-between">
            <p class="text-xs text-gray-500">Updated {{ $material->updated_at->diffForHumans() }}</p>
            <div class="flex">
                <a href="{{ route('teacher.material.edit', $material->id) }}">
                    <x-gmdi-edit class="h-5 w-5 text-gray-600 hover:text-primary" />
                </a>
                <form
                    id="delete-form"
                    action="{{ route('teacher.material.destroy', $material->id) }}"
                    method="POST"
                    x-init
                    x-target='material-{{$material->id}}'
                >
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="ml-2">
                        <x-gmdi-delete class="h-5 w-5 text-gray-600 hover:text-red-600" />
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
