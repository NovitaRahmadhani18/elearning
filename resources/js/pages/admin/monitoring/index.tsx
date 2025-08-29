import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { TContent } from '@/pages/teacher/material/types';
import { BreadcrumbItem, PaginatedData, SharedData } from '@/types';
import { TUser } from '@/types/users';
import { Head } from '@inertiajs/react';
import { GraduationCap, Users } from 'lucide-react';
import AdminDashboardCard from '../partials/components/admin-dashboard-card';
import MonitoringTable from './tables/data-table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
    {
        title: 'Monitoring',
        href: '/admin/monitoring',
    },
];

export type TContentStudent = {
    id: number;
    content_id: number;
    user_id: number;
    status: string;
    score: number | null;
    completed_at: string | null;
    content: TContent;
    user: TUser;
    description: string | null;
};

export interface MonitoringPageProps extends SharedData {
    monitorings: PaginatedData<TContentStudent>;
}

const MonitoringPage = () => {
    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Monitoring" />
            <div className="flex flex-1 flex-col gap-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <AdminDashboardCard
                        title="Daily Active Users"
                        value="0" // Placeholder value, replace with actual data if available'
                        icon={Users} // Replace with an actual icon if needed
                    />

                    <AdminDashboardCard
                        title="Course Completion Rate"
                        value="0" // Placeholder value, replace with actual data if available
                        icon={GraduationCap} // Replace with an actual icon if needed
                    />
                </div>
                <MonitoringTable />
            </div>
        </AdminTeacherLayout>
    );
};

export default MonitoringPage;
