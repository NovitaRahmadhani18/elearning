import { useForm, usePage } from '@inertiajs/react';
import React from 'react';
import { toast } from 'sonner';

import { FormField } from '@/components/form/form-field';
import { ImageDropzone } from '@/components/form/image-dropzone';
import { SelectInput } from '@/components/form/select-field';
import { Button } from '@/components/ui/button';
import { TeacherEditClassroomPageProps } from '../types';

const EditClassroomForm = () => {
    const { classroom, categories } = usePage<TeacherEditClassroomPageProps>().props;

    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT', // Menggunakan PUT untuk update
        name: classroom.data.name,
        description: classroom.data.description,
        category_id: classroom.data.category.id.toString(),
        thumbnail: null as File | null,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('teacher.classrooms.update', classroom.data.id), {
            onSuccess: () => {
                toast.success('Classroom updated successfully!');
            },
            onError: (e) => {
                console.error(e);
                toast.error('Failed to update classroom. Please check the form.');
            },
        });
    };

    return (
        <div className="mx-auto max-w-2xl rounded-xl border bg-white p-8">
            <h1 className="mb-6 text-2xl font-bold">Edit Classroom</h1>
            <form onSubmit={handleSubmit} className="space-y-6">
                <FormField
                    id="name"
                    label="Class Title"
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                    placeholder="Enter class title"
                    error={errors.name}
                    disabled={processing}
                    required
                />

                <FormField
                    id="description"
                    label="Class Description"
                    value={data.description}
                    onChange={(e) => setData('description', e.target.value)}
                    placeholder="Enter class description"
                    error={errors.description}
                    disabled={processing}
                    textarea
                />

                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <SelectInput
                        id="category_id"
                        label="Category"
                        placeholder="Select a category"
                        value={data.category_id}
                        onChange={(value) => setData('category_id', value)}
                        error={errors.category_id}
                        disabled={processing}
                        options={categories.map((category) => ({
                            value: category.id.toString(),
                            label: category.name,
                        }))}
                    />
                </div>

                <div>
                    <label className="mb-1.5 block text-sm font-medium">
                        Course Thumbnail
                    </label>
                    <ImageDropzone
                        id="thumbnail"
                        value={
                            data.thumbnail
                                ? URL.createObjectURL(data.thumbnail)
                                : classroom.data.thumbnail || ''
                        }
                        onChange={(file) => setData('thumbnail', file)}
                        disabled={processing}
                    />
                    {errors.thumbnail && (
                        <p className="mt-1 text-sm text-destructive">
                            {errors.thumbnail}
                        </p>
                    )}
                </div>

                <div className="flex items-center justify-end gap-4 border-t pt-6">
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => window.history.back()}
                        disabled={processing}
                    >
                        Cancel
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? 'Saving...' : 'Save Changes'}
                    </Button>
                </div>
            </form>
        </div>
    );
};

export default EditClassroomForm;
