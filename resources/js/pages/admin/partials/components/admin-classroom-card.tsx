import { getRandomCardAppearance } from '@/lib/classroomCardUtils'; // Import utilitas kita
import { cn } from '@/lib/utils';

import { Eye, Pencil, Trash2 } from 'lucide-react';

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Link, router } from '@inertiajs/react';
import { toast } from 'sonner';
import { TClassroom } from '../../classroom/types';

interface AdminClassroomCardProps {
    classroom: TClassroom;
    routeName?: string;
    className?: string;
}

const AdminClassroomCard = ({
    classroom,
    className,
    routeName = 'admin.classrooms',
}: AdminClassroomCardProps) => {
    const { color, Icon } = getRandomCardAppearance(classroom.id);

    const studentCount = classroom.students?.length ?? 0;
    const contentCount = classroom.contents?.length ?? 0;
    const quizCount =
        classroom.contents?.filter((content) => content.type === 'quiz')?.length ??
        0;

    const materialCount = contentCount - quizCount;

    const teacherInitial = classroom.teacher.name.charAt(0).toUpperCase();

    return (
        <div
            className={cn(
                'flex flex-col overflow-hidden rounded-xl bg-white shadow-lg',
                className,
            )}
        >
            <div className="relative h-40">
                {classroom.thumbnail ? (
                    <img
                        src={classroom.thumbnail}
                        alt={classroom.name}
                        className="h-full w-full object-cover"
                    />
                ) : (
                    <div
                        className={cn(
                            'flex h-full w-full items-center justify-center text-white',
                            color,
                        )}
                    >
                        <Icon className="h-16 w-16" />
                    </div>
                )}
                <div className="absolute top-2 left-2 rounded-full bg-black/50 px-2 py-1 text-xs font-semibold text-white">
                    {classroom.category.name}
                </div>
                <div className="absolute top-2 right-2 rounded-full bg-black/50 px-2 py-1 text-xs font-semibold text-white">
                    {studentCount} student{studentCount !== 1 ? 's' : ''}
                </div>
            </div>

            {/* Card Content */}
            <div className="flex flex-grow flex-col p-4">
                <h3 className="truncate text-lg font-bold">{classroom.fullName}</h3>
                <p className="mb-3 h-10 text-sm text-gray-500">
                    {classroom.description || 'No description available.'}
                </p>

                <div className="mb-4 flex items-center gap-2">
                    <Avatar className="h-8 w-8">
                        <AvatarImage src={classroom.teacher.avatar} />
                        <AvatarFallback>{teacherInitial}</AvatarFallback>
                    </Avatar>
                    <div>
                        <p className="text-sm font-semibold">
                            {classroom.teacher.name}
                        </p>
                        <p className="text-xs text-gray-500">Teacher</p>
                    </div>
                </div>

                {/* Stats Section */}
                <div className="my-2 grid grid-cols-3 gap-2 text-center text-sm text-gray-600">
                    <div>
                        <p className="font-bold">{studentCount}</p>
                        <p>Students</p>
                    </div>
                    <div>
                        <p className="font-bold">{quizCount}</p>
                        <p>Quizzes</p>
                    </div>
                    <div>
                        <p className="font-bold">{materialCount}</p>
                        <p>Materials</p>
                    </div>
                </div>

                {/* Spacer to push buttons to the bottom */}
                <div className="flex-grow" />

                {/* Action Buttons */}
                <div className="grid grid-cols-3 gap-2 pt-4">
                    <Link
                        href={route(routeName + '.show', classroom.id)}
                        className="w-full"
                    >
                        <Button size="sm" className="w-full">
                            <Eye className="mr-1 h-4 w-4" />
                            View
                        </Button>
                    </Link>
                    <Link
                        href={route(routeName + '.edit', classroom.id)}
                        className="w-full"
                    >
                        <Button
                            variant="outline"
                            size="sm"
                            className="w-full bg-secondary text-white hover:bg-secondary/80"
                        >
                            <Pencil className="mr-1 h-4 w-4" />
                            Edit
                        </Button>
                    </Link>
                    <Button
                        variant="destructive"
                        size="sm"
                        onClick={() => {
                            router.delete(
                                route(routeName + '.destroy', classroom.id),
                                {
                                    preserveScroll: true,
                                    onBefore: () =>
                                        confirm(
                                            'Are you sure you want to delete this classroom?',
                                        ),
                                    onSuccess: () => {
                                        toast.success(
                                            'Classroom deleted successfully',
                                        );
                                    },
                                    onError: (error) => {
                                        toast.error(
                                            error.response?.data?.message ||
                                                'Failed to delete classroom',
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

export default AdminClassroomCard;
