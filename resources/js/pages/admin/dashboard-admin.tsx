import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem, PaginatedData } from '@/types';
import { Head } from '@inertiajs/react';
import { GraduationCap, LibraryBig, Users } from 'lucide-react';
import AdminDashboardCard from './partials/components/admin-dashboard-card';

import { intlFormatDistance } from 'date-fns';
import { TContentStudent } from './monitoring';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
];

const DashboardAdmin = ({
    classroomCount,
    totalUserCount,
    completionCount,
    recentActivities,
}: {
    classroomCount: number;
    totalUserCount: number;
    completionCount: number;
    recentActivities: PaginatedData<TContentStudent>;
}) => {
    const userActivities = recentActivities.data;

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <AdminDashboardCard
                        title="Total Users"
                        value={totalUserCount.toLocaleString()}
                        icon={Users} // Replace with an actual icon if needed
                    />

                    <AdminDashboardCard
                        title="Active Classrooms"
                        value={classroomCount.toLocaleString()}
                        icon={LibraryBig} // Replace with an actual icon if needed
                    />

                    <AdminDashboardCard
                        title="Completions"
                        value={completionCount.toLocaleString()}
                        icon={GraduationCap} // Replace with an actual icon if needed
                    />
                </div>
                {/* <Card className=""> */}
                {/*     <CardHeader> */}
                {/*         <h2 className="text-xl font-semibold">Recent Activities</h2> */}
                {/*     </CardHeader> */}
                {/*     <Separator /> */}
                {/*     <CardContent className="space-y-4"> */}
                {/*         {userActivities.length > 0 ? ( */}
                {/*             userActivities.map((activity, index) => ( */}
                {/*                 <AdminActivityUserCard */}
                {/*                     key={index} */}
                {/*                     activity={activity} */}
                {/*                 /> */}
                {/*             )) */}
                {/*         ) : ( */}
                {/*             <p className="text-muted-foreground"> */}
                {/*                 No recent activities found. */}
                {/*             </p> */}
                {/*         )} */}
                {/*     </CardContent> */}
                {/* </Card> */}
            </div>
        </AdminTeacherLayout>
    );
};

export default DashboardAdmin;

interface AdminActivityUserCardProps {
    activity: TContentStudent;
}

const AdminActivityUserCard: React.FC<AdminActivityUserCardProps> = ({
    activity,
}) => {
    const desc =
        activity.content.type == 'quiz'
            ? `submitted the quiz '${activity.content.title}' and earned ${activity.score} points.`
            : `completed the material '${activity.content.title}' and earned ${activity.score} points.`;

    return (
        <div className="flex items-center gap-4">
            <div className="flex-shrink-0">
                {activity.user.avatar ? (
                    <img
                        src={activity.user.avatar}
                        alt={activity.user.name}
                        className="h-10 w-10 rounded-full"
                    />
                ) : (
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200">
                        <span className="text-gray-500">
                            {activity.user.name.charAt(0)}
                        </span>
                    </div>
                )}
            </div>
            <div className="flex-1">
                <p className="text-gray-900">
                    <span className="font-semibold">{activity.user.name}</span>{' '}
                    {desc}
                </p>
                <p className="text-sm text-muted-foreground">
                    {intlFormatDistance(
                        new Date(activity.completed_at ?? ''),
                        new Date(),
                        {
                            style: 'long',
                        },
                    )}
                </p>
            </div>
        </div>
    );
};
