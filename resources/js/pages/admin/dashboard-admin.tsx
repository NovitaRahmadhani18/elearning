import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { GraduationCap, LibraryBig, Users } from 'lucide-react';
import { useMemo } from 'react';
import AdminDashboardCard from './partials/components/admin-dashboard-card';
import { TActvityUser } from './types';

import { intlFormatDistance } from 'date-fns';

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
}: {
    classroomCount: number;
    totalUserCount: number;
    completionCount: number;
}) => {
    const userActivities: TActvityUser[] = useMemo(() => {
        return mockActivityLog;
    }, []);

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
                <Card className="">
                    <CardHeader>
                        <h2 className="text-xl font-semibold">Recent Activities</h2>
                    </CardHeader>
                    <Separator />
                    <CardContent className="space-y-4">
                        {userActivities.length > 0 ? (
                            userActivities.map((activity, index) => (
                                <AdminActivityUserCard
                                    key={index}
                                    activity={activity}
                                />
                            ))
                        ) : (
                            <p className="text-muted-foreground">
                                No recent activities found.
                            </p>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AdminTeacherLayout>
    );
};

export default DashboardAdmin;

interface AdminActivityUserCardProps {
    activity: TActvityUser;
}

const AdminActivityUserCard: React.FC<AdminActivityUserCardProps> = ({
    activity,
}) => {
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
                    {activity.user.name} {activity.desc}
                </p>
                <p className="text-sm text-muted-foreground">
                    {intlFormatDistance(new Date(activity.created_at), new Date(), {
                        style: 'long',
                    })}
                </p>
            </div>
        </div>
    );
};

// Mockup Data for the Activity Log
export const mockActivityLog: TActvityUser[] = [
    {
        user: {
            id: 2,
            name: 'Budi Santoso',
            email: 'budi.s@example.com',
            avatar: 'https://i.pravatar.cc/150?u=budi',
        },
        desc: "submitted the quiz 'Chapter 1 Quiz: Linear Equations' and earned 40 points.",
        created_at: '2025-08-23T12:05:11.861Z', // A few moments ago (UTC)
    },
    {
        user: {
            id: 4,
            name: 'Dewi Lestari',
            email: 'dewi.l@example.com',
            avatar: null, // Example of a user without an avatar
        },
        desc: "completed the material 'Introduction to Basic Algebra' and earned 10 points.",
        created_at: '2025-08-23T11:45:23.512Z',
    },
    {
        user: {
            id: 3,
            name: 'Cici Paramida',
            email: 'cici.p@example.com',
            avatar: 'https://i.pravatar.cc/150?u=cici',
        },
        desc: "submitted the quiz 'Chapter 1 Quiz: Linear Equations' and earned 50 points.",
        created_at: '2025-08-23T10:15:05.123Z',
    },
    {
        user: {
            id: 1,
            name: 'Tanek',
            email: 'tanek@example.com',
            avatar: 'https://i.pravatar.cc/150?u=tanek',
        },
        desc: "completed the material 'Introduction to Spatial Geometry' and earned 15 points.",
        created_at: '2025-08-22T14:30:18.992Z', // Yesterday
    },
    {
        user: {
            id: 5,
            name: 'Eko Prasetyo',
            email: 'eko.p@example.com',
            avatar: 'https://i.pravatar.cc/150?u=eko',
        },
        desc: "viewed the material 'Introduction to Basic Algebra' and earned 10 points.",
        created_at: '2025-08-22T09:00:45.332Z',
    },
    {
        user: {
            id: 2,
            name: 'Budi Santoso',
            email: 'budi.s@example.com',
            avatar: 'https://i.pravatar.cc/150?u=budi',
        },
        desc: "completed the material 'Introduction to Basic Algebra' and earned 10 points.",
        created_at: '2025-08-21T16:20:55.781Z', // Two days ago
    },
    {
        user: {
            id: 4,
            name: 'Dewi Lestari',
            email: 'dewi.l@example.com',
            avatar: null,
        },
        desc: "submitted the quiz 'History of Independence Quiz' and earned 85 points.",
        created_at: '2025-08-21T15:55:10.104Z',
    },
];
