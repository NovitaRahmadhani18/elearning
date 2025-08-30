import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { TClassroom } from '@/pages/admin/classroom/types';
import { ContentProgressBlock } from '@/pages/student/leaderboard/leaderboard';
import { TContentLeaderboard } from '@/pages/student/leaderboard/types';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useMemo } from 'react';

export interface ShowContentClassroomProps extends SharedData {
    classroom: {
        data: TClassroom;
    };
    content: {
        data: TContentLeaderboard;
    };
}

const ShowContentClassroom = () => {
    const { classroom, content } = usePage<ShowContentClassroomProps>().props;

    const breadcrumbs: BreadcrumbItem[] = useMemo(
        () => [
            {
                title: 'Classrooms Management',
                href: '/teacher/classrooms',
            },
            {
                title: classroom.data.fullName,
                href: route('teacher.classrooms.show', {
                    classroom: classroom.data.id,
                }),
            },
            {
                title: content.data.title,
                href: '/teacher/classrooms/show-student',
            },
        ],
        [classroom, content],
    );

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Classroom" />
            <div className="flex-1 flex-col gap-4">
                <ContentProgressBlock
                    content={content.data}
                    limit={content.data.leaderboard.length}
                />
            </div>
        </AdminTeacherLayout>
    );
};

export default ShowContentClassroom;
