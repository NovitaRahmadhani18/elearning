import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
];

const DashboardTeacher = () => {
    return <AdminTeacherLayout breadcrumbs={breadcrumbs}>ad</AdminTeacherLayout>;
};

export default DashboardTeacher;
