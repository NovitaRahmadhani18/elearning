import { ColumnDef } from '@tanstack/react-table';
import { TContentStudent } from '..';

export const monitoringTableColumns: ColumnDef<TContentStudent>[] = [
    {
        accessorKey: '#',
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorKey: 'created_at',
        header: 'Timestamp',
        cell: ({ row }) =>
            new Date(row.original.completed_at ?? '').toLocaleString(),
    },
    {
        accessorKey: 'user.name',
        header: 'User',
        cell: ({ row }) => {
            const user = row.original.user;
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
        id: 'classroom',
        header: 'Classroom',
        cell: ({ row }) => {
            const classroom = row.original.content.classroom;
            return (
                <span className="text-sm text-gray-600">
                    {classroom ? classroom.fullName : 'N/A'}
                </span>
            );
        },
    },
    {
        id: 'desc',
        header: 'Description',
        cell: ({ row }) => {
            const content = row.original.content;

            return (
                <div className="w-xs">
                    <span className="text-xs text-gray-700">
                        {content.type === 'quiz'
                            ? 'Submitted quiz '
                            : 'Completed material '}
                        '{content.title}' and earned {row.original.score} points.
                    </span>
                </div>
            );
        },
    },
];
