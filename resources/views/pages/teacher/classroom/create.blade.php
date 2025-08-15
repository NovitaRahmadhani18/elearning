<x-layouts.teacher-layout>
    <x-slot name="header">Classroom › Add Classroom</x-slot>
    <div class="flex min-h-[70vh] items-center justify-center">
        <form class="w-full max-w-3xl rounded-lg border border-primary/20 bg-white p-10" method="POST"
            enctype="multipart/form-data" action="{{ route('teacher.classroom.store') }}">
            @csrf
            <h2 class="mb-8 text-xl font-semibold text-gray-800">Add New Classroom</h2>
            <div class="mb-6">
                <x-input-label for="title" value="Classroom Title" />
                <x-text-input id="title" name="title" type="text" required placeholder="Enter classroom title"
                    :value="old('title')" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>
            <div class="mb-6">
                <x-input-label for="description" value="Classroom Description" />
                <textarea id="description" name="description" rows="4" placeholder="Enter classroom description"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="category" value="Category" />
                    <select id="category" name="category"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select Category</option>
                        @foreach (['7A', '7B', '7C', '8A', '8B', '8C', '9A', '9B', '9C'] as $category)
                            <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>
            </div>
            <div class="mb-6">
                <x-input-label for="thumbnail" value="Course Thumbnail" />
                <div x-data="{ fileName: '', preview: '' }"
                    class="w-full rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-6 flex flex-col items-center justify-center text-center cursor-pointer hover:border-primary transition">
                    <input id="thumbnail" name="thumbnail" type="file" accept="image/*" class="hidden"
                        @change="
                        fileName = $event.target.files[0]?.name;
                        if ($event.target.files[0]) {
                            const reader = new FileReader();
                            reader.onload = e => preview = e.target.result;
                            reader.readAsDataURL($event.target.files[0]);
                        }
                    " />
                    <label for="thumbnail" class="flex flex-col items-center w-full cursor-pointer">
                        <template x-if="!preview">
                            <div class="flex flex-col items-center">
                                <x-gmdi-cloud-upload-o class="h-10 w-10 text-gray-400 mb-2" />
                                <span class="text-gray-500">Drag and drop your image here or</span>
                                <span class="text-primary font-semibold">Browse Files</span>
                            </div>
                        </template>
                        <template x-if="preview">
                            <img :src="preview"
                                class="mx-auto h-32 object-contain rounded-lg border border-gray-200" />
                        </template>
                        <span x-text="fileName" class="block mt-2 text-xs text-gray-500"></span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
            </div>
            <div class="flex justify-end">
                <div class="flex flex-row justify-end gap-2">
                    <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                    <x-primary-button type="submit" class="w-fit">Publish Classroom</x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.teacher-layout>
