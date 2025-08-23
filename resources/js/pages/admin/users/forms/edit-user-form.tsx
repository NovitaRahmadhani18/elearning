import { useForm } from '@inertiajs/react';
import React from 'react';
import { toast } from 'sonner';

// Import semua komponen reusable Anda
import { AvatarInput } from '@/components/form/avatar-input';
import { FormField } from '@/components/form/form-field';
import { RadioGroupInput } from '@/components/form/radio-field';
import { SelectInput } from '@/components/form/select-field';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { TUser } from '@/types/users';

const EditUserForm = ({ user }: { user: TUser }) => {
    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT', // Menggunakan PUT untuk update
        avatar: null as File | null, // Field avatar untuk file BARU, null secara default
        id_number: user.id_number,
        name: user.name,
        email: user.email,
        gender: user.gender,
        address: user.address,
        role: user.role,
        is_active: user.is_active,
        password: '',
        password_confirmation: '',
    });

    // 2. Fungsi submit sekarang menargetkan route 'update'
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('admin.users.update', user.id), {
            onSuccess: () => {
                toast.success('User updated successfully!');
            },
            onError: (errors) => {
                toast.error('Failed to update user. Please check the form.');
                console.error(errors);
            },
            preserveState: true,
        });
    };

    return (
        <div className="border-input mx-auto max-w-2xl rounded-xl border bg-white p-6">
            <form onSubmit={handleSubmit} className="space-y-4">
                <div className="mx-auto max-w-fit">
                    {/* value sekarang lebih pintar: jika ada file avatar baru di state (data.avatar),
                      tampilkan previewnya. Jika tidak, tampilkan avatar_url yang ada.
                    */}
                    <AvatarInput
                        id="avatar"
                        label="Avatar"
                        value={data.avatar || user.avatar}
                        onChange={(file) => setData('avatar', file)}
                        error={errors.avatar}
                    />
                </div>

                <FormField
                    id="id_number"
                    label="ID Number (NIP/NISN)"
                    value={data.id_number}
                    onChange={(e) => setData('id_number', e.target.value)}
                    placeholder="Enter ID Number"
                    required
                    error={errors.id_number}
                    disabled={processing}
                />

                <FormField
                    id="name"
                    label="Name"
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                    placeholder="Enter name"
                    required
                    error={errors.name}
                    disabled={processing}
                />

                <FormField
                    id="email"
                    label="Email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    placeholder="Enter email"
                    type="email"
                    required
                    error={errors.email}
                    disabled={processing}
                />

                {/* ... (FormField, SelectInput, RadioGroupInput lain tetap sama) ... */}

                <SelectInput
                    id="gender"
                    label="Gender"
                    placeholder="Select gender"
                    options={[
                        { label: 'Male', value: 'male' },
                        { label: 'Female', value: 'female' },
                    ]}
                    value={data.gender}
                    // @ts-expect-error unknown type for value
                    onChange={(value) => setData('gender', value)}
                    error={errors.gender}
                    disabled={processing}
                />

                <FormField
                    id="address"
                    label="Address"
                    value={data.address}
                    onChange={(e) => setData('address', e.target.value)}
                    required
                    placeholder="Enter address"
                    textarea
                    error={errors.address}
                    disabled={processing}
                />

                <RadioGroupInput
                    id="role"
                    label="Role"
                    options={[
                        { value: 'admin', label: 'Admin' },
                        { value: 'teacher', label: 'Teacher' },
                        { value: 'student', label: 'Student' },
                    ]}
                    value={data.role}
                    // @ts-expect-error unknown type for value
                    onChange={(value) => setData('role', value)}
                    error={errors.role}
                    disabled={processing}
                />

                <div className="border-input flex items-center justify-between rounded-lg border p-4">
                    <Label htmlFor="is_active" className="text-sm font-medium">
                        Active Status
                    </Label>
                    <Switch
                        id="is_active"
                        checked={data.is_active}
                        onCheckedChange={(checked) => setData('is_active', checked)}
                        disabled={processing}
                    />
                </div>

                {/* Password fields: beri catatan bahwa ini opsional */}
                <FormField
                    id="password"
                    label="New Password (Optional)"
                    value={data.password}
                    onChange={(e) => setData('password', e.target.value)}
                    placeholder="Leave blank to keep current password"
                    type="password"
                    required={false} // Tidak wajib, jika tidak diisi, password lama tetap digunakan
                    error={errors.password}
                    disabled={processing}
                />

                <FormField
                    id="password_confirmation"
                    label="Confirm New Password"
                    value={data.password_confirmation}
                    onChange={(e) =>
                        setData('password_confirmation', e.target.value)
                    }
                    placeholder="Confirm new password"
                    type="password"
                    required={false} // Tidak wajib, jika tidak diisi, password lama tetap digunakan
                    error={errors.password_confirmation}
                    disabled={processing}
                />

                <div className="flex items-center justify-end pt-4">
                    {/* Tombol Cancel bisa mengarahkan kembali ke halaman index */}
                    <Button
                        type="button"
                        onClick={() => window.history.back()}
                        className="mr-2 w-full sm:w-auto"
                        variant="outline"
                        disabled={processing}
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        className="w-full sm:w-auto"
                        disabled={processing}
                    >
                        {processing ? 'Saving...' : 'Save Changes'}
                    </Button>
                </div>
            </form>
        </div>
    );
};

export default EditUserForm;
