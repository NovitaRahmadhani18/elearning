import { Progress } from '@/components/ui/progress';
import { cn } from '@/lib/utils';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { BookCopy, Clock, Loader2 } from 'lucide-react';
import { useState } from 'react';

interface QuizInProgressViewProps {
    quiz: TContentQuiz;
    currentQuestionIndex: number;
    onAnswerSelect: (questionId: number, answerId: number) => void;
    formattedTime: string;
    isSubmitting: boolean;
}

// BARU: Definisikan palet warna pastel kita
const pastelThemes = [
    {
        bg: 'bg-sky-500',
        border: 'border-sky-200',
        hoverBg: 'hover:bg-sky-100',
        hoverBorder: 'hover:border-sky-500',
        badgeBg: 'bg-sky-100',
        badgeText: 'text-sky-700',
        hoverBadgeBg: 'group-hover:bg-sky-500',
        hoverBadgeText: 'group-hover:text-white',
        ring: 'focus:ring-sky-200',
        textHover: 'group-hover:text-sky-900',
    },
    {
        bg: 'bg-emerald-500',
        border: 'border-emerald-200',
        hoverBg: 'hover:bg-emerald-100',
        hoverBorder: 'hover:border-emerald-500',
        badgeBg: 'bg-emerald-100',
        badgeText: 'text-emerald-700',
        hoverBadgeBg: 'group-hover:bg-emerald-500',
        hoverBadgeText: 'group-hover:text-white',
        ring: 'focus:ring-emerald-200',
        textHover: 'group-hover:text-emerald-900',
    },
    {
        bg: 'bg-amber-500',
        border: 'border-amber-200',
        hoverBg: 'hover:bg-amber-100',
        hoverBorder: 'hover:border-amber-500',
        badgeBg: 'bg-amber-100',
        badgeText: 'text-amber-700',
        hoverBadgeBg: 'group-hover:bg-amber-500',
        hoverBadgeText: 'group-hover:text-white',
        ring: 'focus:ring-amber-200',
        textHover: 'group-hover:text-amber-900',
    },
    {
        bg: 'bg-rose-500',
        border: 'border-rose-200',
        hoverBg: 'hover:bg-rose-100',
        hoverBorder: 'hover:border-rose-500',
        badgeBg: 'bg-rose-100',
        badgeText: 'text-rose-700',
        hoverBadgeBg: 'group-hover:bg-rose-500',
        hoverBadgeText: 'group-hover:text-white',
        ring: 'focus:ring-rose-200',
        textHover: 'group-hover:text-rose-900',
    },
    {
        bg: 'bg-violet-500',
        border: 'border-violet-200',
        hoverBg: 'hover:bg-violet-100',
        hoverBorder: 'hover:border-violet-500',
        badgeBg: 'bg-violet-100',
        badgeText: 'text-violet-700',
        hoverBadgeBg: 'group-hover:bg-violet-500',
        hoverBadgeText: 'group-hover:text-white',
        ring: 'focus:ring-violet-200',
        textHover: 'group-hover:text-violet-900',
    },
];

const gridColsByAnswerCount: Record<number, string> = {
    2: 'md:grid-cols-2',
    3: 'md:grid-cols-3',
    4: 'md:grid-cols-4',
    5: 'md:grid-cols-5',
};

