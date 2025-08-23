import { Icon } from '@/components/ui/icon';
import { cn } from '@/lib/utils';
import { Calendar, CheckCircle2, Clock, Lock, LucideIcon } from 'lucide-react';

type TBadgeStatus = 'completed' | 'locked' | 'scheduled' | 'expired' | 'unlocked';

const colors: Record<TBadgeStatus, string> = {
    completed: 'bg-green-100 text-green-800',
    locked: 'bg-white text-gray-500',
    scheduled: 'bg-amber-100 text-amber-800',
    expired: 'bg-red-100 text-red-800',
    unlocked: 'bg-blue-100 text-blue-800',
};

const icons: Record<TBadgeStatus, LucideIcon> = {
    completed: CheckCircle2,
    locked: Lock,
    unlocked: CheckCircle2,
    scheduled: Calendar,
    expired: Clock,
};

const ContentBadge = ({
    status = 'unlocked',
    className,
}: {
    status?: TBadgeStatus;
    className?: string;
}) => {
    const badgeStatus = status as TBadgeStatus;

    if (status === 'unlocked') {
        return null;
    }

    return (
        <span
            className={cn(
                'inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-medium capitalize',
                colors[badgeStatus],
                className,
            )}
        >
            <Icon iconNode={icons[badgeStatus]} className="mr-1 h-3 w-3" />

            {badgeStatus}
        </span>
    );
};

export default ContentBadge;
