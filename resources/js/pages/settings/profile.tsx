import { Head, useForm, usePage } from '@inertiajs/react';
import React from 'react';
import { toast } from 'sonner';

// Import komponen-komponen reusable kita
import { AvatarInput } from '@/components/form/avatar-input';
import { FormField } from '@/components/form/form-field';
import { SelectInput } from '@/components/form/select-field';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import SettingsLayout from '@/layouts/settings/layout';
import { SharedData } from '@/types';

const UpdateProfileInformationForm = ({
    user,
}: {
    user: SharedData['auth']['user'];
}) => {
    const { data, setData, post, errors, processing, recentlySuccessful } = useForm({
        _method: 'PATCH', // Menggunakan PATCH untuk update parsial
        name: user.name,
        email: user.email,
        avatar: null as File | null,
        id_number: user.id_number || '',
        gender: user.gender || '',
        address: user.address || '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('profile.update'), {
            onSuccess: () => toast.success('Profile updated successfully!'),
        });
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>Profile Information</CardTitle>
                <CardDescription>
                    Update your account's profile information and email address.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form onSubmit={submit} className="space-y-6">
                    <div>
                        <p className="text-sm font-medium">Profile Photo</p>
                        <div className="mt-2 flex items-center gap-4">
                            <AvatarInput
                                id="avatar"
                                value={data.avatar || user.avatar_url}
                                onChange={(file) => setData('avatar', file)}
                                size="lg"
                            />
                            <p className="text-sm text-muted-foreground">
                                Upload a new profile picture (JPG, PNG, max 2MB)
                            </p>
                        </div>
                    </div>

                    <FormField
                        id="name"
                        label="Name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        error={errors.name}
                        required
                    />
                    <FormField
                        id="email"
                        label="Email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        error={errors.email}
                        required
                    />

                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <FormField
                            id="id_number"
                            label="Student/Teacher ID"
                            value={data.id_number}
                            onChange={(e) => setData('id_number', e.target.value)}
                            placeholder="Enter your student or teacher ID"
                            error={errors.id_number}
                        />
                        <SelectInput
                            id="gender"
                            label="Gender"
                            placeholder="Select Gender"
                            value={data.gender}
                            onChange={(value) => setData('gender', value)}
                            options={[
                                { value: 'male', label: 'Male' },
                                { value: 'female', label: 'Female' },
                            ]}
                            error={errors.gender}
                        />
                    </div>

                    <FormField
                        id="address"
                        label="Address"
                        value={data.address}
                        onChange={(e) => setData('address', e.target.value)}
                        placeholder="Enter your full address"
                        error={errors.address}
                        textarea
                        rows={4}
                    />

                    <div className="flex items-center gap-4">
                        <Button disabled={processing}>
                            {processing ? 'Saving...' : 'Save Changes'}
                        </Button>
                        {recentlySuccessful && (
                            <p className="text-sm text-muted-foreground">Saved.</p>
                        )}
                    </div>
                </form>
            </CardContent>
        </Card>
    );
};

// --- Form untuk Keamanan / Ubah Password ---
const UpdatePasswordForm = () => {
    const { data, setData, put, errors, processing, recentlySuccessful, reset } =
        useForm({
            current_password: '',
            password: '',
            password_confirmation: '',
        });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('password.update'), {
            onSuccess: () => {
                reset();
                toast.success('Password updated successfully!');
            },
        });
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>Security</CardTitle>
                <CardDescription>
                    Update your password to keep your account secure.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form onSubmit={submit} className="space-y-6">
                    <FormField
                        id="current_password"
                        label="Current Password"
                        type="password"
                        value={data.current_password}
                        onChange={(e) => setData('current_password', e.target.value)}
                        error={errors.current_password}
                        autoComplete="current-password"
                    />
                    <FormField
                        id="password"
                        label="New Password"
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        error={errors.password}
                        autoComplete="new-password"
                    />
                    <FormField
                        id="password_confirmation"
                        label="Confirm Password"
                        type="password"
                        value={data.password_confirmation}
                        onChange={(e) =>
                            setData('password_confirmation', e.target.value)
                        }
                        error={errors.password_confirmation}
                        autoComplete="new-password"
                    />

                    <div className="flex items-center gap-4">
                        <Button disabled={processing}>
                            {processing ? 'Updating...' : 'Update Password'}
                        </Button>
                        {recentlySuccessful && (
                            <p className="text-sm text-muted-foreground">Saved.</p>
                        )}
                    </div>
                </form>
            </CardContent>
        </Card>
    );
};

// ====================================================================
// KOMPONEN HALAMAN UTAMA
// ====================================================================
const EditProfilePage = () => {
    const { auth } = usePage<SharedData>().props;

    return (
        <SettingsLayout>
            <Head title="Profile Settings" />
            <div className="grid grid-cols-1 items-start gap-8 lg:grid-cols-3">
                {/* Kolom Kiri - Informasi Profil */}
                <div className="lg:col-span-2">
                    <UpdateProfileInformationForm user={auth.user} />
                </div>
                {/* Kolom Kanan - Keamanan */}
                <div className="lg:col-span-1">
                    <UpdatePasswordForm />
                </div>
            </div>
        </SettingsLayout>
    );
};

export default EditProfilePage;
