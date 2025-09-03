import DataTable from '@/components/data-table/data-table';
import { Button } from '@/components/ui/button';
import { TUser } from '@/types/users';
import { usePage } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import UserCard from '../partials/components/user-card';
import { ShowClassroomPageProps } from './types';

export type TClassroomRecapNilai = {
    student: {
        data: TUser;
    };
    scores: {
        content_id: number;
        content_title: string;
        score: number | null;
    }[];
    total_score: number | null;
    average_score: number | null;
};

const ShowRekap = () => {
    const { rekapNilai, classroom } = usePage<ShowClassroomPageProps>().props;
    return (
        <div>
            <div className="mb-4 flex justify-end">
                <Button asChild>
                    <a
                        href={route(
                            'classrooms.export-rekap-nilai',
                            classroom.data.id,
                        )}
                    >
                        Download Excel
                    </a>
                </Button>
            </div>

            <DataTable
                data={rekapNilai}
                columns={columns(rekapNilai)}
                title="Recap Nilai Students"
            />
        </div>
    );
};

const columns = (
    rekapNilai: TClassroomRecapNilai[],
): ColumnDef<TClassroomRecapNilai>[] => {
    const baseCols: ColumnDef<TClassroomRecapNilai>[] = [
        {
            id: 'number',
            header: '#',
            cell: ({ row }) => row.index + 1,
        },
        {
            accessorKey: 'student.data.name',
            header: 'Student Name',
            cell: ({ row }) => <UserCard user={row.original.student.data} />,
        },
        {
            accessorKey: 'total_score',
            header: 'Total Score',
            cell: (info) => info.getValue() ?? '-',
        },
        {
            accessorKey: 'average_score',
            header: 'Average Score',
            cell: (info) => info.getValue() ?? '-',
        },
    ];

    const dynamicCols: ColumnDef<TClassroomRecapNilai>[] = [];
    if (rekapNilai.length > 0) {
        const firstStudentScores = rekapNilai[0].scores;
        firstStudentScores.forEach((score) => {
            dynamicCols.push({
                accessorKey: `scores.${score.content_id}`,
                header: () => {
                    return (
                        <span className="line-clamp-2 max-w-[80px] text-sm break-words whitespace-break-spaces">
                            {score.content_title}
                        </span>
                    );
                },
                cell: (info) => {
                    const scores = info.row.original.scores;
                    const scoreItem = scores.find(
                        (s) => s.content_id === score.content_id,
                    );
                    return scoreItem?.score ?? '-';
                },
            });
        });
    }

    return [...baseCols.slice(0, 2), ...dynamicCols, ...baseCols.slice(2)];
};

export default ShowRekap;
