<x-layouts.teacher-layout>
    <x-slot name="header">Quizzes</x-slot>
    <form class="flex gap-4" method="POST" action="{{ route('teacher.material.store') }}">
        <div class="w-full rounded-lg border border-primary/20 bg-white p-6">
            @csrf
            <div class="mb-6">
                <x-input-label for="title" value="Quiz Title" class="text-xl" />
                <x-text-input
                    id="title"
                    name="title"
                    class="mt-1"
                    required
                    placeholder="Please enter the material title"
                    :value="old('title')"
                />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="description" value="Description" />
                <textarea
                    id="description"
                    name="description"
                    required
                    placeholder="Enter class description"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                >
{{ old('description') }}</textarea
                >
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <div class="flex flex-row justify-end gap-2">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                    <x-primary-button type="submit" class="w-fit">Publish Material</x-primary-button>
                </div>
            </div>
        </div>
        <div class="min-h-screen border border-primary/20 bg-white p-6">
            <x-input-label for="" value="Quiz Title" class="text-xl" />

            <div class="mb-6">
                <x-input-label for="title" value="Quiz Title" class="text-xl" />
                <x-text-input
                    id="title"
                    name="title"
                    class="mt-1"
                    required
                    placeholder="Please enter the material title"
                    :value="old('title')"
                />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="title" value="Quiz Title" class="text-xl" />
                <x-text-input
                    id="title"
                    name="title"
                    class="mt-1"
                    required
                    placeholder="Please enter the material title"
                    :value="old('title')"
                />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="title" value="Quiz Title" class="text-xl" />
                <x-text-input
                    id="title"
                    name="title"
                    class="mt-1"
                    required
                    placeholder="Please enter the material title"
                    :value="old('title')"
                />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>
        </div>
    </form>
</x-layouts.teacher-layout>
