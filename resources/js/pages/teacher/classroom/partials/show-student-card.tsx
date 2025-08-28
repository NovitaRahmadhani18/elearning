import { formatDistanceToNow } from 'date-fns';

// Import Ikon
import { Head, usePage } from '@inertiajs/react';
// Import Komponen UI
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useInitials } from '@/hooks/use-initials';
import { cn } from '@/lib/utils';
import { TAchievement } from '@/pages/student/achievement/types';
import { ShowStudentClassroomPageProps } from '@/pages/student/classrooms/types';

// ====================================================================
// TIPE DATA & MOCKUP
// ====================================================================

// --- Tipe Data Disesuaikan untuk Menambah Info Skor ---
type TActivity = {
    id: number;
    description: string;
    score_info: string; // Teks untuk menampilkan skor atau poin
    created_at: string;
};

const mockAchievements: TAchievement[] = [
    {
        id: 1,
        name: 'Quiz Champion',
        description: 'Menyelesaikan kuis dengan nilai ≥ 85',
        image: '/images/achievements/quiz-champion.png',
        locked: true,
    },
    {
        id: 2,
        name: 'Fast Learner',
        description: 'Menyelesaikan kuis dalam waktu < 10 menit',
        image: '/images/achievements/fast-learner.png',
        locked: false,
        achieved_at: '2025-08-12T10:30:00.000Z',
    },
    {
        id: 3,
        name: 'Perfect Score',
        description: 'Mendapatkan nilai sempurna (100) pada salah satu kuis',
        image: '/images/achievements/perfect-score.png',
        locked: true,
    },
    {
        id: 4,
        name: 'Streak Master',
        description: 'Mengerjakan kuis 5 hari berturut-turut',
        image: '/images/achievements/streak-master.png',
        locked: true,
    },
    {
        id: 5,
        name: 'Top Rank',
        description: 'Berada di peringkat 3 besar leaderboard',
        image: '/images/achievements/top-rank.png',
        locked: true,
    },
];

type TUserProfile = {
    id: number;
    name: string;
    email: string;
    role: 'teacher' | 'admin' | 'student';
    avatar_initial: string;
    stats: {
        courses_enrolled: number;
        last_activity: string;
        member_since: string;
        achievements_unlocked: number;
    };
    personal_info: {
        id_number: string | null;
        gender: string | null;
        address: string | null;
    };
    recent_activity: TActivity[];
};

// --- DATA MOCKUP DIUBAH MENJADI DATA SISWA ---
const mockUser: TUserProfile = {
    id: 101,
    name: 'Budi Santoso',
    email: 'budi.santoso@student.com',
    role: 'student',
    avatar_initial: 'BS',
    stats: {
        courses_enrolled: 3,
        last_activity: '2025-08-23T12:05:11Z',
        member_since: '2025-02-10T09:00:00Z',
        achievements_unlocked: 2,
    },
    personal_info: {
        id_number: '123456789',
        gender: 'Male',
        address: 'Jl. Merdeka No. 17, Bandung',
    },
    recent_activity: [
        {
            id: 1,
            description: 'Quiz 1: Matematika Dasar',
            score_info: 'Score: 80/100',
            created_at: '2025-08-23T12:05:11Z',
        },
        {
            id: 2,
            description: 'Pengenalan Aljabar',
            score_info: '+10 points',
            created_at: '2025-08-22T11:00:00Z',
        },
        {
            id: 3,
            description: 'Quiz 2: Faktorisasi dan Persamaan',
            score_info: 'Score: 95/100',
            created_at: '2025-08-21T15:30:00Z',
        },
        {
            id: 4,
            description: 'Apa itu Aljabar?',
            score_info: '+5 points',
            created_at: '2025-08-21T09:15:00Z',
        },
        {
            id: 5,
            description: 'Quiz 2: Aljabar Dasar',
            score_info: 'Score: 95/100',
            created_at: '2025-08-20T10:00:00Z',
        },
    ],
};
// --- AKHIR PERUBAHAN DATA MOCKUP ---

// ====================================================================
// SUB-KOMPONEN
// ====================================================================

const StatCard = ({ label, value }: { label: string; value: string | number }) => (
    <div className="rounded-lg bg-white/10 p-4 backdrop-blur-sm">
        <p className="text-xs tracking-wider uppercase">{label}</p>
        <p className="text-lg font-semibold">{value}</p>
    </div>
);

const PersonalInfoItem = ({
    label,
    value,
}: {
    label: string;
    value: string | null;
}) => (
    <div className="flex justify-between border-b border-gray-100 py-3 text-sm">
        <p className="text-gray-500">{label}</p>
        <p className="font-medium text-gray-800">{value || '—'}</p>
    </div>
);

// --- Penyesuaian Kecil untuk Menampilkan Info Skor ---
const ActivityItem = ({ activity }: { activity: TActivity }) => (
    <div className="flex items-center justify-between border-b border-gray-100 py-3 text-sm">
        <div>
            <p className="text-gray-800">{activity.description}</p>
            {activity.score_info && (
                <p className="font-semibold text-primary">{activity.score_info}</p>
            )}
        </div>
        <p className="ml-4 flex-shrink-0 text-gray-500">
            {formatDistanceToNow(new Date(activity.created_at), {
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
    const { classroom, student } = usePage<ShowStudentClassroomPageProps>().props;

    const getInitial = useInitials();

    const user = mockUser; // Gunakan data siswa dari props atau data mockup;

    return (
        <div className="min-h-screen">
            <Head title={`${user.name}'s Profile`} />
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
                        {/* Tombol Edit Profile Anda (tidak diubah) */}
                    </div>

                    <div className="mt-8">
                        <h2 className="mb-2 text-sm font-semibold">Quick Stats</h2>
                        <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                            <StatCard
                                label="Classroom"
                                value={classroom.data.fullName}
                            />
                            <StatCard label="Completion Rate" value={'80%'} />
                            <StatCard label="Achievements" value={'1'} />
                            <StatCard
                                label="Last Activity"
                                value={formatDistanceToNow(new Date(), {
                                    addSuffix: true,
                                })}
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
                                {mockAchievements.map((achievement) => (
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
                                {user.recent_activity.map((activity) => (
                                    <ActivityItem
                                        key={activity.id}
                                        activity={activity}
                                    />
                                ))}
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </main>
        </div>
    );
};

export default StudentProfilePage;
