import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { ShowStudentClassroomPageProps } from '@/pages/student/classrooms/types';
import StudentProfilePage from '@/pages/teacher/classroom/partials/show-student-card';
import { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useMemo } from 'react';

const ShowStudentClassroom = () => {
    const { classroom, student } = usePage<ShowStudentClassroomPageProps>().props;

    const breadcrumbs: BreadcrumbItem[] = useMemo(
        () => [
            {
                title: 'Dashboard',
                href: '/',
            },
            {
                title: 'Classrooms Management',
                href: '/admin/classrooms',
            },
            {
                title: classroom.data.fullName,
                href: route('admin.classrooms.show', {
                    classroom: classroom.data.id,
                }),
            },
            {
                title: student.data.name,
                href: '/admin/classrooms/show-student',
            },
        ],
        [classroom, student],
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
