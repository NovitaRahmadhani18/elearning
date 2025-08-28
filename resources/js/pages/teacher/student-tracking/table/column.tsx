import { Progress } from '@/components/ui/progress';
import { ColumnDef } from '@tanstack/react-table';
import { TStudentClassroom } from '../types';

export const studentTrackingTableColumns: ColumnDef<TStudentClassroom>[] = [
    {
        accessorKey: '#',
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorKey: 'student.name',
        header: 'Student Name',
        cell: ({ row }) => row.original.student.name,
    },
    {
        accessorKey: 'classroom.name',
        header: 'Classroom Name',
        cell: ({ row }) => row.original.classroom.fullName,
    },
    {
        id: 'progress',
        header: 'Progress',
        cell: ({ row }) => {
            // randomly generate a progress percentage for demonstration
            const progress = Math.floor(Math.random() * 101); // 0 to 100

            return (
                <div className="w-full">
                    <div className="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                        <Progress value={progress} />
                    </div>
                </div>
            );
        },
    },
    {
        accessorKey: 'completion',
        header: 'Completion',
        cell: ({ row }) => {
            // randomly generate a completion percentage for demonstration
            const completion = Math.floor(Math.random() * 101); // 0 to 100
            return completion + '%';
        },
    },
];
