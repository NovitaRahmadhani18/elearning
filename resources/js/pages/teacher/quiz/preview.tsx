import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { cn } from '@/lib/utils';
import { SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import {
    ArrowLeft,
    BookCopy,
    Clock,
    Edit,
    Eye,
    Lightbulb,
    RotateCcw,
    Trophy,
} from 'lucide-react';
import { useState } from 'react';
import { TContentQuiz } from '../material/types';

interface QuizPreviewPageProps extends SharedData {
    quiz: {
        data: TContentQuiz;
    };
}

// Theme colors untuk jawaban - sama persis dengan tampilan siswa
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

const QuizPreview = () => {
    const { quiz } = usePage<QuizPreviewPageProps>().props;
    const quizData = quiz.data;
    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
    const [selectedAnswerId, setSelectedAnswerId] = useState<number | null>(null);
    const [selectedAnswers, setSelectedAnswers] = useState<Record<number, number>>(
        {},
    );
    const [isCompleted, setIsCompleted] = useState(false);

    // Mock timer untuk preview (tidak akan berkurang)
    const formattedTime = `${quizData.details.duration_minutes}:00`;

    const currentQuestion = quizData.details.questions[currentQuestionIndex];
    const progressPercentage =
        (currentQuestionIndex / quizData.details.questions.length) * 100;
    const numAnswers = currentQuestion?.answers?.length || 0;
    const responsiveGridClass =
        gridColsByAnswerCount[numAnswers] || 'md:grid-cols-2';

    // Handler untuk answer select - READ ONLY, hanya visual feedback, tidak ada penyimpanan
    const handleAnswerClick = (answerId: number) => {
        // Set visual feedback
        setSelectedAnswerId(answerId);

        // Simpan jawaban yang dipilih (untuk tracking saja, tidak disimpan ke database)
        setSelectedAnswers((prev) => ({
            ...prev,
            [currentQuestion.id]: answerId,
        }));

        // Auto advance ke pertanyaan berikutnya setelah delay untuk visual feedback

        if (currentQuestionIndex < quizData.details.questions.length - 1) {
            setCurrentQuestionIndex((prev) => prev + 1);
            setSelectedAnswerId(null); // Reset selection untuk pertanyaan berikutnya
        } else {
            // Quiz selesai, tampilkan result
            setIsCompleted(true);
        }
    };

    const handleRestart = () => {
        setCurrentQuestionIndex(0);
        setSelectedAnswers({});
        setSelectedAnswerId(null);
        setIsCompleted(false);
    };

    // Calculate mock results (preview only)
    const calculateResults = () => {
        let correctCount = 0;
        quizData.details.questions.forEach((question) => {
            const selectedAnswerId = selectedAnswers[question.id];
            const correctAnswer = question.answers.find((a) => a.is_correct);
            if (selectedAnswerId === correctAnswer?.id) {
                correctCount++;
            }
        });

        const totalQuestions = quizData.details.questions.length;
        const accuracy = (correctCount / totalQuestions) * 100;
        const finalScore = Math.round(accuracy);

        return {
            final_score: finalScore,
            correct_answers_count: correctCount,
            total_questions: totalQuestions,
            time_spent_seconds: 0, // Mock untuk preview
            accuracy,
            incorrect_answers_count: totalQuestions - correctCount,
        };
    };

    // Quotes dan theme sama persis dengan student view
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

    // Helper warna yang lebih kaya, sama persis dengan student view
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

    const formatTime = (totalSeconds: number) => {
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    };

    if (!currentQuestion && !isCompleted) {
        return (
            <div className="flex min-h-screen items-center justify-center bg-sky-50 text-slate-800">
                <div className="text-center">
                    <p className="text-lg">Loading quiz preview...</p>
                </div>
            </div>
        );
    }

    // Tampilkan hasil jika quiz sudah selesai
    if (isCompleted) {
        const result = calculateResults();
        const theme = getThemeByAccuracy(result.accuracy);

        return (
            <main>
                <Head title={`Quiz Preview Result: ${quizData.title}`} />

                {/* Preview Mode Banner - di atas semua */}
                <div className="border-b-2 border-purple-200 bg-purple-100 px-4 py-3">
                    <div className="mx-auto max-w-2xl">
                        <div className="flex items-center gap-3">
                            <Eye className="h-5 w-5 text-purple-600" />
                            <div>
                                <p className="text-sm font-semibold text-purple-900">
                                    PREVIEW MODE
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

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
                                T
                            </div>
                            <div>
                                <p className="text-left font-semibold text-slate-700">
                                    Teacher (Preview)
                                </p>
                                <p className="text-left text-sm text-slate-500">
                                    {quizData.classroom.name} • {quizData.title}
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
                                    <p className={cn('text-sm', theme.text)}>
                                        Final Score
                                    </p>
                                    <p
                                        className={cn(
                                            'text-3xl font-bold',
                                            theme.accent,
                                        )}
                                    >
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
                                    <p
                                        className={cn(
                                            'text-3xl font-bold',
                                            theme.accent,
                                        )}
                                    >
                                        {result.correct_answers_count}/
                                        {result.total_questions}
                                    </p>
                                </CardContent>
                            </Card>
                            <Card className="border-2 border-indigo-200 bg-indigo-100 shadow-lg">
                                <CardContent className="p-4">
                                    <p className="text-sm text-indigo-800">
                                        Time Spent
                                    </p>
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
                                    className={cn(
                                        'h-2 bg-slate-200',
                                        theme.progress,
                                    )}
                                />
                                <div className="mt-4 grid grid-cols-2 gap-4">
                                    <div className="rounded-lg border bg-red-50 p-4">
                                        <p className="text-sm text-red-700">
                                            Incorrect
                                        </p>
                                        <p className="text-2xl font-bold text-red-800">
                                            {result.incorrect_answers_count}
                                        </p>
                                    </div>
                                    <div className="rounded-lg border bg-green-50 p-4">
                                        <p className="text-sm text-green-700">
                                            Accuracy
                                        </p>
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
                                onClick={handleRestart}
                                size="lg"
                                className="w-full bg-amber-500 font-bold text-black shadow-md hover:bg-amber-600"
                            >
                                <RotateCcw className="mr-2 h-4 w-4" />
                                Preview Again
                            </Button>
                            <Button
                                asChild
                                size="lg"
                                className="w-full bg-amber-500 font-bold text-black shadow-md hover:bg-amber-600"
                            >
                                <Link
                                    href={route('teacher.quizzes.edit', quizData.id)}
                                >
                                    <Edit className="mr-2 h-4 w-4" />
                                    Edit Quiz
                                </Link>
                            </Button>
                            <Button
                                asChild
                                size="lg"
                                variant="link"
                                className="w-full text-slate-500 hover:text-slate-700"
                            >
                                <Link
                                    href={route('teacher.quizzes.show', quizData.id)}
                                >
                                    <ArrowLeft className="mr-2 h-4 w-4" />
                                    Back to Quiz Details
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </main>
        );
    }

    return (
        <main>
            <Head title={`Quiz Preview: ${quizData.title}`} />

            <div className="flex min-h-screen flex-col bg-slate-50 p-4 text-slate-800 sm:p-6 lg:p-8">
                {/* Preview Mode Banner - Prominent */}
                <div className="mb-4 rounded-lg border-2 border-purple-300 bg-purple-50 p-3 shadow-md">
                    <div className="flex items-center gap-3">
                        <Eye className="h-5 w-5 flex-shrink-0 text-purple-600" />
                        <div>
                            <span className="font-bold text-purple-900">
                                PREVIEW MODE
                            </span>
                        </div>
                    </div>
                </div>

                {/* Header - sama persis dengan tampilan siswa */}
                <header className="mb-6 flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm">
                            <BookCopy className="h-6 w-6 text-slate-600" />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900">
                                {quizData.title}
                            </h1>
                            <p className="text-sm text-slate-600">
                                {quizData.classroom.name}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center gap-2 rounded-lg bg-amber-400 px-3 py-1.5 font-bold text-amber-900 shadow-sm">
                        <Clock className="h-5 w-5" />
                        <span>{formattedTime}</span>
                    </div>
                </header>

                {/* Progress bar - sama persis dengan tampilan siswa */}
                <div className="mb-8">
                    <div className="mb-2 flex justify-between text-sm font-medium text-slate-600">
                        <span>
                            Question {currentQuestionIndex + 1} of{' '}
                            {quizData.details.questions.length}
                        </span>
                        <span>{Math.floor(progressPercentage)}% Complete</span>
                    </div>
                    <Progress value={progressPercentage} />
                </div>

                {/* Main content - sama persis dengan tampilan siswa */}
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

                    {/* Answers grid - sama persis dengan tampilan siswa */}
                    <div
                        className={cn(
                            'grid w-full grid-cols-1 gap-1',
                            responsiveGridClass,
                        )}
                    >
                        {currentQuestion.answers.map((answer, index) => {
                            const theme = pastelThemes[index % pastelThemes.length];
                            const isSelected = selectedAnswerId === answer.id;

                            return (
                                <button
                                    key={answer.id}
                                    onClick={() => handleAnswerClick(answer.id)}
                                    className={cn(
                                        'group flex h-full flex-col rounded-xl border-2 p-4 text-left shadow-sm transition-all duration-200',
                                        theme.bg,
                                        theme.border,
                                        theme.hoverBg,
                                        theme.hoverBorder,
                                        theme.ring,
                                        'focus:ring-4 focus:ring-offset-2 focus:ring-offset-slate-50 focus:outline-none',
                                        isSelected && 'ring-4 ring-offset-2',
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
        </main>
    );
};

export default QuizPreview;
