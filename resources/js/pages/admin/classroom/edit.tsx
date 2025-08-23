import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import EditClassroomForm from './forms/edit-classroom-form';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
    {
        title: 'Classrooms Management',
        href: '/admin/classrooms',
    },
    {
        title: 'Edit Classroom',
        href: '/admin/classrooms/edit',
    },
];

const ClassroomEditPage = () => {
    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Classroom" />
            <div className="flex-1 flex-col gap-4">
                <EditClassroomForm />
            </div>
        </AdminTeacherLayout>
    );
};

export default ClassroomEditPage;
