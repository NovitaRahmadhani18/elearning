import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import CreateClassroomForm from './forms/create-classroom-form';

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
        title: 'Create Classroom',
        href: '/admin/classrooms/create',
    },
];

const CreateClassroomPage = () => {
    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Classroom" />
            <div className="flex-1 flex-col gap-4">
                <CreateClassroomForm />
            </div>
        </AdminTeacherLayout>
    );
};

export default CreateClassroomPage;
