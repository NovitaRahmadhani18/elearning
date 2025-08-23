import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { Link } from '@inertiajs/react';
import { format } from 'date-fns';
import {
    Calendar,
    Clock,
    Eye,
    FileQuestion,
    HelpCircle,
    Lock,
    Play,
} from 'lucide-react';
import ContentBadge from './content-badge';

const StudentQuizCard = ({ content }: { content: TContentQuiz }) => {
    const now = new Date();
    const startTime = new Date(content.details.start_time);
    const endTime = new Date(content.details.end_time);
    const isExpired = endTime < now;
    // const isScheduled = startTime > now;
    // const isActive = !isExpired && !isScheduled;

    const isLocked = content.status === 'locked';
    const isCompleted = content.status === 'completed';

    return (
        <div
            className={cn(
                'overflow-hidden rounded-lg border bg-white shadow',
                isLocked && 'bg-slate-50',
            )}
        >
            <div
                className={cn(
                    'relative flex h-24 items-center justify-center p-3',
                    isLocked ? 'bg-slate-100' : 'bg-purple-50',
                )}
            >
                <div
                    className={cn(
                        'flex flex-col items-center justify-center gap-1',
                        isLocked ? 'text-slate-400' : 'text-purple-800',
                    )}
                >
                    <FileQuestion
                        className={cn(
                            'h-8 w-8',
                            isLocked ? 'text-slate-400' : 'text-purple-600',
                        )}
                    />
                    <span className="font-semibold">Quiz</span>
                </div>
                <ContentBadge
                    className="absolute top-3 right-3"
                    status={content.status}
                />
            </div>
            <div className="p-4">
                <h3
                    className={cn(
                        'text-lg font-bold',
                        isLocked ? 'text-slate-500' : 'text-slate-800',
                    )}
                >
                    {content.title}
                </h3>
                <div
                    className={cn(
                        'mt-2 space-y-1 text-sm',
                        isLocked ? 'text-slate-400' : 'text-slate-500',
                    )}
                >
                    <div className="flex items-center gap-2">
                        <Clock className="h-4 w-4" />
                        <span>Opens: {format(startTime, 'PPPP, HH:mm')}</span>
                    </div>
                    <div
                        className={cn(
                            'flex items-center gap-2',
                            isExpired && !isLocked && 'font-semibold text-red-600',
                        )}
                    >
                        <Calendar className="h-4 w-4" />
                        <span>
                            Due: {format(endTime, 'PPPP, HH:mm')}{' '}
                            {isExpired && '· Expired'}
                        </span>
                    </div>
                    <div className="flex items-center gap-2">
                        <HelpCircle className="h-4 w-4" />
                        <span>{content.details.questions.length} questions</span>
                    </div>
                </div>
                <div className="mt-4 flex items-center justify-end">
                    <Button
                        asChild
                        className={cn(
                            'self-end',
                            isLocked
                                ? 'cursor-not-allowed bg-slate-200 text-slate-500'
                                : 'bg-green-600 text-white',
                        )}
                        variant={'secondary'}
                        disabled={isLocked}
                    >
                        <Link
                            href={
                                isLocked
                                    ? '#'
                                    : route('student.contents.show', content.id)
                            }
                            as="div"
                        >
                            {isLocked ? (
                                <Lock className="h-5 w-5" />
                            ) : isCompleted ? (
                                <Eye className="h-5 w-5" />
                            ) : (
                                <Play className="h-5 w-5" />
                            )}
                            {isLocked
                                ? 'Locked'
                                : isCompleted
                                  ? 'View Results'
                                  : 'Start Quiz'}
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default StudentQuizCard;
