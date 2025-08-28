import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import AdminDashboardCard from '@/pages/admin/partials/components/admin-dashboard-card';
import { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { BookOpenText, ChartBar } from 'lucide-react';
import StudentTrackingTable from './table/data-table';
import { TStudentTrackingPageProps } from './types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Student Tracking',
        href: '/teacher/student-tracking',
    },
];

const StudentTrackingPage = () => {
    const { studentClassrooms, averageCompletionRate } =
        usePage<TStudentTrackingPageProps>().props;

    const totalStudents = studentClassrooms.data?.length ?? 0; // Get the total number of students from the props

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Student Tracking" />
            <div className="flex-1 flex-col gap-4 space-y-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <AdminDashboardCard
                        title="Total Students"
                        value={totalStudents.toString()} // Placeholder value, replace with actual data if available'
                        icon={BookOpenText} // Replace with an actual icon if needed
                    />

                    <AdminDashboardCard
                        title="Average Completion Rate"
                        value={`${averageCompletionRate.toFixed(2)}%`} // Placeholder value, replace with actual data if available
                        icon={ChartBar} // Replace with an actual icon if needed
                    />
                </div>
                <StudentTrackingTable />
            </div>
        </AdminTeacherLayout>
    );
};

export default StudentTrackingPage;
