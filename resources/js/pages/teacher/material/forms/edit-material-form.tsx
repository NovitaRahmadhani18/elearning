import { FormField } from '@/components/form/form-field';
import { SelectInput } from '@/components/form/select-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { FileInput } from '@/components/ui/file-input';
import { Label } from '@/components/ui/label';
import { RichTextEditor } from '@/components/ui/rich-text-editor';
import { useForm, usePage } from '@inertiajs/react'; // Tambahkan Link
import { Trash2 } from 'lucide-react';
import { toast } from 'sonner';
import { EditMaterialPageProps } from '../types'; // Tipe untuk halaman edit

export function EditMaterialForm() {
    const { material, classrooms } = usePage<EditMaterialPageProps>().props;

    const { data, setData, post, processing, errors, reset } = useForm({
        _method: 'PUT', // Penting untuk update
        title: material.data.title,
        points: material.data.points,
        body: material.data.details.body,
        attachment: null as File | null,
        remove_attachment: false, // Flag untuk menghapus lampiran
    });

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('teacher.materials.update', material.data.id), {
            onSuccess: () => {
                toast.success('Material updated successfully!');
            },
            onError: (errors) => {
                console.error('Form submission errors:', errors);
                toast.error('Failed to update material. Please check the form.');
            },
            preserveState: true,
        });
    };

    // Fungsi untuk menandai penghapusan attachment
    const handleRemoveAttachment = () => {
        setData((prevData) => ({
            ...prevData,
            attachment: null, // Hapus file baru jika ada
            remove_attachment: true, // Set flag hapus
        }));
    };

    return (
        <div className="mx-auto max-w-2xl rounded-xl border bg-white p-8 shadow-md">
            <h1 className="mb-6 text-2xl font-bold">Edit Material</h1>
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
                        value={material.data.classroom_id.toString()}
                        onChange={(value) => {}}
                        options={classrooms.data.map((classroom) => ({
                            value: classroom.id.toString(),
                            label: classroom.fullName,
                        }))}
                        disabled={true} // Classroom tidak bisa diubah saat edit
                    />
                    <FormField
                        id="points"
                        label="Points"
                        type="number"
                        value={data.points?.toString()}
                        onChange={(e) =>
                            setData('points', parseInt(e.target.value) || 0)
                        }
                        placeholder="Enter points for this material"
                        error={errors.points}
                        disabled={processing}
                        required
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="attachment">Attachment</Label>
                    {/* Tampilkan lampiran yang ada & tombol hapus */}
                    {material.data.details.attachment_path &&
                        !data.remove_attachment && (
                            <div className="flex items-center justify-between rounded-md bg-muted p-2 text-sm">
                                <a
                                    href={material.data.details.attachment_path}
                                    target="_blank"
                                    className="truncate text-primary hover:underline"
                                >
                                    {material.data.details.attachment_path
                                        .split('/')
                                        .pop()}
                                </a>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    className="h-6 w-6"
                                    onClick={handleRemoveAttachment}
                                >
                                    <Trash2 className="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        )}

                    {/* Tampilkan input file jika tidak ada lampiran atau jika lampiran sudah dihapus */}
                    {(!material.data.details.attachment_path ||
                        data.remove_attachment) && (
                        <>
                            <FileInput
                                onChange={(file: File) =>
                                    setData('attachment', file)
                                }
                            />

                            <p className="text-xs text-gray-500">
                                Max file size: 10MB. Allowed formats: pdf, docx,
                                pptx, jpg, png, zip.
                            </p>
                        </>
                    )}
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
                        Reset
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? 'Saving...' : 'Save Changes'}
                    </Button>
                </div>
            </form>
        </div>
    );
}
