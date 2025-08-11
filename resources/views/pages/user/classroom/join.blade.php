<x-layouts.user-layout>
    <x-slot name="header"></x-slot>
    <div class="mb-8 rounded-lg bg-white p-6 shadow-sm">
        <div class="flex">
            <!-- Course Image Placeholder -->
            <div class="mr-6 h-28 w-28 flex-shrink-0 rounded-lg bg-gray-300">
                @if ($classroom->imageUrl)
                    <img
                        src="{{ $classroom->imageUrl }}"
                        alt="{{ $classroom->title }}"
                        class="h-full w-full rounded-lg object-cover"
                    />
                @endif
            </div>

            <div class="flex-grow">
                <h2 class="text-xl font-semibold text-gray-900">{{ $classroom->title }}</h2>
                <p class="mb-4 text-sm text-gray-600">Enter the classroom code to join.</p>

                <form
                    method="POST"
                    action="{{ route('user.classroom.join.submit', $classroom->invite_code) }}"
                    class="space-y-4"
                >
                    @csrf
                    <input
                        type="text"
                        name="code"
                        id="code"
                        required
                        placeholder="Classroom Code"
                        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 leading-5 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary sm:text-sm"
                    />
                    <button
                        type="submit"
                        class="w-full rounded-md bg-primary-dark px-4 py-2 text-white hover:bg-primary-dark"
                    >
                        Join Classroom
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.user-layout>
