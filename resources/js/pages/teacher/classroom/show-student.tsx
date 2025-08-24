import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { TClassroom } from '@/pages/admin/classroom/types';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import StudentProfilePage from './partials/show-student-card';

interface ShowStudentClassroomProps extends SharedData {
    classroom: {
        data: TClassroom;
    };
}

const ShowStudentClassroom = () => {
    const { classroom, student } = usePage<ShowStudentClassroomProps>().props;

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
                title: 'Show Student',
                href: '/teacher/classrooms/show-student',
            },
        ],
        [classroom],
    );

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Classroom" />
            <div className="flex-1 flex-col gap-4">
                <StudentProfilePage />
            </div>
        </AdminTeacherLayout>
    );
};

export default ShowStudentClassroom;
