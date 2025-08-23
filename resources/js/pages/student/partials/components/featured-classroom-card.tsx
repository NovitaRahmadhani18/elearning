import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { getRandomCardAppearance } from '@/lib/classroomCardUtils';
import { cn } from '@/lib/utils';
import { TClassroom } from '@/pages/admin/classroom/types';
import { router } from '@inertiajs/react';
import { Play } from 'lucide-react';

interface FeaturedClassroomCardProps extends React.HTMLAttributes<HTMLDivElement> {
    classroom: TClassroom;
}

const FeaturedClassroomCard: React.FC<FeaturedClassroomCardProps> = ({
    classroom,
}) => {
    const { color, textColor } = getRandomCardAppearance(classroom.id);
    // const progressNumber = Math.round(Math.random() * 100 || 0);
    const progressNumber = 100;

    return (
        <div
            className={cn(
                color,
                'flex flex-row items-center justify-between gap-12 rounded-xl p-6 text-white shadow-sm',
            )}
        >
            <section className="flex flex-1 flex-col">
                <h1 className="mb-1 text-3xl font-bold" title={classroom.name}>
                    Continue Learning
                </h1>
                <p className="text-md mb-2 font-semibold">{classroom.fullName}</p>
                <p className="mb-4 text-sm">
                    {classroom.description || 'No description available.'}
                </p>

                <div className="mb-4 flex flex-col gap-2">
                    <div className="flex items-center justify-between">
                        <span className="text-sm font-semibold">Progress</span>

                        <span className={cn('text-sm font-semibold')}>
                            {progressNumber}% Complete
                        </span>
                    </div>
                    <Progress
                        value={progressNumber}
                        color="bg-white"
                        bgColor="bg-white/20"
                    />
                    {progressNumber >= 100 ? (
                        <p className="text-xs">
                            Congratulations! You have completed this classroom.
                        </p>
                    ) : (
                        <p className="text-xs">
                            Keep going! You are making great progress.
                        </p>
                    )}
                </div>

                <Button
                    className={cn(
                        'mt-2 w-fit rounded-lg bg-white p-6 font-semibold',
                        textColor,
                        'hover:bg-white/80',
                    )}
                    onClick={() =>
                        router.visit(route('student.classrooms.show', classroom.id))
                    }
                >
                    <Play className={cn('mr-2 h-4 w-4')} />
                    Continue to Classroom
                </Button>
            </section>
        </div>
    );
};

export default FeaturedClassroomCard;
