import { FormField } from '@/components/form/form-field';
import { SelectInput } from '@/components/form/select-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { FileInput } from '@/components/ui/file-input';
import { Label } from '@/components/ui/label';
import { RichTextEditor } from '@/components/ui/rich-text-editor';
import { useForm, usePage } from '@inertiajs/react';
import { toast } from 'sonner';
import { CreateMaterialPageProps } from '../types';

export function CreateMaterialForm() {
    const { classrooms } = usePage<CreateMaterialPageProps>().props;

    const { data, setData, post, processing, errors, reset } = useForm({
        title: '',
        classroom_id: '',
        points: 100,
        body: '',
        attachment: null as File | null,
    });

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('teacher.materials.store'), {
            onSuccess: () => {
                toast.success('Material created successfully!');
            },
            onError: (errors) => {
                console.error('Form submission errors:', errors);
                toast.error(
                    'Failed to create material. Please check the form for errors.',
                );
            },
            preserveScroll: true,
            preserveState: true,
        });
    };

    return (
        <div className="mx-auto max-w-2xl rounded-xl border bg-white p-8 shadow-md">
            <h1 className="mb-6 text-2xl font-bold">Add New Material</h1>
            <form onSubmit={handleSubmit} className="space-y-6">
                <FormField
                    id="title"
                    label="Material Title"
                    value={data.title}
                    onChange={(e) => setData('title', e.target.value)}
                    placeholder="Enter material title"
                    error={errors.title}
                    disabled={processing}
                    required
                />

                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <SelectInput
                        id="classroom_id"
                        label="Classroom"
                        placeholder="Select a classroom"
                        value={data.classroom_id}
                        onChange={(value) => setData('classroom_id', value)}
                        options={classrooms.data.map((classroom) => ({
                            value: classroom.id.toString(),
                            label: classroom.fullName,
                        }))}
                        error={errors.classroom_id}
                        required
                    />
                    <FormField
                        id="points"
                        label="Points"
                        type="number"
                        value={data.points?.toString()}
                        onChange={(e) => setData('points', parseInt(e.target.value))}
                        placeholder="Enter points for this material"
                        error={errors.points}
                        disabled={processing}
                        required
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="attachment">Attachment</Label>
                    <FileInput
                        onChange={(file: File) => setData('attachment', file)}
                    />
                    <p className="text-xs text-gray-500">
                        Max file size: 10MB. Allowed formats: pdf, docx, pptx, jpg,
                        png, zip.
                    </p>

                    <InputError message={errors.attachment} />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="body">Body Content</Label>
                    <RichTextEditor
                        value={data.body}
                        onChange={(value) => setData('body', value)}
                    />
                    <InputError message={errors.body} />
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
                        {processing ? 'Creating...' : 'Create Material'}
                    </Button>
                </div>
            </form>
        </div>
    );
}
