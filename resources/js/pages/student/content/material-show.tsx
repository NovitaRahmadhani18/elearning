import Heading from '@/components/heading';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import StudentLayout from '@/layouts/student-layout';
import { cn } from '@/lib/utils';
import { TContentMaterial } from '@/pages/teacher/material/types';
import { SharedData } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, BookOpen, CheckCircle2Icon, Download, Star } from 'lucide-react';

interface MaterialShowProps extends SharedData {
    content: {
        data: TContentMaterial;
    };
}

const MaterialShow = ({ content }: MaterialShowProps) => {
    return (
        <StudentLayout>
            <Head title={content.data.title} />
            <div className="container mx-auto flex flex-col gap-6 p-8">
                <Link
                    href={route(
                        'student.classrooms.show',
                        content.data.classroom_id,
                    )}
                    className="inline-flex items-center gap-2 text-sm text-gray-700"
                >
                    <ArrowLeft className="" />
                    Back to classroom
                </Link>

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
                            {content.data.points} Points
                        </span>
                    </div>

                    <div className="p-4">
                        <Heading
                            title={content.data.title}
                            description={
                                content.data.description ||
                                'No description available.'
                            }
                        />

                        {content.data.details.attachment_path && (
                            <div className="mt-2 flex flex-col gap-2">
                                <a
                                    href={content.data.details.attachment_path}
                                    target="_blank"
                                >
                                    <Button
                                        asChild
                                        variant="outline"
                                        className="w-fit"
                                    >
                                        <span className="flex items-center gap-2">
                                            <Download className="h-4 w-4" />
                                            Download Attachment
                                        </span>
                                    </Button>
                                </a>
                            </div>
                        )}

                        <Separator className="my-4" />
                        <div className="prose max-w-none">
                            <div
                                dangerouslySetInnerHTML={{
                                    __html: content.data.details.body,
                                }}
                            />
                        </div>
                    </div>
                </div>

                <Alert className="bg-green-50 text-green-800">
                    <CheckCircle2Icon />
                    <AlertTitle>Material Completed!</AlertTitle>
                    <AlertDescription className="text-green-800">
                        You've earned {content.data.points} points for reading this
                        material. Great job on your learning journey!
                    </AlertDescription>
                </Alert>

                <Button asChild className="w-fit" variant="outline">
                    <Link
                        href={route(
                            'student.classrooms.show',
                            content.data.classroom_id,
                        )}
                    >
                        <ArrowLeft className="" />
                        Back to classroom
                    </Link>
                </Button>
            </div>
        </StudentLayout>
    );
};

export default MaterialShow;
