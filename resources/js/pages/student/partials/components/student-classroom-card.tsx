import { ActionButton } from '@/components/action-button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Progress } from '@/components/ui/progress';
import { getRandomCardAppearance } from '@/lib/classroomCardUtils';
import { cn } from '@/lib/utils';
import { TClassroom } from '@/pages/admin/classroom/types';
import { router } from '@inertiajs/react';
import { Play } from 'lucide-react';

interface StudentClassroomCardProps extends React.HTMLAttributes<HTMLDivElement> {
    classroom: TClassroom;
    progress: number; // Optional progress prop
    [key: string]: any; // Allow any additional props
}

const StudentClassroomCard: React.FC<StudentClassroomCardProps> = ({
    className,
    progress = 0, // Default progress to 0 if not provided
    classroom,
}) => {
    const { color, Icon } = getRandomCardAppearance(classroom.id);

    const teacherInitial = classroom.teacher.name.charAt(0).toUpperCase();

    const contentCount = classroom.contents?.length || 0;
    const quizCount =
        classroom.contents?.filter((content) => content.type === 'quiz').length || 0;

    const materialCount =
        classroom.contents?.filter((content) => content.type === 'material')
            .length || 0;

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
                <div className="absolute top-2 right-2 rounded-full bg-black/50 px-2 py-1 text-xs font-semibold text-white">
                    {progress}% Complete
                </div>
            </div>

            {/* Card Content */}
            <div className="flex flex-grow flex-col p-4">
                <h3 className="truncate text-lg font-bold">{classroom.fullName}</h3>
                <p className="h-10 text-sm text-gray-500">
                    {classroom.description || 'No description available.'}
                </p>

                <div className="mb-4 flex items-center gap-2">
                    <Avatar className="h-8 w-8 border border-primary">
                        <AvatarImage src={classroom.teacher.avatar} />
                        <AvatarFallback className="bg-primary-light text-primary">
                            {teacherInitial}
                        </AvatarFallback>
                    </Avatar>
                    <div>
                        <p className="text-sm font-semibold">
                            {classroom.teacher.name}
                        </p>
                        <p className="text-xs text-gray-500">Teacher</p>
                    </div>
                </div>

                <div className="mb-4 flex flex-col gap-2">
                    <div className="flex items-center justify-between">
                        <span className="text-sm font-semibold text-gray-600">
                            Progress
                        </span>

                        <span
                            className={cn(
                                'text-sm font-semibold',
                                progress >= 100 ? 'text-green-600' : 'text-gray-600',
                            )}
                        >
                            {progress}% Complete
                        </span>
                    </div>
                    <Progress value={progress} />
                    {progress >= 100 ? (
                        <p className="text-xs text-green-600">
                            Congratulations! You have completed this classroom.
                        </p>
                    ) : (
                        <p className="text-xs text-gray-500">
                            Keep going! You are making great progress.
                        </p>
                    )}
                </div>

                {/* Stats Section */}
                <div className="my-2 grid grid-cols-3 gap-2 text-center text-sm text-gray-600">
                    <div className="rounded-lg bg-primary-light/20 p-2">
                        <p className="font-bold">{contentCount}</p>
                        <p>Contents</p>
                    </div>
                    <div className="rounded-lg bg-primary-light/20 p-2">
                        <p className="font-bold">{quizCount}</p>
                        <p>Quizzes</p>
                    </div>
                    <div className="rounded-lg bg-primary-light/20 p-2">
                        <p className="font-bold">{materialCount}</p>
                        <p>Materials</p>
                    </div>
                </div>

                {/* Spacer to push buttons to the bottom */}
                <div className="flex-grow">
                    <ActionButton
                        action="create"
                        className="h-10 w-full text-sm font-semibold"
                        icon={<Play className="h-4 w-4" />}
                        onClick={() =>
                            router.visit(
                                route('student.classrooms.show', classroom.id),
                            )
                        }
                    >
                        Review Classroom
                    </ActionButton>
                </div>

                {/* Action Buttons */}
            </div>
        </div>
    );
};

export default StudentClassroomCard;
