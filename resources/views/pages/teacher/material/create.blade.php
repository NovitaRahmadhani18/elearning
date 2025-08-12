<x-layouts.teacher-layout>
    <x-slot name="header">Material Creation</x-slot>
    <div class="flex justify-center">
        <form
            class="w-full max-w-5xl rounded-lg border border-primary/20 bg-white p-10"
            method="POST"
            enctype="multipart/form-data"
            action="{{ route('teacher.material.store') }}"
        >
            @csrf
            <div class="mb-6">
                <x-input-label for="title" value="Material Title" class="text-xl" />
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
                <x-input-label for="classroom_id" value="Select Course" />
                <select name="classroom_id" id="classroom_id">
                    <option value="" disabled selected>Select a course</option>
                    @foreach ($classrooms as $classroom)
                        <option
                            value="{{ $classroom['id'] }}"
                            {{ old('classroom_id') == $classroom['id'] ? 'selected' : '' }}
                        >
                            {{ $classroom['title'] }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('classroom_id')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="points" value="Points Reward" class="text-lg" />
                <x-text-input
                    id="points"
                    name="points"
                    type="number"
                    min="0"
                    max="100"
                    class="mt-1"
                    placeholder="Points students will earn (default: 10)"
                    :value="old('points', 10)"
                />
                <p class="mt-1 text-sm text-gray-600">
                    Students will earn these points when they first view this material
                </p>
                <x-input-error :messages="$errors->get('points')" class="mt-2" />
            </div>

            <div class="prose mb-6 min-w-full">
                @trix(\App\Models\Material::class, 'content')
            </div>

            <div class="flex justify-end">
                <div class="flex flex-row justify-end gap-2">
                    <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                    <x-primary-button type="submit" class="w-fit">Publish Material</x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.teacher-layout>
