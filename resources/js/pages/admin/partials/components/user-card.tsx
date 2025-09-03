import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import { TUser } from '@/types/users';

const UserCard: React.FC<{ user: TUser }> = ({ user }) => {
    const getInitials = useInitials();
    return (
        <div className="flex min-w-[100px] items-center space-x-2 py-2">
            <Avatar className="h-8 w-8 overflow-hidden rounded-full border border-primary">
                <AvatarImage src={user.avatar} alt={user.name} />
                <AvatarFallback className="rounded-lg bg-primary-light text-primary">
                    {getInitials(user.name)}
                </AvatarFallback>
            </Avatar>
            <div className="grid flex-1 text-left text-sm leading-tight">
                <span className="truncate font-medium">{user.name}</span>
                <span className="truncate text-xs text-muted-foreground">
                    {user.email}
                </span>
            </div>
        </div>
    );
};

export default UserCard;
