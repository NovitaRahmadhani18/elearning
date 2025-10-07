import Heading from '@/components/heading';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { cn } from '@/lib/utils';
import { TContentMaterial } from '@/pages/teacher/material/types';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, BookOpen, Download, Eye, Star } from 'lucide-react';

interface MaterialPreviewProps extends SharedData {
    material: {
        data: TContentMaterial;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Materials Management',
        href: '/teacher/materials',
    },
    {
        title: 'Material Preview',
        href: '#',
    },
];

const MaterialPreview = ({ material }: MaterialPreviewProps) => {
    const materialData = material.data;

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title={`Material Preview: ${materialData.title}`} />

            <div className="container mx-auto flex flex-col gap-6">
                {/* Preview Mode Banner - Prominent */}
                <div className="rounded-lg border-2 border-purple-300 bg-purple-50 p-3 shadow-md">
                    <div className="flex items-center gap-3">
                        <Eye className="h-5 w-5 flex-shrink-0 text-purple-600" />
                        <div>
                            <span className="font-bold text-purple-900">
                                PREVIEW MODE
                            </span>
                            <span className="ml-2 text-sm text-purple-700">
                                • This is how students will see this material • No
                                data is saved
                            </span>
                        </div>
                    </div>
                </div>

                <div className="container mx-auto overflow-clip rounded-lg bg-white shadow">
                    <div
                        className={cn(
                            'relative flex h-36 items-center justify-center p-3',
                            'bg-blue-50',
                        )}
                    >
                        <div
                            className={cn(
                                'flex flex-col items-center justify-center gap-1',
                                'text-blue-800',
                            )}
                        >
                            <BookOpen className={cn('h-16 w-16', 'text-blue-600')} />
                            <span className="font-semibold">Learning Material</span>
                        </div>
                        <span className="absolute top-3 right-3 inline-flex items-center gap-1 rounded-full bg-secondary px-2 py-1 font-semibold text-white">
                            <Star className="h-4 w-4" />
                            {materialData.points} Points
                        </span>
                    </div>

                    <div className="p-4">
                        <Heading
                            title={materialData.title}
                            description={
                                materialData.description ||
                                'No description available.'
                            }
                        />

                        {materialData.details.attachment_path && (
                            <div className="mt-2 flex flex-col gap-2">
                                <a
                                    href={materialData.details.attachment_path}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <Button variant="outline" className="w-fit">
                                        <Download className="mr-2 h-4 w-4" />
                                        Download Attachment
                                    </Button>
                                </a>
                            </div>
                        )}

                        <Separator className="my-4" />

                        <div className="prose max-w-none">
                            <div
                                dangerouslySetInnerHTML={{
                                    __html: materialData.details.body,
                                }}
                            />
                        </div>
                    </div>
                </div>

                {/* Info Alert - Preview Mode */}
                <Alert className="border-purple-200 bg-purple-50 text-purple-900">
                    <Eye className="h-4 w-4" />
                    <AlertTitle>Preview Mode Information</AlertTitle>
                    <AlertDescription className="text-purple-800">
                        This is how students will see this material. When students
                        view this material, they will automatically earn{' '}
                        {materialData.points} points and this will be marked as
                        completed.
                    </AlertDescription>
                </Alert>

                {/* Action Buttons */}
                <div className="flex gap-3">
                    <Button asChild variant="outline">
                        <Link
                            href={route('teacher.materials.edit', materialData.id)}
                        >
                            Edit Material
                        </Link>
                    </Button>
                    <Button asChild variant="outline">
                        <Link
                            href={route('teacher.materials.show', materialData.id)}
                        >
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Material Details
                        </Link>
                    </Button>
                </div>
            </div>
        </AdminTeacherLayout>
    );
};

export default MaterialPreview;
