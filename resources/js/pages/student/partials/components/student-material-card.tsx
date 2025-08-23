import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { TContent } from '@/pages/teacher/material/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Eye, Lock, Play, Star } from 'lucide-react';
import ContentBadge from './content-badge';

const StudentMaterialCard = ({ content }: { content: TContent }) => {
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
                    isLocked ? 'bg-slate-100' : 'bg-blue-50',
                )}
            >
                <div
                    className={cn(
                        'flex flex-col items-center justify-center gap-1',
                        isLocked ? 'text-slate-400' : 'text-blue-800',
                    )}
                >
                    <BookOpen
                        className={cn(
                            'h-8 w-8',
                            isLocked ? 'text-slate-400' : 'text-blue-600',
                        )}
                    />
                    <span className="font-semibold">Material</span>
                </div>
                <ContentBadge
                    className="absolute top-3 right-3"
                    status={content.status}
                />
            </div>
            <div className="flex items-center justify-between p-4">
                <div>
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
                            'mt-1 mb-8 flex items-center gap-1 text-sm',
                            isLocked ? 'text-slate-400' : 'text-yellow-600',
                        )}
                    >
                        <Star className="h-4 w-4" />
                        <span>{content.points} points</span>
                    </div>
                </div>
                <Button
                    asChild
                    className={cn(
                        'self-end',
                        isLocked
                            ? 'cursor-not-allowed bg-slate-200 text-slate-500'
                            : 'bg-secondary text-white',
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
                              ? 'View Again'
                              : 'Start Reading'}
                    </Link>
                </Button>
            </div>
        </div>
    );
};

export default StudentMaterialCard;
