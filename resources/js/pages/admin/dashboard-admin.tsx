import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { GraduationCap, LibraryBig, Users } from 'lucide-react';
import AdminDashboardCard from './partials/components/admin-dashboard-card';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
];

const DashboardAdmin = () => {
    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <AdminDashboardCard
                        title="Total Users"
                        value="1,234"
                        icon={Users} // Replace with an actual icon if needed
                    />

                    <AdminDashboardCard
                        title="Active Classrooms"
                        value="1,234"
                        icon={LibraryBig} // Replace with an actual icon if needed
                    />

                    <AdminDashboardCard
                        title="Completions"
                        value="1,234"
                        icon={GraduationCap} // Replace with an actual icon if needed
                    />
                </div>
                <Card className="">
                    <CardHeader>
                        <h2 className="text-xl font-semibold">Recent Activities</h2>
                    </CardHeader>
                    <Separator />
                    <CardContent></CardContent>
                </Card>
            </div>
        </AdminTeacherLayout>
    );
};

export default DashboardAdmin;