export const QuizInProgressView = ({
    quiz,
    currentQuestionIndex,
    onAnswerSelect,
    formattedTime,
    isSubmitting,
}: QuizInProgressViewProps) => {
    const [selectedAnswerId, setSelectedAnswerId] = useState<number | null>(null);

    const currentQuestion = quiz.details.questions[currentQuestionIndex];
    if (!currentQuestion) {
        return (
            <div className="flex min-h-screen items-center justify-center bg-sky-50 text-slate-800">
                <Loader2 className="h-8 w-8 animate-spin" />
            </div>
        );
    }

    const progressPercentage =
        (currentQuestionIndex / quiz.details.questions.length) * 100;
    const numAnswers = currentQuestion.answers?.length || 0;
    const responsiveGridClass =
        gridColsByAnswerCount[numAnswers] || 'md:grid-cols-2';

    const handleSelectAndSubmit = (questionId: number, answerId: number) => {
        if (isSubmitting) return;
        setSelectedAnswerId(answerId);
        onAnswerSelect(questionId, answerId);
    };

    return (
        <div className="flex min-h-screen flex-col bg-slate-50 p-4 text-slate-800 sm:p-6 lg:p-8">
            <header className="mb-6 flex items-center justify-between">
                <div className="flex items-center gap-4">
                    <div className="flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm">
                        <BookCopy className="h-6 w-6 text-slate-600" />
                    </div>
                    <div>
                        <h1 className="text-xl font-bold text-slate-900">
                            {quiz.title}
                        </h1>
                        <p className="text-sm text-slate-600">
                            {quiz.classroom.name}
                        </p>
                    </div>
                </div>
                <div className="flex items-center gap-2 rounded-lg bg-amber-400 px-3 py-1.5 font-bold text-amber-900 shadow-sm">
                    <Clock className="h-5 w-5" />
                    <span>{formattedTime}</span>
                </div>
            </header>

            <div className="mb-8">
                <div className="mb-2 flex justify-between text-sm font-medium text-slate-600">
                    <span>
                        Question {currentQuestionIndex + 1} of{' '}
                        {quiz.details.questions.length}
                    </span>
                    <span>{Math.floor(progressPercentage)}% Complete</span>
                </div>
                <Progress value={progressPercentage} />
            </div>

            <main className="flex flex-grow flex-col items-center justify-center text-center">
                <h2 className="mb-8 text-3xl font-bold text-slate-900">
                    {currentQuestion.question_text}
                </h2>

                {currentQuestion.image_path && (
                    <img
                        src={currentQuestion.image_path}
                        alt={`Image for question: ${currentQuestion.question_text}`}
                        className="mb-8 max-h-64 w-full max-w-lg rounded-xl object-contain shadow-md"
                    />
                )}

                <div
                    className={cn(
                        'grid w-full grid-cols-1 gap-1',
                        responsiveGridClass,
                    )}
                >
                    {currentQuestion.answers.map((answer, index) => {
                        // Pilih tema warna secara berurutan dan berulang
                        const theme = pastelThemes[index % pastelThemes.length];

                        return (
                            <button
                                key={answer.id}
                                onClick={() =>
                                    handleSelectAndSubmit(
                                        currentQuestion.id,
                                        answer.id,
                                    )
                                }
                                disabled={isSubmitting}
                                className={cn(
                                    'group flex h-full flex-col rounded-xl border-2 p-4 text-left shadow-sm transition-all duration-200',
                                    theme.bg,
                                    theme.border,
                                    theme.hoverBg,
                                    theme.hoverBorder,
                                    theme.ring,
                                    'focus:ring-4 focus:ring-offset-2 focus:ring-offset-slate-50 focus:outline-none',
                                    'disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:border-inherit disabled:hover:bg-inherit',
                                )}
                            >
                                <div className="flex items-center">
                                    <div
                                        className={cn(
                                            'mr-4 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md font-bold transition-colors',
                                            theme.badgeBg,
                                            theme.badgeText,
                                            theme.hoverBadgeBg,
                                            theme.hoverBadgeText,
                                        )}
                                    >
                                        {String.fromCharCode(65 + index)}
                                    </div>
                                    <span
                                        className={cn(
                                            'font-semibold text-white',
                                            theme.textHover,
                                        )}
                                    >
                                        {answer.answer_text}
                                    </span>
                                    {isSubmitting &&
                                        selectedAnswerId === answer.id && (
                                            <Loader2 className="ml-auto h-5 w-5 animate-spin text-primary" />
                                        )}
                                </div>

                                {answer.image_path && (
                                    <div className="h-full">
                                        <img
                                            src={answer.image_path}
                                            alt={`Image for answer option ${index + 1}`}
                                            className="mt-4 w-full flex-1 rounded-lg object-cover"
                                        />
                                    </div>
                                )}
                            </button>
                        );
                    })}
                </div>
            </main>
        </div>
    );
};
