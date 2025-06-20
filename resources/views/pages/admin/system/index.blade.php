<x-layouts.admin-layout>
    <x-slot name="header">System Settings</x-slot>
    <x-slot name="username">Joshua</x-slot>
    <x-slot name="role">Administrator</x-slot>

    <!-- Settings Cards Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Gamification Settings Card -->
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="mb-6 text-lg font-semibold text-gray-800">Gamification Settings</h2>

            <!-- Point System -->
            <div class="mb-6">
                <label for="pointSystem" class="mb-2 block text-sm font-medium text-gray-700">Point System</label>
                <div class="flex items-center">
                    <input
                        type="number"
                        id="pointSystem"
                        name="pointSystem"
                        class="w-24 rounded-md border-gray-300 focus:border-primary focus:ring-primary"
                        value="10"
                    />
                    <span class="ml-3 text-sm text-gray-600">points per completed class</span>
                </div>
            </div>

            <!-- Level Progression -->
            <div class="mb-6">
                <label for="levelProgression" class="mb-2 block text-sm font-medium text-gray-700">
                    Level Progression
                </label>
                <div class="flex items-center">
                    <input
                        type="number"
                        id="levelProgression"
                        name="levelProgression"
                        class="w-24 rounded-md border-gray-300 focus:border-primary focus:ring-primary"
                        value="100"
                    />
                    <span class="ml-3 text-sm text-gray-600">points needed per level</span>
                </div>
            </div>

            <!-- Achievement Badges -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Achievement Badges</label>
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="enableBadges"
                        name="enableBadges"
                        class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                        checked
                    />
                    <label for="enableBadges" class="ml-2 text-sm text-gray-700">Enable achievement badges</label>
                </div>
            </div>
        </div>

        <!-- Notification Settings & Security Settings Card -->
        <div class="space-y-6">
            <!-- Notification Settings -->
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-semibold text-gray-800">Notification Settings</h2>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="courseCompletionNotifications"
                            name="courseCompletionNotifications"
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                            checked
                        />
                        <label for="courseCompletionNotifications" class="ml-2 text-sm text-gray-700">
                            Course completion notifications
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="newClassAnnouncements"
                            name="newClassAnnouncements"
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                            checked
                        />
                        <label for="newClassAnnouncements" class="ml-2 text-sm text-gray-700">
                            New class announcements
                        </label>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-semibold text-gray-800">Security Settings</h2>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Password Requirements</label>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="minChars"
                                name="minChars"
                                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                                checked
                            />
                            <label for="minChars" class="ml-2 text-sm text-gray-700">
                                Require minimum 8 characters
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="specialChars"
                                name="specialChars"
                                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                                checked
                            />
                            <label for="specialChars" class="ml-2 text-sm text-gray-700">
                                Require special characters
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="numbers"
                                name="numbers"
                                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                                checked
                            />
                            <label for="numbers" class="ml-2 text-sm text-gray-700">Require numbers</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin-layout>
