import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useInitials } from '@/hooks/use-initials'; // Asumsi hook ini ada
import SettingsLayout from '@/layouts/settings/layout';
import { SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { format, formatDistanceToNow, subWeeks } from 'date-fns';
import { id as indonesiaLocale } from 'date-fns/locale';

// ====================================================================
// TIPE DATA & MOCKUP
// ====================================================================

type TActivity = {
    id: number;
    description: string;
    created_at: string; // ISO String
};

// Data dummy untuk melengkapi informasi profil
const mockProfileData = {
    stats: {
        classrooms: 0,
        last_login: subWeeks(new Date(), 1).toISOString(), // 1 minggu yang lalu
        member_since: new Date('2025-08-12T09:00:00Z').toISOString(),
        last_updated: new Date('2025-08-12T09:00:00Z').toISOString(),
    },
    personal_info: {
        id_number: null,
        gender: null,
        address: null,
    },
    recent_activity: [
        {
            id: 1,
            description: 'logged in',
            created_at: subWeeks(new Date(), 1).toISOString(),
        },
        {
            id: 2,
            description: 'logged in',
            created_at: subWeeks(new Date(), 1).toISOString(),
        },
        {
            id: 3,
            description: 'logged in',
            created_at: subWeeks(new Date(), 1).toISOString(),
        },
        {
            id: 4,
            description: 'logged in',
            created_at: subWeeks(new Date(), 1).toISOString(),
        },
        {
            id: 5,
            description: 'logged in',
            created_at: subWeeks(new Date(), 1).toISOString(),
        },
    ],
};

// ====================================================================
// SUB-KOMPONEN
// ====================================================================

const StatCard = ({ label, value }: { label: string; value: string | number }) => (
    <div className="rounded-lg border bg-slate-50 p-4">
        <p className="text-xs tracking-wider text-slate-500 uppercase">{label}</p>
        <p className="text-lg font-bold text-slate-800">{value}</p>
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

const ActivityItem = ({ activity }: { activity: TActivity }) => (
    <div className="flex justify-between border-b border-gray-100 py-3 text-sm">
        <p className="text-gray-800">{activity.description}</p>
        <p className="ml-4 flex-shrink-0 text-gray-500">
            {formatDistanceToNow(new Date(activity.created_at), {
                addSuffix: true,
                locale: indonesiaLocale,
            })}
        </p>
    </div>
);

// ====================================================================
// KOMPONEN HALAMAN UTAMA
// ====================================================================

const ShowProfilePage = () => {
    const { user } = usePage<SharedData>().props.auth;
    const getInitials = useInitials();

    // Menggabungkan data asli dengan data dummy
    const profileData = {
        ...user,
        ...mockProfileData,
    };

    return (
        <SettingsLayout>
            <Head title="My Profile" />

            <div className="space-y-6 rounded-lg bg-white p-6 shadow-sm">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex items-center gap-4">
                        <Avatar className="h-20 w-20 border-4 text-3xl">
                            <AvatarFallback>{getInitials(user.name)}</AvatarFallback>
                        </Avatar>
                        <div>
                            <h1 className="text-3xl font-bold">{user.name}</h1>
                            <p className="text-muted-foreground">
                                {user.email} •{' '}
                                <Badge
                                    variant="outline"
                                    className="border-primary text-primary capitalize"
                                >
                                    {user.role}
                                </Badge>
                            </p>
                        </div>
                    </div>
                    <Button asChild className="mt-4 sm:mt-0">
                        <Link href={route('profile.edit')}>Edit Profile</Link>
                    </Button>
                </div>

                {/* Quick Stats Section */}
                <div className="">
                    <h2 className="mb-2 text-sm font-semibold text-muted-foreground">
                        Quick Stats
                    </h2>
                    <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                        <StatCard
                            label="Classrooms"
                            value={profileData.stats.classrooms}
                        />
                        <StatCard
                            label="Last Login"
                            value={formatDistanceToNow(
                                new Date(profileData.stats.last_login),
                                { addSuffix: true },
                            )}
                        />
                        <StatCard
                            label="Member Since"
                            value={format(
                                new Date(profileData.stats.member_since),
                                'dd MMM yyyy',
                            )}
                        />
                        <StatCard
                            label="Last Updated"
                            value={format(
                                new Date(profileData.stats.last_updated),
                                'dd MMM yyyy',
                            )}
                        />
                    </div>
                </div>
            </div>

            {/* Main Content Section */}
            <main className="mt-8">
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    {/* Kolom Kiri: Personal Info */}
                    <div className="lg:col-span-1">
                        <Card>
                            <CardHeader>
                                <CardTitle>Personal Info</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <PersonalInfoItem
                                    label="ID Number"
                                    value={profileData.personal_info.id_number}
                                />
                                <PersonalInfoItem
                                    label="Gender"
                                    value={profileData.personal_info.gender}
                                />
                                <PersonalInfoItem
                                    label="Address"
                                    value={profileData.personal_info.address}
                                />
                                <PersonalInfoItem
                                    label="Member Since"
                                    value={format(
                                        new Date(profileData.stats.member_since),
                                        'dd MMM yyyy',
                                    )}
                                />
                                <PersonalInfoItem
                                    label="Last Updated"
                                    value={format(
                                        new Date(profileData.stats.last_updated),
                                        'dd MMM yyyy',
                                    )}
                                />
                            </CardContent>
                        </Card>
                    </div>

                    {/* Kolom Kanan: Recent Activity */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Recent Activity</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {profileData.recent_activity.map((activity) => (
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
        </SettingsLayout>
    );
};

export default ShowProfilePage;
