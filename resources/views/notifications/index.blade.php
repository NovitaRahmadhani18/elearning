@php
    $role = auth()
        ->user()
        ->getRoleNames()
        ->first();
    $layoutComponent = match ($role) {
        'admin' => 'layouts.admin-layout',
        'teacher' => 'layouts.teacher-layout',
        default => 'layouts.user-layout',
    };
    $header = 'Notifications';
@endphp

<x-dynamic-component :component="$layoutComponent" :header="$header">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">All Notifications</h1>
            @if ($notifications->where('read_at', null)->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="bg-primary-600 hover:bg-primary-700 rounded-lg px-4 py-2 text-sm font-medium text-white"
                    >
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="divide-y divide-gray-200 rounded-lg bg-white shadow">
            @forelse ($notifications as $notification)
                <div class="{{ $notification->read_at ? 'opacity-75' : 'bg-blue-50' }} p-6 hover:bg-gray-50">
                    <div class="flex items-start space-x-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div
                                class="{{ $notification->read_at ? 'bg-gray-100' : 'bg-blue-100' }} flex h-10 w-10 items-center justify-center rounded-full"
                            >
                                @php
                                    $iconColor = match ($notification->data['type'] ?? 'default') {
                                        'quiz_completed' => 'text-blue-500',
                                        'achievement_unlocked' => 'text-yellow-500',
                                        'classroom_joined' => 'text-green-500',
                                        'system_alert' => 'text-red-500',
                                        default => 'text-gray-500',
                                    };

                                    $iconComponent = match ($notification->data['type'] ?? 'default') {
                                        'quiz_completed' => 'gmdi-quiz',
                                        'achievement_unlocked' => 'gmdi-stars',
                                        'classroom_joined' => 'gmdi-school',
                                        'system_alert' => 'gmdi-warning',
                                        default => 'gmdi-notifications',
                                    };
                                @endphp

                                <x-dynamic-component :component="$iconComponent" class="w-5 h-5 {{ $iconColor }}" />
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </h3>
                                    <p class="mt-1 text-gray-600">
                                        {{ $notification->data['message'] ?? 'You have a new notification' }}
                                    </p>
                                    <p class="mt-2 text-sm text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    @if (! $notification->read_at)
                                        <form
                                            action="{{ route('notifications.read', $notification->id) }}"
                                            method="POST"
                                            class="inline"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="text-primary-600 hover:text-primary-800 text-sm font-medium"
                                            >
                                                Mark as Read
                                            </button>
                                        </form>
                                    @endif

                                    @if (! $notification->read_at)
                                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                                    @endif
                                </div>
                            </div>

                            <!-- Additional Details for specific notification types -->
                            @if (isset($notification->data['quiz_title']))
                                <div class="mt-3 rounded-lg bg-gray-50 p-3">
                                    <div class="text-sm text-gray-600">
                                        <strong>Quiz:</strong>
                                        {{ $notification->data['quiz_title'] }}
                                        @if (isset($notification->data['score_percentage']))
                                            <br />
                                            <strong>Score:</strong>
                                            {{ $notification->data['score_percentage'] }}%
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if (isset($notification->data['achievement_name']))
                                <div class="mt-3 rounded-lg bg-yellow-50 p-3">
                                    <div class="text-sm text-gray-600">
                                        <strong>Achievement:</strong>
                                        {{ $notification->data['achievement_name'] }}
                                        @if (isset($notification->data['points_awarded']))
                                            <br />
                                            <strong>Points:</strong>
                                            {{ $notification->data['points_awarded'] }}
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if (isset($notification->data['classroom_title']))
                                <div class="mt-3 rounded-lg bg-green-50 p-3">
                                    <div class="text-sm text-gray-600">
                                        <strong>Classroom:</strong>
                                        {{ $notification->data['classroom_title'] }}
                                        @if (isset($notification->data['user_name']))
                                            <br />
                                            <strong>Student:</strong>
                                            {{ $notification->data['user_name'] }}
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <x-gmdi-notifications-none class="mx-auto mb-4 h-16 w-16 text-gray-300" />
                    <h3 class="mb-2 text-lg font-medium text-gray-900">No notifications</h3>
                    <p class="text-gray-500">You haven't received any notifications yet.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-dynamic-component>
