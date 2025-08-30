import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { formatDistanceToNow } from 'date-fns';
import { Award, Bell, BookOpen, Clock, Trophy, type LucideIcon } from 'lucide-react';
import { Popover, PopoverContent, PopoverTrigger } from './ui/popover';
import { ScrollArea } from './ui/scroll-area';
import { Separator } from './ui/separator';

// ====================================================================
// DATA TYPES & MOCKUP
// ====================================================================

type TNotification = {
    id: number;
    title: string;
    date: string; // ISO 8601 String
    type: string;
    icon: string; // Icon name as a string
    link: string;
    message: string;
    read: boolean;
};

// Mockup Data for Notifications
const mockNotifications: TNotification[] = [
    {
        id: 1,
        title: 'Quiz Graded',
        date: new Date().toISOString(), // Now
        type: 'quiz_graded',
        icon: 'award',
        link: '/student/quizzes/4/result',
        message: 'Your "Linear Equations" quiz has been graded. Your score: 85.',
        read: false, // Unread
    },
    {
        id: 2,
        title: 'New Material Added',
        date: new Date(Date.now() - 1000 * 60 * 60 * 2).toISOString(), // 2 hours ago
        type: 'new_material',
        icon: 'book',
        link: '/student/classrooms/1/materials/103',
        message: 'Your teacher added a new material: "Introduction to Geometry".',
        read: false, // Unread
    },
    {
        id: 3,
        title: 'Achievement Unlocked!',
        date: new Date(Date.now() - 1000 * 60 * 60 * 24).toISOString(), // Yesterday
        type: 'achievement',
        icon: 'trophy',
        link: '/student/achievements',
        message: 'Congratulations! You unlocked the "Fast Learner" badge.',
        read: true,
    },
    {
        id: 4,
        title: 'Quiz Reminder',
        date: new Date(Date.now() - 1000 * 60 * 60 * 48).toISOString(), // 2 days ago
        type: 'reminder',
        icon: 'clock',
        link: '/student/classrooms/1/quizzes/104',
        message: 'The "Pythagorean Theorem" quiz is due in 24 hours.',
        read: true,
    },
];

// Mapping from icon name string to Lucide icon components
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
                href={notification.link}
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
    // In a real application, you would fetch these notifications from global state or usePage
    const notifications = mockNotifications;
    const unreadCount = notifications.filter((n) => !n.read).length;

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
                    {notifications.length > 0 ? (
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
