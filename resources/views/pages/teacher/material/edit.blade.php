<x-layouts.teacher-layout>
    <x-slot name="header">Edit Material</x-slot>
    <div class="flex justify-center">
        <form class="w-full max-w-5xl rounded-lg border border-primary/20 bg-white p-10" method="POST"
            enctype="multipart/form-data" action="{{ route('teacher.material.update', $material->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-6">
                <x-input-label for="title" value="Material Title" class="text-xl" />
                <x-text-input id="title" name="title" class="mt-1" required
                    placeholder="Please enter the material title" :value="old('title', $material->title)" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="points" value="Points Reward" class="text-lg" />
                <x-text-input id="points" name="points" type="number" min="0" max="100" class="mt-1"
                    placeholder="Points students will earn (default: 10)" :value="old('points', $material->points ?? 10)" />
                <p class="mt-1 text-sm text-gray-600">Students will earn these points when they first view this material
                </p>
                <x-input-error :messages="$errors->get('points')" class="mt-2" />
            </div>

            <div class="prose mb-6 min-w-full">
                @trix($material, 'content')
            </div>

            <div class="flex justify-end">
                <div class="flex flex-row justify-end gap-2">
                    <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                    <x-primary-button type="submit" class="w-fit">Update Material</x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.teacher-layout>
