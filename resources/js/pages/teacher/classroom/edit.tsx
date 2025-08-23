import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import EditClassroomForm from './forms/edit-classroom-form';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Classrooms Management',
        href: '/teacher/classrooms',
    },
    {
        title: 'Edit Classroom',
        href: '/teacher/classrooms/create',
    },
];

const EditClassroomPage = () => {
    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Classroom" />
            <div className="flex-1 flex-col gap-4">
                <EditClassroomForm />
            </div>
        </AdminTeacherLayout>
    );
};

export default EditClassroomPage;
