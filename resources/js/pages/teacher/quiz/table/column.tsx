import UserCard from '@/pages/admin/partials/components/user-card';
import { TStudentSubmission } from '@/pages/student/leaderboard/leaderboard';
import { ColumnDef } from '@tanstack/react-table';

export const studentQuizColumn: ColumnDef<TStudentSubmission>[] = [
    {
        id: 'number',
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorKey: 'student',
        header: 'Student',
        cell: ({ row }) => {
            return <UserCard user={row.original.student} />;
        },
    },
    {
        accessorKey: 'completed_at',
        header: 'Completed',
        cell: ({ row }) => {
            const date = new Date(row.original.completed_at);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        },
    },
    {
        accessorKey: 'points_awarded',
        header: 'Point',
        cell: ({ row }) => {
            const points = row.original.points_awarded;
            return points ? `${points} Points` : 'No Points Awarded';
        },
    },
];
