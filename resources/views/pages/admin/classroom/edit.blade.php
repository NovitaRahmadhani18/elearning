<x-layouts.admin-layout>
    <x-slot name="header">Class &gt; Edit Class</x-slot>
    <div class="">
        <div class="flex min-h-[70vh] items-center justify-center">
            <form
                class="w-full max-w-3xl rounded-lg border border-primary/20 bg-white p-10"
                method="POST"
                enctype="multipart/form-data"
                action="{{ route('admin.classroom.update', $classroom->id) }}"
            >
                @csrf
                @method('PUT')
                <h2 class="mb-8 text-xl font-semibold text-gray-800">Edit Class</h2>
                <div class="mb-6">
                    <x-input-label for="title" value="Class Title" />
                    <x-text-input
                        id="title"
                        name="title"
                        type="text"
                        required
                        placeholder="Enter class title"
                        :value="old('title', $classroom->title)"
                    />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                <div class="mb-6">
                    <x-input-label for="description" value="Class Description" />
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        required
                        placeholder="Enter class description"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                    >
{{ old('description', $classroom->description) }}</textarea
                    >
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <x-input-label for="category" value="Category" />
                        <x-text-input
                            id="category"
                            name="category"
                            type="text"
                            required
                            placeholder="Enter category"
                            :value="old('category', $classroom->category)"
                        />
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="teacher_id" value="Teacher" />
                        <select
                            id="teacher_id"
                            name="teacher_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                        >
                            <option value="">Select Teacher</option>
                            @foreach ($teachers as $teacher)
                                <option
                                    value="{{ $teacher->id }}"
                                    {{ old('teacher_id', $classroom->teacher_id) == $teacher->id ? 'selected' : '' }}
                                >
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('teacher_id')" class="mt-2" />
                    </div>
                </div>
                <div class="mb-6">
                    <x-input-label for="thumbnail" value="Course Thumbnail" />
                    <div
                        x-data="{
                            fileName: '',
                            preview: '{{ $classroom->image_url ? asset($classroom->image_url) : '' }}',
                        }"
                        class="flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-6 text-center transition hover:border-primary"
                    >
                        <input
                            id="thumbnail"
                            name="thumbnail"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="
                        fileName = $event.target.files[0]?.name;
                        if ($event.target.files[0]) {
                            const reader = new FileReader();
                            reader.onload = e => preview = e.target.result;
                            reader.readAsDataURL($event.target.files[0]);
                        }
                    "
                        />
                        <label for="thumbnail" class="flex w-full cursor-pointer flex-col items-center">
                            <template x-if="!preview">
                                <div class="flex flex-col items-center">
                                    <x-gmdi-cloud-upload-o class="mb-2 h-10 w-10 text-gray-400" />
                                    <span class="text-gray-500">Drag and drop your image here or</span>
                                    <span class="font-semibold text-primary">Browse Files</span>
                                </div>
                            </template>
                            <template x-if="preview">
                                <img
                                    :src="preview"
                                    class="mx-auto h-32 rounded-lg border border-gray-200 object-contain"
                                />
                            </template>
                            <span x-text="fileName" class="mt-2 block text-xs text-gray-500"></span>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                </div>
                <div class="mb-8">
                    <x-input-label for="max_students" value="Maximum Students" />
                    <x-text-input
                        id="max_students"
                        name="max_students"
                        type="number"
                        min="0"
                        required
                        placeholder="Enter maximum students"
                        :value="old('max_students', $classroom->max_students)"
                    />
                    <x-input-error :messages="$errors->get('max_students')" class="mt-2" />
                </div>
                <div class="flex justify-end">
                    <div class="flex flex-row justify-end gap-2">
                        <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                        <x-primary-button type="submit" class="w-fit">Update Class</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin-layout>
