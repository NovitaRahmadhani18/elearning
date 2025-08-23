import Heading from '@/components/heading';
import HeadingSmall from '@/components/heading-small';
import StudentLayout from '@/layouts/student-layout';
import { SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { BookOpen, CheckCircle2 } from 'lucide-react';
import AdminDashboardCard from '../admin/partials/components/admin-dashboard-card';
import DashboardClassroomCard from './partials/components/dashboard-classroom-card';
import UpcomingDeadlineCard from './partials/components/upcoming-deadline-card';

const DashboardStudent = () => {
    const { auth } = usePage<SharedData>().props;

    return (
        <StudentLayout>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-4 space-y-4">
                <div>
                    <Heading
                        title="Dashboard"
                        description={`Welcome back, ${auth.user.name}!`}
                    />
                </div>

                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <AdminDashboardCard
                        title="Classroom in Progress"
                        value="1,234"
                        icon={BookOpen}
                    />

                    <AdminDashboardCard
                        title="Completed Class"
                        value="1,234"
                        icon={CheckCircle2}
                    />
                </div>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    {/* current courses */}
                    <div className="mb-6 space-y-4 rounded-lg bg-white p-6 shadow-sm lg:col-span-2">
                        <HeadingSmall title="Current Courses" />
                        <section className="space-y-2">
                            {Array(3)
                                .fill(0)
                                .map((_, index) => (
                                    <DashboardClassroomCard key={index} />
                                ))}
                        </section>
                    </div>

                    {/* upcoming deadline */}
                    <div className="lg:col-span-1">
                        <div className="mb-6 space-y-4 rounded-lg bg-white p-6 shadow-sm">
                            <HeadingSmall title="Upcoming Deadlines" />
                            <div className="space-y-2">
                                {Array(3)
                                    .fill(0)
                                    .map((_, index) => (
                                        <UpcomingDeadlineCard key={index} />
                                    ))}
                            </div>
                        </div>

                        <div className="mb-6 rounded-lg bg-white p-6 shadow-sm">
                            <HeadingSmall title="Achievements Badges" />
                            <div className="bg-red-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </StudentLayout>
    );
};

export default DashboardStudent;
