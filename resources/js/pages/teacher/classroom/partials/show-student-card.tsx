import { formatDistanceToNow } from 'date-fns';

// Import Ikon
import { Head, usePage } from '@inertiajs/react';
// Import Komponen UI
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useInitials } from '@/hooks/use-initials';
import { cn } from '@/lib/utils';
import { TContentStudent } from '@/pages/admin/monitoring';
import { ShowStudentClassroomPageProps } from '@/pages/student/classrooms/types';

const StatCard = ({ label, value }: { label: string; value: string | number }) => (
    <div className="rounded-lg bg-white/10 p-4 backdrop-blur-sm">
        <p className="text-xs tracking-wider uppercase">{label}</p>
        <p className="text-lg font-semibold">{value}</p>
    </div>
);

// --- Penyesuaian Kecil untuk Menampilkan Info Skor ---
const ActivityItem = ({ activity }: { activity: TContentStudent }) => (
    <div className="flex items-center justify-between border-b border-gray-100 py-3 text-sm">
        <div>
            <p className="text-gray-800 capitalize">
                {activity.content.type}: {activity.content.title}
            </p>
            {activity.content.type === 'quiz' ? (
                <Badge className="mt-1">
                    Score: {activity.score} / {activity.content.points}
                </Badge>
            ) : (
                <Badge className="mt-1">Score: {activity.score ?? 'N/A'}</Badge>
            )}
        </div>
        <p className="ml-4 flex-shrink-0 text-gray-500">
            {formatDistanceToNow(new Date(activity.completed_at ?? ''), {
                addSuffix: true,
            })}
        </p>
    </div>
);
// --- Akhir Penyesuaian ---

// ====================================================================
// KOMPONEN HALAMAN UTAMA
// ====================================================================

const StudentProfilePage = () => {
    const { classroom, student, classroomStudent, achievements, contentStudents } =
        usePage<ShowStudentClassroomPageProps>().props;

    const getInitial = useInitials();
    const completedAchievements = achievements?.data.filter(
        (achievement) => !achievement.locked,
    ).length;
    const lastActivity = contentStudents.data.length
        ? new Date(contentStudents.data[0].completed_at || '')
        : null;

    return (
        <div className="min-h-screen">
            <Head title={`${student.data.name}'s Profile`} />
            {/* Header Section */}
            <div className="rounded bg-white bg-cover bg-center p-6 text-gray-700 shadow-lg md:p-8">
                <div className="mx-auto max-w-7xl">
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div className="flex items-center gap-4">
                            <Avatar className="h-20 w-20 border-4 border-white/50 text-3xl">
                                <AvatarFallback className="bg-blue-400 text-white">
                                    {getInitial(student.data.name)}
                                </AvatarFallback>
                            </Avatar>
                            <div>
                                <h1 className="text-3xl font-bold">
                                    {student.data.name}
                                </h1>
                                <p className="">
                                    {student.data.email} •{' '}
                                    <Badge
                                        variant="secondary"
                                        className="capitalize"
                                    >
                                        {student.data.role}
                                    </Badge>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="mt-8">
                        <h2 className="mb-2 text-sm font-semibold">Quick Stats</h2>
                        <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                            <StatCard
                                label="Classroom"
                                value={classroom.data.fullName}
                            />
                            <StatCard
                                label="Completion Rate"
                                value={classroomStudent.data.progress + '%'}
                            />
                            <StatCard
                                label="Achievements"
                                value={completedAchievements}
                            />
                            <StatCard
                                label="Last Activity"
                                value={
                                    lastActivity
                                        ? formatDistanceToNow(lastActivity, {
                                              addSuffix: true,
                                          })
                                        : 'No activity'
                                }
                            />
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content Section */}
            <main className="mx-auto max-w-7xl px-4 py-8 sm:px-0">
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    {/* Kolom Kiri: Personal Info */}
                    <div className="lg:col-span-1">
                        <Card>
                            <CardHeader>
                                <CardTitle>Achievements</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {achievements?.data?.map((achievement) => (
                                    <div
                                        key={achievement.id}
                                        className={cn(
                                            'relative flex items-center justify-between border-b border-gray-100 py-3 text-sm',
                                            achievement.locked ? 'opacity-50' : '',
                                        )}
                                    >
                                        <div>
                                            <p className="text-gray-800">
                                                {achievement.name}
                                            </p>
                                            <p className="text-gray-500">
                                                {achievement.description}
                                            </p>
                                        </div>
                                        <div className="absolute top-2 right-0">
                                            {achievement.locked ? (
                                                <Badge variant="secondary">
                                                    Locked
                                                </Badge>
                                            ) : (
                                                <Badge>Unlocked</Badge>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Kolom Kanan: Recent Activity */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Progress</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {contentStudents.data.length > 0 ? (
                                    contentStudents.data.map((activity, index) => (
                                        <ActivityItem
                                            key={index}
                                            activity={activity}
                                        />
                                    ))
                                ) : (
                                    <p className="text-sm text-gray-500">
                                        No recent activity.
                                    </p>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </main>
        </div>
    );
};

export default StudentProfilePage;
