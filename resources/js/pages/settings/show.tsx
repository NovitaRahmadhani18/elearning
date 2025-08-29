import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useInitials } from '@/hooks/use-initials';
import SettingsLayout from '@/layouts/settings/layout';
import { SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { format } from 'date-fns';
import { TContentStudent } from '../admin/monitoring';

const PersonalInfoItem = ({
    label,
    value,
}: {
    label: string;
    value: string | number | null;
}) => (
    <div className="flex justify-between border-b border-gray-100 py-3 text-sm">
        <p className="text-gray-500">{label}</p>
        <p className="font-medium text-gray-800 capitalize">{value || '—'}</p>
    </div>
);

const CardPersonalInfo = ({ title, value }: { title: string; value?: string }) => (
    <Card className="gap-2 bg-gray-50">
        <CardHeader>
            <CardTitle className="text-sm font-normal">{title}</CardTitle>
        </CardHeader>
        <CardContent>
            <p className="text-xl font-semibold">{value || '-'}</p>
        </CardContent>
    </Card>
);

interface ShowProfilePageProps extends SharedData {
    classroomsCount: number;
    contentStudent: {
        data: TContentStudent[];
    };
}

const ShowProfilePage = () => {
    const { auth, contentStudent, classroomsCount } =
        usePage<ShowProfilePageProps>().props;
    const user = auth.user;
    const getInitials = useInitials();

    return (
        <SettingsLayout>
            <Head title="My Profile" />

            <div className="space-y-6 rounded-lg bg-white p-6 shadow-sm">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex items-center gap-4">
                        <Avatar className="h-20 w-20 border-4 text-3xl">
                            <AvatarImage
                                src={user.avatar}
                                alt={user.name}
                                className="object-cover"
                            />
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
                <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <CardPersonalInfo
                        title="Classrooms"
                        value={classroomsCount.toString()}
                    />
                    <CardPersonalInfo
                        title="Member Since"
                        value={format(new Date(user.created_at), 'dd MMM yyyy')}
                    />
                    <CardPersonalInfo
                        title="Last Update"
                        value={format(new Date(user.updated_at), 'dd MMM yyyy')}
                    />
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
                                    value={user.id_number || '-'}
                                />
                                <PersonalInfoItem
                                    label="Gender"
                                    value={user.gender}
                                />
                                <PersonalInfoItem
                                    label="Last Updated"
                                    value={format(
                                        new Date(user.updated_at),
                                        'dd MMM yyyy',
                                    )}
                                />
                                <PersonalInfoItem
                                    label="Member Since"
                                    value={format(
                                        new Date(user.created_at),
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
                                {contentStudent.data.length > 0 ? (
                                    contentStudent.data.map((activity, index) => (
                                        <div
                                            key={index}
                                            className="border-b border-gray-100 py-3 last:border-0"
                                        >
                                            <p className="text-sm text-gray-800">
                                                {activity.description ||
                                                    'No description'}
                                            </p>
                                            <p className="text-xs text-gray-500">
                                                {format(
                                                    new Date(
                                                        activity.completed_at || '',
                                                    ),
                                                    'dd MMM yyyy HH:mm',
                                                )}
                                            </p>
                                        </div>
                                    ))
                                ) : (
                                    <p className="text-center text-gray-500">
                                        No recent activity.
                                    </p>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </main>
        </SettingsLayout>
    );
};

export default ShowProfilePage;
