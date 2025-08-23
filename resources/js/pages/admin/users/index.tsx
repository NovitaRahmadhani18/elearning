import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { GraduationCap, Presentation, UserCog } from 'lucide-react';
import AdminDashboardCard from '../partials/components/admin-dashboard-card';
import UserDataTable from './data-table/data-table';
import { UserIndexPageProps } from './types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
    {
        title: 'Users Management',
        href: '/admin/users',
    },
];

const UsersPage = () => {
    const { count } = usePage<UserIndexPageProps>().props;

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Users Management" />
            <div className="flex flex-1 flex-col gap-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <AdminDashboardCard
                        title="Administrators"
                        value={count?.admin.toString() || '0'}
                        icon={UserCog} // Replace with an actual icon if needed
                    />

                    <AdminDashboardCard
                        title="Teachers"
                        value={count?.teacher.toString() || '0'}
                        icon={Presentation} // Replace with an actual icon if needed
                    />

                    <AdminDashboardCard
                        title="Students"
                        value={count?.student.toString() || '0'}
                        icon={GraduationCap} // Replace with an actual icon if needed
                    />
                </div>
                <UserDataTable />
            </div>
        </AdminTeacherLayout>
    );
};

export default UsersPage;
