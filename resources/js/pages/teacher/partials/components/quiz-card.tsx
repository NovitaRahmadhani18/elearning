import { cn } from '@/lib/utils';

// Ganti ikon FileText dan Paperclip dengan yang relevan untuk kuis
import {
    BookMarked,
    ClipboardList,
    Clock,
    Eye,
    Pencil,
    PlayCircle,
    Star,
    Trash2,
    Users,
} from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Link, router } from '@inertiajs/react';
import { intlFormatDistance } from 'date-fns';
import { toast } from 'sonner';
import { TContent, TContentQuiz } from '../../material/types';

interface QuizCardProps {
    content: TContent;
    routeName?: string;
    className?: string;
}

const QuizCard = ({
    content,
    className,
    routeName = 'teacher.quizzes', // Default route untuk kuis
}: QuizCardProps) => {
    // Tampilkan komponen hanya jika tipenya 'quiz'
    if (content.type !== 'quiz') {
        return null;
    }
    const quizContent = content as TContentQuiz;

    return (
        <div
            className={cn(
                'flex flex-col overflow-hidden rounded-xl bg-white shadow-lg',
                className,
            )}
        >
            {/* Card Header */}
            <div className="relative h-32 bg-secondary/30 text-secondary">
                <div className="flex h-full w-full flex-col items-center justify-center gap-2">
                    <ClipboardList className="h-12 w-12 opacity-70" />
                    <span className="text-sm text-amber-600">
                        {quizContent.details.questions.length} Questions
                    </span>
                </div>
                <QuizStatusBadge
                    startTime={quizContent.details.start_time}
                    dueTime={quizContent.details.end_time}
                    className="absolute top-2 left-2 rounded-full px-2 py-1 text-xs font-semibold"
                />
                <div className="absolute top-2 right-2 max-w-[50%] truncate rounded-full bg-secondary/40 px-2 py-1 text-xs font-semibold text-amber-800">
                    <ClipboardList className="mr-1 inline h-3 w-3" />
                    Quiz
                </div>
            </div>

            {/* Card Content */}
            <div className="flex flex-grow flex-col p-4">
                <h3 className="truncate text-lg font-bold" title={quizContent.title}>
                    {quizContent.title}
                </h3>

                <section className="mb-3 flex items-center gap-1">
                    <BookMarked className="h-3 w-3 text-gray-500" />
                    <p className="text-xs text-gray-400">
                        {quizContent.classroom.fullName}
                    </p>
                </section>

                <section className="mb-2 flex items-center gap-1">
                    <Clock className="h-3 w-3 text-gray-500" />
                    <p className="overflow-hidden text-xs text-gray-500">
                        {quizContent.details.duration_minutes} Min
                    </p>
                </section>

                <section className="mb-6 flex items-center gap-1">
                    <Clock className="h-3 w-3 text-gray-500" />
                    <p className="overflow-hidden text-xs text-gray-500">
                        Due{' '}
                        {intlFormatDistance(
                            new Date(quizContent.details.end_time),
                            new Date(),
                            {
                                style: 'long',
                            },
                        )}
                    </p>
                </section>

                {/* Stats Section - Disesuaikan untuk Kuis */}
                <div className="my-2 grid grid-cols-2 gap-2 text-center text-sm text-gray-600">
                    <div>
                        <p className="flex items-center justify-center gap-1 font-bold">
                            <Users className="h-4 w-4 text-primary" />0
                        </p>
                        <p>Submissions</p>
                    </div>
                    <div>
                        <p className="flex items-center justify-center gap-1 font-bold">
                            <Star className="h-4 w-4 text-primary" />
                            {quizContent.points || 0}
                        </p>
                        <p>Points</p>
                    </div>
                </div>

                <div className="flex-grow" />

                {/* Action Buttons - Logika tetap sama */}
                <div className="mt-4 grid grid-cols-3 gap-2 border-t pt-4">
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
                        className="text-xs"
                        size="sm"
                        onClick={() => {
                            router.delete(
                                route(routeName + '.destroy', content.id),
                                {
                                    preserveScroll: true,
                                    onBefore: () =>
                                        confirm(
                                            'Are you sure you want to delete this quiz?',
                                        ),
                                    onSuccess: () => {
                                        toast.success('Quiz deleted successfully');
                                    },
                                    onError: (error) => {
                                        toast.error('Failed to delete quiz');
                                        console.error('Delete quiz error:', error);
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

interface QuizStatusBadgeProps {
    startTime: string | null;
    dueTime: string | null;
    className?: string;
}

// Definisikan tampilan untuk setiap status
const statusConfig = {
    expired: {
        text: 'Expired',
        icon: <Clock className="mr-1 h-3 w-3" />,
        className: 'bg-red-100 text-red-800',
    },
    scheduled: {
        text: 'Scheduled',
        icon: <Clock className="mr-1 h-3 w-3" />,
        className: 'bg-yellow-100 text-yellow-800',
    },
    active: {
        text: 'Active',
        icon: <PlayCircle className="mr-1 h-3 w-3" />,
        className: 'bg-green-100 text-green-800',
    },
};

export function QuizStatusBadge({
    startTime,
    dueTime,
    className,
}: QuizStatusBadgeProps) {
    // Fungsi untuk menentukan status kuis saat ini
    const getStatus = (): keyof typeof statusConfig => {
        const now = new Date();
        const startDate = startTime ? new Date(startTime) : null;
        const dueDate = dueTime ? new Date(dueTime) : null;

        if (dueDate && dueDate < now) {
            return 'expired';
        }
        if (startDate && startDate > now) {
            return 'scheduled';
        }
        return 'active';
    };

    const currentStatus = getStatus();
    const { text, icon, className: statusClassName } = statusConfig[currentStatus];

    return (
        <span
            className={cn(
                'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                statusClassName,
                className,
            )}
        >
            {icon}
            {text}
        </span>
    );
}

export default QuizCard;
