import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import EditUserForm from './forms/edit-user-form';
import { EditUserFormProps } from './types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
    {
        title: 'Users Management',
        href: '/admin/users',
    },
    {
        title: 'Edit User',
        href: '/admin/users/edit',
    },
];

const EditUsersPage = () => {
    const { user } = usePage<EditUserFormProps>().props;

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Users Management" />
            <div className="flex-1 flex-col gap-4">
                <EditUserForm user={user.data} />
            </div>
        </AdminTeacherLayout>
    );
};

export default EditUsersPage;
