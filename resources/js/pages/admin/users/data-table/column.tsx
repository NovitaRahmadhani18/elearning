import { ActionButton } from '@/components/action-button';
import { cn } from '@/lib/utils';
import { TUser } from '@/types/users';
import { Link, router } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { toast } from 'sonner';
import UserCard from '../../partials/components/user-card';

export const userColumns: ColumnDef<TUser>[] = [
    {
        id: 'number',
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorKey: 'name',
        header: 'User',
        cell: ({ row }) => <UserCard user={row.original} />,
    },
    {
        accessorKey: 'role',
        header: 'Role',
        cell: ({ row }) => {
            // badge styles based on role admin, teacher, student

            const roleStyles = {
                teacher: 'bg-red-100 text-red-800',
                student: 'bg-blue-100 text-blue-800',
                admin: 'bg-green-100 text-green-800',
            };

            return (
                <span
                    className={cn(
                        'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium capitalize',
                        roleStyles[row.original.role] || 'bg-gray-100 text-gray-800', // default style
                    )}
                >
                    {row.original.role}
                </span>
            );
        },
    },
    {
        accessorKey: 'is_active',
        header: 'Status',
        cell: ({ row }) => {
            const isActive = row.getValue('is_active');
            return (
                <span
                    className={cn(
                        'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                        isActive
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800',
                    )}
                >
                    {isActive ? 'Active' : 'Inactive'}
                </span>
            );
        },
    },
    {
        accessorKey: 'created_at',
        header: 'Join Date',
        cell: ({ row }) =>
            new Date(row.getValue('created_at')).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            }),
    },
    {
        id: 'last login',
        header: 'Last Login',
        cell: ({ row }) =>
            new Date(row.getValue('created_at')).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            }),
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => (
            <div className="flex items-center space-x-2">
                <Link href={route('admin.users.edit', row.original.id)}>
                    <ActionButton action="edit">
                        <span className="text-sm">Edit</span>
                    </ActionButton>
                </Link>
                <DeleteButton userId={row.original.id} />
            </div>
        ),
    },
];

const DeleteButton = ({ userId }: { userId: number }) => {
    return (
        <ActionButton
            action="delete"
            onClick={() => {
                router.delete(route('admin.users.destroy', userId), {
                    preserveScroll: true,
                    preserveState: true,
                    onBefore: () =>
                        confirm('Are you sure you want to delete this user?'),
                    onSuccess: () => {
                        toast.success('User deleted successfully');
                    },
                    onError: (error) => {
                        toast.error('Failed to delete user');
                        console.error(error);
                    },
                });
            }}
        >
            <span className="text-sm">Delete</span>
        </ActionButton>
    );
};
