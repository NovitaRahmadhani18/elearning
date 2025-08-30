import { cn } from '@/lib/utils';
import { SharedData, TNotification } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { formatDistanceToNow } from 'date-fns';
import { Award, Bell, BookOpen, Clock, Trophy, type LucideIcon } from 'lucide-react';
import { Popover, PopoverContent, PopoverTrigger } from './ui/popover';
import { ScrollArea } from './ui/scroll-area';
import { Separator } from './ui/separator';

const iconMap: Record<string, LucideIcon> = {
    award: Award,
    book: BookOpen,
    trophy: Trophy,
    clock: Clock,
    default: Bell,
};

// ====================================================================
// SUB-COMPONENT FOR A SINGLE NOTIFICATION ITEM
// ====================================================================
const NotificationItem = ({ notification }: { notification: TNotification }) => {
    const Icon = iconMap[notification.icon] || iconMap.default;

    return (
        <li>
            <Link
                href={route('notifications.read', { id: notification.id })}
                className="-m-3 block rounded-lg p-3 transition-colors hover:bg-gray-50"
            >
                <div className="flex items-start gap-3">
                    {/* Unread Indicator */}
                    {!notification.read && (
                        <div className="mt-1.5 h-2 w-2 flex-shrink-0 rounded-full bg-primary" />
                    )}
                    {/* Icon */}
                    <div
                        className={cn('flex-shrink-0', notification.read && 'ml-3')}
                    >
                        <Icon className="h-5 w-5 text-gray-500" />
                    </div>
                    {/* Text Content */}
                    <div className="flex-1">
                        <p className="text-sm font-semibold text-gray-800">
                            {notification.title}
                        </p>
                        <p className="text-sm text-gray-600">
                            {notification.message}
                        </p>
                        <p className="mt-1 text-xs text-gray-400">
                            {formatDistanceToNow(new Date(notification.date), {
                                addSuffix: true,
                            })}
                        </p>
                    </div>
                </div>
            </Link>
        </li>
    );
};

// ====================================================================
// MAIN POPOVER COMPONENT
// ====================================================================

const NotificationPopover = () => {
    const { notifications, unreadNotificationsCount } = usePage<SharedData>().props;

    const unreadCount = unreadNotificationsCount;

    return (
        <Popover>
            <PopoverTrigger className="relative">
                <Bell className="h-5 w-5 text-gray-500 transition-colors hover:text-gray-700" />
                {/* Badge for unread notification count */}
                {unreadCount > 0 && (
                    <div className="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs text-white">
                        {unreadCount}
                    </div>
                )}
            </PopoverTrigger>
            <PopoverContent align="end" className="w-80 md:w-96">
                <div className="">
                    <h3 className="text-lg font-semibold">Notifications</h3>
                    <Separator className="" />
                    {notifications && notifications.length > 0 ? (
                        <ScrollArea className="h-auto max-h-96">
                            <ul className="mt-2 space-y-1">
                                {notifications.map((notification) => (
                                    <NotificationItem
                                        key={notification.id}
                                        notification={notification}
                                    />
                                ))}
                            </ul>
                        </ScrollArea>
                    ) : (
                        <div className="py-8 text-center text-sm text-gray-500">
                            You have no new notifications.
                        </div>
                    )}
                </div>
                <Separator className="" />
            </PopoverContent>
        </Popover>
    );
};

export default NotificationPopover;
