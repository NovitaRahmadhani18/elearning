<div class="relative" x-data="{ open: @entangle('isOpen') }">
    <!-- Notification Button -->
    <button @click="$wire.toggleDropdown()"
        class="relative rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
        <span class="sr-only">View notifications</span>
        <x-gmdi-notifications-o class="h-6 w-6" />

        <!-- Unread Count Badge -->
        @if ($unreadCount > 0)
            <span
                class="absolute -top-1 -right-1 inline-flex items-center justify-center h-5 w-5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        @click.outside="open = false"
        class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        x-cloak>

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
            @if ($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                    Mark all read
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div wire:key="notification-{{ $notification->id }}"
                    class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0
                            {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50' }}"
                    wire:click="markAsRead('{{ $notification->id }}')">

                    <div class="flex items-start space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div
                                class="flex items-center justify-center w-8 h-8 rounded-full {{ $notification->read_at ? 'bg-gray-100' : 'bg-blue-100' }}">
                                <x-dynamic-component :component="$this->getNotificationIcon($notification->data['type'] ?? 'default')"
                                    class="w-4 h-4 {{ $this->getNotificationColor($notification->data['type'] ?? 'default') }}" />
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </div>
                            <div class="text-sm text-gray-600 mt-1">
                                {{ $notification->data['message'] ?? 'You have a new notification' }}
                            </div>
                            <div class="text-xs text-gray-400 mt-2">
                                {{ $this->getTimeAgo($notification->created_at) }}
                            </div>
                        </div>

                        <!-- Unread Indicator -->
                        @if (!$notification->read_at)
                            <div class="flex-shrink-0">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <x-gmdi-notifications-none class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                    <p class="text-sm">No notifications yet</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if ($notifications->count() > 0)
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <a href="{{ route('notifications.index') }}"
                    class="block text-sm text-center text-primary-600 hover:text-primary-800 font-medium">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>
