import { useForm } from '@inertiajs/react';
import React from 'react';

// Import all your reusable components
import { AvatarInput } from '@/components/form/avatar-input';
import { FormField } from '@/components/form/form-field';
import { RadioGroupInput } from '@/components/form/radio-field';
import { SelectInput } from '@/components/form/select-field';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { toast } from 'sonner';

const CreateUserForm = () => {
    // 1. Initialize useForm with all the fields in the form
    const { data, setData, post, processing, errors, reset } = useForm({
        avatar: null as File | null,
        id_number: '',
        name: '',
        email: '',
        gender: '',
        address: '',
        role: '',
        is_active: true, // Default active status
        password: '',
        password_confirmation: '',
    });

    // 2. Create a function to handle form submission
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        // Replace 'users.store' with your route name in Laravel
        post(route('admin.users.store'), {
            onSuccess: () => {
                toast.success('User created successfully!');
            },
            onError: (errors) => {
                toast.error('Failed to create user. Please check the form.');
                console.error(errors);
            },
        });
    };

    return (
        <div className="border-input mx-auto max-w-2xl rounded-xl border bg-white p-6">
            {/* Connect the handleSubmit function to the form's onSubmit */}
            <form onSubmit={handleSubmit} className="space-y-4">
                <div className="mx-auto max-w-fit">
                    {/* 3. Connect state to each input */}
                    <AvatarInput
                        id="avatar"
                        value={data.avatar}
                        onChange={(file) => setData('avatar', file)}
                        // error={errors.avatar} // You can add an error prop to AvatarInput if needed
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

                <SelectInput
                    id="gender"
                    label="Gender"
                    placeholder="Select gender"
                    options={[
                        { label: 'Male', value: 'male' },
                        { label: 'Female', value: 'female' },
                    ]}
                    value={data.gender}
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

                <FormField
                    id="password"
                    label="Password"
                    value={data.password}
                    onChange={(e) => setData('password', e.target.value)}
                    placeholder="Enter password"
                    type="password"
                    required
                    error={errors.password}
                    disabled={processing}
                />

                <FormField
                    id="password_confirmation"
                    label="Confirm Password"
                    value={data.password_confirmation}
                    onChange={(e) =>
                        setData('password_confirmation', e.target.value)
                    }
                    placeholder="Confirm password"
                    type="password"
                    required
                    error={errors.password_confirmation}
                    disabled={processing}
                />

                <div className="flex items-center justify-end pt-4">
                    <Button
                        type="button" // Change to type="button" to prevent form submission
                        onClick={() => reset()} // Call the reset function
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
                        {processing ? 'Saving...' : 'Create User'}
                    </Button>
                </div>
            </form>
        </div>
    );
};

export default CreateUserForm;
