import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { cn } from '@/lib/utils';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { Link } from '@inertiajs/react';
import { ArrowLeft, Lightbulb, Trophy } from 'lucide-react';

export type QuizResult = {
    final_score: number;
    correct_answers_count: number;
    total_questions: number;
    time_spent_seconds: number;
    accuracy: number;
    incorrect_answers_count: number;
};

interface QuizResultsViewProps {
    result: QuizResult;
    quiz: TContentQuiz;
}

const quotesAccuracy = [
    { threshold: 90, quote: 'Excellent work! You are a true champion!' },
    { threshold: 75, quote: 'Great job! You have a solid understanding!' },
    { threshold: 50, quote: "Good effort! Let's review and try again!" },
    { threshold: 0, quote: "Don't give up! Every mistake is a lesson learned." },
];

const getQuoteByAccuracy = (accuracy: number) => {
    for (const { threshold, quote } of quotesAccuracy) {
        if (accuracy >= threshold) return quote;
    }
    return 'Keep trying! You can improve!';
};

// Helper warna yang lebih kaya, mencakup warna kartu
const getThemeByAccuracy = (accuracy: number) => {
    if (accuracy >= 70)
        return {
            cardBg: 'bg-green-100',
            cardBorder: 'border-green-200',
            text: 'text-green-900',
            accent: 'text-green-700',
            progress: '[&>div]:bg-green-500',
            trophy: 'text-yellow-400',
        };
    if (accuracy >= 50)
        return {
            cardBg: 'bg-sky-100',
            cardBorder: 'border-sky-200',
            text: 'text-sky-900',
            accent: 'text-sky-700',
            progress: '[&>div]:bg-sky-500',
            trophy: 'text-gray-400',
        };
    if (accuracy >= 35)
        return {
            cardBg: 'bg-amber-100',
            cardBorder: 'border-amber-200',
            text: 'text-amber-900',
            accent: 'text-amber-700',
            progress: '[&>div]:bg-amber-500',
            trophy: 'text-amber-700',
        };
    return {
        cardBg: 'bg-red-100',
        cardBorder: 'border-red-200',
        text: 'text-red-900',
        accent: 'text-red-700',
        progress: '[&>div]:bg-red-500',
        trophy: 'text-amber-800',
    };
};

export const QuizResultsView = ({ result, quiz }: QuizResultsViewProps) => {
    const theme = getThemeByAccuracy(result.accuracy);

    const formatTime = (totalSeconds: number) => {
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    };

    return (
        <div className="flex min-h-screen flex-col items-center bg-slate-50 p-4 sm:p-6 lg:py-12">
            <div className="mx-auto w-full max-w-2xl text-center">
                <div className="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-white shadow-lg">
                    <Trophy className={cn('h-12 w-12', theme.trophy)} />
                </div>
                <h1 className="text-4xl font-extrabold text-slate-800">
                    Quiz Completed!
                </h1>
                <p className={cn('mt-2 text-lg font-medium', theme.accent)}>
                    {getQuoteByAccuracy(result.accuracy)}
                </p>

                <div className="my-8 flex items-center justify-center gap-3">
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-xl font-bold text-slate-600">
                        U
                    </div>
                    <div>
                        <p className="text-left font-semibold text-slate-700">
                            User
                        </p>
                        <p className="text-left text-sm text-slate-500">
                            {quiz.classroom.name} • {quiz.title}
                        </p>
                    </div>
                </div>

                {/* KARTU HASIL DENGAN WARNA-WARNI */}
                <div className="mb-8 grid grid-cols-1 gap-4 text-center sm:grid-cols-3">
                    <Card
                        className={cn(
                            'border-2 shadow-lg',
                            theme.cardBg,
                            theme.cardBorder,
                        )}
                    >
                        <CardContent className="p-4">
                            <p className={cn('text-sm', theme.text)}>Final Score</p>
                            <p className={cn('text-3xl font-bold', theme.accent)}>
                                {result.final_score.toFixed(0)}%
                            </p>
                        </CardContent>
                    </Card>
                    <Card
                        className={cn(
                            'border-2 shadow-lg',
                            theme.cardBg,
                            theme.cardBorder,
                        )}
                    >
                        <CardContent className="p-4">
                            <p className={cn('text-sm', theme.text)}>
                                Correct Answers
                            </p>
                            <p className={cn('text-3xl font-bold', theme.accent)}>
                                {result.correct_answers_count}/
                                {result.total_questions}
                            </p>
                        </CardContent>
                    </Card>
                    <Card className="border-2 border-indigo-200 bg-indigo-100 shadow-lg">
                        <CardContent className="p-4">
                            <p className="text-sm text-indigo-800">Time Spent</p>
                            <p className="text-3xl font-bold text-indigo-700">
                                {formatTime(result.time_spent_seconds)}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <Card className="bg-white text-left shadow-lg">
                    <CardHeader>
                        <CardTitle>Performance Breakdown</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="mb-1 flex items-center justify-between text-sm text-slate-500">
                            <span>Accuracy</span>
                            <span>{result.accuracy.toFixed(1)}%</span>
                        </div>
                        <Progress
                            value={result.accuracy}
                            className={cn('h-2 bg-slate-200', theme.progress)}
                        />
                        <div className="mt-4 grid grid-cols-2 gap-4">
                            <div className="rounded-lg border bg-red-50 p-4">
                                <p className="text-sm text-red-700">Incorrect</p>
                                <p className="text-2xl font-bold text-red-800">
                                    {result.incorrect_answers_count}
                                </p>
                            </div>
                            <div className="rounded-lg border bg-green-50 p-4">
                                <p className="text-sm text-green-700">Accuracy</p>
                                <p className="text-2xl font-bold text-green-800">
                                    {result.accuracy.toFixed(1)}%
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div
                    className={cn(
                        'mt-6 mb-8 flex items-center justify-center gap-3 rounded-xl p-4 shadow-inner',
                        theme.cardBg,
                        theme.text,
                    )}
                >
                    <Lightbulb className="h-6 w-6" />
                    <p className="font-medium">
                        {getQuoteByAccuracy(result.accuracy)}
                    </p>
                </div>

                <div className="space-y-3">
                    <Button
                        asChild
                        size="lg"
                        className="w-full bg-amber-500 font-bold text-black shadow-md hover:bg-amber-600"
                    >
                        <Link href={route('student.quizzes.review', quiz.id)}>
                            Review Answers
                        </Link>
                    </Button>
                    <Button
                        asChild
                        size="lg"
                        variant="link"
                        className="w-full text-slate-500 hover:text-slate-700"
                    >
                        <Link
                            href={route(
                                'student.classrooms.show',
                                quiz.classroom_id,
                            )}
                        >
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Classroom
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
    );
};
