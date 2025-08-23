import { useForm, usePage } from '@inertiajs/react';
import React from 'react';
import { toast } from 'sonner';

import { FormField } from '@/components/form/form-field';
import { ImageDropzone } from '@/components/form/image-dropzone';
import { SelectInput } from '@/components/form/select-field';
import { Button } from '@/components/ui/button';
import { CreateClassroomPageProps } from '../types';

const CreateClassroomForm = () => {
    const { categories, teachers } = usePage<CreateClassroomPageProps>().props;

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        description: '',
        category_id: '',
        teacher_id: '',
        thumbnail: null as File | null,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('admin.classrooms.store'), {
            onSuccess: () => {
                toast.success('Class published successfully!');
                reset();
            },
            onError: (e) => {
                console.error(e);
                toast.error('Failed to publish class. Please check the form.');
            },
        });
    };

    return (
        <div className="mx-auto max-w-2xl rounded-xl border bg-white p-8">
            <h1 className="mb-6 text-2xl font-bold">Add New Classroom</h1>
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

                    <SelectInput
                        id="teacher_id"
                        label="Teacher"
                        placeholder="Select a teacher"
                        value={data.teacher_id.toString()}
                        onChange={(value) => setData('teacher_id', value)}
                        error={errors.teacher_id}
                        disabled={processing}
                        options={teachers.data.map((teacher) => ({
                            value: teacher.id.toString(),
                            label: teacher.name,
                        }))}
                    />
                </div>

                <div>
                    <label className="mb-1.5 block text-sm font-medium">
                        Course Thumbnail
                    </label>
                    <ImageDropzone
                        id="thumbnail"
                        value={data.thumbnail}
                        onChange={(file) => setData('thumbnail', file)}
                        disabled={processing}
                    />
                    {errors.thumbnail && (
                        <p className="text-destructive mt-1 text-sm">
                            {errors.thumbnail}
                        </p>
                    )}
                </div>

                <div className="flex items-center justify-end gap-4 border-t pt-6">
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => reset()}
                        disabled={processing}
                    >
                        Cancel
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? 'Publishing...' : 'Publish Classroom'}
                    </Button>
                </div>
            </form>
        </div>
    );
};

export default CreateClassroomForm;
