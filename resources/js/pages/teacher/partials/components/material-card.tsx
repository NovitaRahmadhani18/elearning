import { cn } from '@/lib/utils';

import {
    BookMarked,
    Eye,
    FileText,
    HelpCircle,
    Pencil,
    PlayCircle,
    Star,
    Trash2,
} from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Link, router } from '@inertiajs/react';
import { toast } from 'sonner';
import { TContent, TContentMaterial } from '../../material/types';

interface MaterialCardProps {
    content: TContent;
    routeName?: string;
    className?: string;
}

const MaterialCard = ({
    content,
    className,
    routeName = 'teacher.materials',
}: MaterialCardProps) => {
    // Tampilkan komponen hanya jika tipenya 'material'
    if (content.type !== 'material') {
        return null;
    }
    const materialContent = content as TContentMaterial;

    const completionCount = materialContent?.students_count ?? 0;
    const totalStudents = materialContent.classroom?.students_count ?? 0;

    return (
        <div
            className={cn(
                'flex flex-col overflow-hidden rounded-xl bg-white shadow-lg',
                className,
            )}
        >
            {/* Card Header */}
            <div className="relative h-32 bg-secondary/30 text-secondary">
                <div className="flex h-full w-full items-center justify-center">
                    <FileText className="h-12 w-12 opacity-70" />
                </div>
                <div className="absolute top-2 right-2 max-w-[50%] truncate rounded-full bg-secondary/40 px-2 py-1 text-xs font-semibold text-amber-800">
                    <BookMarked className="mr-1 inline h-3 w-3" />
                    Material
                </div>
            </div>

            {/* Card Content */}
            <div className="flex flex-grow flex-col p-4">
                <h3
                    className="truncate text-lg font-bold"
                    title={materialContent.title}
                >
                    {materialContent.title}
                </h3>
                <section className="mb-3 flex items-center gap-1">
                    <BookMarked className="h-3 w-3 text-gray-500" />
                    <p className="text-xs text-gray-400">
                        {materialContent.classroom.fullName}
                    </p>
                </section>

                {/* Stats Section */}
                <div className="my-2 grid grid-cols-2 gap-2 text-center text-sm text-gray-600">
                    <div>
                        <p className="flex items-center justify-center gap-1 font-bold">
                            <HelpCircle className="h-4 w-4 text-primary" />
                            {completionCount} / {totalStudents}
                        </p>
                        <p>Completed</p>
                    </div>
                    <div>
                        <p className="flex items-center justify-center gap-1 font-bold">
                            <Star className="h-4 w-4 text-primary" />
                            {materialContent.points || 0}
                        </p>
                        <p>Points</p>
                    </div>
                </div>

                <div className="flex-grow" />

                {/* Action Buttons */}
                <div className="grid grid-cols-3 gap-2 pt-4">
                    <section className="col-span-3 flex justify-center">
                        <Button asChild className="w-full max-w-xs" size="sm">
                            <Link href={route(routeName + '.preview', content.id)}>
                                <PlayCircle className="h-4 w-4" />
                                Preview Material
                            </Link>
                        </Button>
                    </section>

                    <Link
                        href={route(routeName + '.show', content.id)}
                        className="w-full"
                    >
                        <Button size="sm" className="w-full text-xs">
                            <Eye className="h-4 w-4" />
                            View
                        </Button>
                    </Link>
                    <Link
                        href={route(routeName + '.edit', content.id)}
                        className="w-full"
                    >
                        <Button
                            variant="outline"
                            size="sm"
                            className="w-full bg-secondary text-xs text-white hover:bg-secondary/80"
                        >
                            <Pencil className="h-4 w-4" />
                            Edit
                        </Button>
                    </Link>
                    <Button
                        variant="destructive"
                        size="sm"
                        className="text-xs"
                        onClick={() => {
                            router.delete(
                                route(routeName + '.destroy', content.id),
                                {
                                    preserveScroll: true,
                                    onBefore: () =>
                                        confirm(
                                            'Are you sure you want to delete this material?',
                                        ),
                                    onSuccess: () => {
                                        toast.success(
                                            'Material deleted successfully',
                                        );
                                    },
                                    onError: (error) => {
                                        toast.error(
                                            error.response?.data?.message ||
                                                'Failed to delete material',
                                        );
                                        console.error(
                                            'Delete classroom error:',
                                            error,
                                        );
                                    },
                                },
                            );
                        }}
                    >
                        <Trash2 className="h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default MaterialCard;
