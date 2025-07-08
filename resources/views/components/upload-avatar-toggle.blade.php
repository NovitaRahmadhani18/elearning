@props(['type' => 'avatar', 'checked' => false, 'avatarUrl' => null])

@if ($type === 'avatar')
    <div class="flex flex-col items-center">
        <div class="relative mb-2 h-24 w-24" x-data="{ preview: '' }">
            <img :src="preview || avatarUrl || 'https://avatar.iran.liara.run/public/50'" class="h-24 w-24 rounded-full bg-gray-200 object-cover" id="avatarPreview" />
            <label
                for="avatar"
                class="absolute bottom-0 right-0 cursor-pointer rounded-full bg-black p-2 transition hover:bg-primary"
            >
                <x-gmdi-camera-alt-r class="h-6 w-6 text-white" />
                <input
                    id="avatar"
                    name="avatar"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="
                        const file = $event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = e => preview = e.target.result;
                            reader.readAsDataURL(file);
                        }
                    "
                />
            </label>
        </div>
    </div>
@endif

@if ($type === 'toggle')
    <div class="flex items-center space-x-3">
        <span class="text-gray-700">Status</span>
        <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" name="status" value="1" class="peer sr-only" @checked($checked) />
            <div
                class="peer h-6 w-11 rounded-full bg-gray-200 transition peer-checked:bg-primary peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary"
            ></div>
            <div
                class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"
            ></div>
        </label>
        <span class="ml-2 text-gray-700">Active</span>
    </div>
@endif
 