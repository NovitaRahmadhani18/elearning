import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import StudentProfilePage from './partials/show-student-card';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
    {
        title: 'Classrooms Management',
        href: '/teacher/classrooms',
    },
    {
        title: 'Classroom Details',
        href: '/teacher/classrooms/show',
    },
    {
        title: 'Show Student',
        href: '/teacher/classrooms/show-student',
    },
];

const ShowStudentClassroom = () => {
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
