import { Button } from '@/components/ui/button';
import { TUser } from '@/types/users';
import { Link } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { ArrowRight } from 'lucide-react';

export const studentColumn: ColumnDef<TUser>[] = [
    {
        accessorKey: '#',
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorKey: 'user.name',
        header: 'User',
        cell: ({ row }) => {
            const user = row.original;
            return (
                <div className="my-2 flex items-center gap-2">
                    {user.avatar ? (
                        <img
                            src={user.avatar}
                            alt={user.name}
                            className="h-8 w-8 rounded-full"
                        />
                    ) : (
                        <div className="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200">
                            <span className="text-gray-500">{user.name[0]}</span>
                        </div>
                    )}
                    <div className="flex flex-col">
                        <span>{user.name}</span>

                        <span className="text-sm text-gray-500" title={user.email}>
                            {user.email}
                        </span>
                    </div>
                </div>
            );
        },
    },
    {
        accessorKey: 'created_at',
        header: 'Joined At',
        cell: ({ row }) => {
            const date = new Date(row.original.created_at);
            return date.toLocaleDateString();
        },
    },
    {
        id: 'action',
        header: 'Action',
        cell: ({ row }) => {
            return <ActionColumn userId={row.original.id} key={row.original.id} />;
        },
    },
];

const ActionColumn = ({ userId }: { userId: number }) => {
    return (
        <div className="flex items-center gap-2">
            <Button size={'sm'} variant="outline" className="text-xs" asChild>
                <Link
                    href={route('teacher.classrooms.student.show', {
                        classroom: route().params.classroom,
                        student: userId,
                    })}
                >
                    View
                    <ArrowRight className="ml-1 h-4 w-4" />
                </Link>
            </Button>
        </div>
    );
};
