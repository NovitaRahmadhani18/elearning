<div class="space-y-3 rounded-md bg-primary/10 p-4">
    <div class="flex items-start gap-2">
        <textarea
            id="description"
            name="description"
            required
            rows="1"
            placeholder="Enter class description"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
        ></textarea>
        <button>
            <x-gmdi-delete class="h-6 w-6 text-red-500" />
        </button>
    </div>

    <div class="flex items-center gap-2">
        <input type="radio" disabled />
        <x-text-input id="" name="" required placeholder="Please enter the material title" />
    </div>

    <div>
        <button class="ml-6 text-primary hover:underline">Add Option</button>
    </div>
</div>
