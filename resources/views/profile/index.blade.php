<x-layouts.user-layout>
    <x-slot name="header">Profile</x-slot>

    <!-- Profile Card -->
    <div class="rounded-lg bg-white p-6 shadow-sm">
        <div class="flex flex-col md:flex-row">
            <!-- Profile Image and Basic Info -->
            <div class="mb-6 flex flex-col items-center md:mb-0 md:mr-6">
                <div class="relative mb-4 h-36 w-36 rounded-full bg-gray-200">
                    <div class="absolute bottom-0 right-0 rounded-full bg-black p-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-white"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                </div>
                <h2 class="text-xl font-semibold text-gray-900">Novita</h2>
                <p class="text-sm text-gray-600">Student</p>
            </div>

            <!-- Profile Form -->
            <div class="flex-1">
                <form class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Full Name -->
                        <div>
                            <label for="fullName" class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                            <input
                                type="text"
                                id="fullName"
                                name="fullName"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                value="Novita Rahma"
                            />
                        </div>

                        <!-- Student ID -->
                        <div>
                            <label for="studentId" class="mb-1 block text-sm font-medium text-gray-700">
                                Student ID
                            </label>
                            <input
                                type="text"
                                id="studentId"
                                name="studentId"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                            />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-gray-700">
                                Email Address
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                value="Novita@example.com"
                            />
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="mb-1 block text-sm font-medium text-gray-700">Location</label>
                            <input
                                type="text"
                                id="location"
                                name="location"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                            />
                        </div>
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="mb-1 block text-sm font-medium text-gray-700">Bio</label>
                        <textarea
                            id="bio"
                            name="bio"
                            rows="4"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                        >
Passionate about Design</textarea
                        >
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded-md border border-transparent bg-black px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2"
                        >
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.user-layout>
