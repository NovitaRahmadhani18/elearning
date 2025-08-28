import StudentLayout from '@/layouts/student-layout';
import { cn } from '@/lib/utils';
import { SharedData } from '@/types'; // Pastikan path tipe benar
import { Head, Link, usePage } from '@inertiajs/react';
import { format, isFuture, parseISO } from 'date-fns';
import React from 'react';

// Import Ikon dari lucide-react
import { Button } from '@/components/ui/button';
import { TContentQuiz } from '@/pages/teacher/material/types';
import {
    AlertTriangle,
    ArrowLeft,
    Calendar,
    CheckCircle,
    ClipboardCheck,
    Clock,
    HelpCircle,
    Info,
    Lightbulb,
    Play,
    Timer,
} from 'lucide-react';

interface QuizShowPageProps extends SharedData {
    content: {
        data: TContentQuiz;
    };
}

const InfoCard = ({
    icon: Icon,
    label,
    value,
    iconBgColor,
    iconColor,
}: {
    icon: React.ElementType;
    label: string;
    value: string;
    iconBgColor: string;
    iconColor: string;
}) => (
    <div className="flex items-center rounded-lg bg-gray-50 p-4">
        <div
            className={cn(
                'mr-3 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full',
                iconBgColor,
            )}
        >
            <Icon className={cn('h-5 w-5', iconColor)} />
        </div>
        <div>
            <p className="text-sm text-gray-600">{label}</p>
            <p className="text-lg font-semibold text-gray-900">{value}</p>
        </div>
    </div>
);

// Sub-komponen untuk kotak pesan (Instructions & Important Notes)
const MessageBox = ({
    icon: Icon,
    title,
    children,
    color,
}: {
    icon: React.ElementType;
    title: string;
    children: React.ReactNode;
    color: 'blue' | 'yellow';
}) => {
    const variants = {
        blue: {
            border: 'border-blue-200',
            bg: 'bg-blue-50',
            titleText: 'text-blue-900',
            bodyText: 'text-blue-800',
        },
        yellow: {
            border: 'border-yellow-200',
            bg: 'bg-yellow-50',
            titleText: 'text-yellow-900',
            bodyText: 'text-yellow-800',
        },
    };
    const selectedVariant = variants[color];

    return (
        <div
            className={cn(
                'mb-6 rounded-lg border p-4',
                selectedVariant.border,
                selectedVariant.bg,
            )}
        >
            <h3
                className={cn(
                    'mb-2 flex items-center text-sm font-medium',
                    selectedVariant.titleText,
                )}
            >
                <Icon className="mr-1.5 h-4 w-4" />
                {title}
            </h3>
            <div className={cn('text-sm', selectedVariant.bodyText)}>{children}</div>
        </div>
    );
};

const QuizShowPage = () => {
    const { content } = usePage<QuizShowPageProps>().props;
    const quiz = content.data; // Untuk kemudahan membaca
    const classroom = quiz.classroom;

    const isFutureQuiz = isFuture(parseISO(quiz.details.start_time));

    return (
        <StudentLayout>
            <Head title={`Quiz: ${quiz.title}`} />
            <div className="mx-auto w-full max-w-4xl px-4 py-8">
                {/* Navigasi Kembali */}
                <div className="mb-6">
                    <Link
                        href={route('student.classrooms.show', classroom.id)}
                        className="inline-flex items-center text-sm text-gray-600 transition-colors hover:text-primary"
                    >
                        <ArrowLeft className="mr-1 h-4 w-4" />
                        Back to Classroom
                    </Link>
                </div>

                {/* Kartu Kuis Utama */}
                <div className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    {/* Header Kartu */}
                    <div className="relative h-32 overflow-hidden bg-gradient-to-br from-purple-50 to-purple-100">
                        <div className="bg-quiz-pattern absolute inset-0 opacity-10"></div>{' '}
                        {/* Asumsi Anda punya bg-quiz-pattern */}
                        <div className="relative flex h-full items-center justify-center">
                            <div className="text-center">
                                <ClipboardCheck className="mx-auto mb-2 h-12 w-12 text-purple-600" />
                                <div className="text-sm font-medium text-purple-700">
                                    Ready to Start Quiz
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Konten Kartu */}
                    <div className="p-6">
                        <h1 className="mb-4 text-2xl font-bold text-gray-900">
                            {quiz.title}
                        </h1>

                        <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <InfoCard
                                icon={HelpCircle}
                                label="Total Questions"
                                value={`${quiz.details.questions.length}`}
                                iconBgColor="bg-purple-100"
                                iconColor="text-purple-600"
                            />
                            <InfoCard
                                icon={Timer}
                                label="Duration"
                                value={`${quiz.details.duration_minutes} minutes`}
                                iconBgColor="bg-orange-100"
                                iconColor="text-orange-600"
                            />
                            <InfoCard
                                icon={Clock}
                                label="Opens At"
                                value={format(
                                    new Date(quiz.details.start_time),
                                    'EEEE, dd MMM yyyy, HH:mm',
                                )}
                                iconBgColor="bg-green-100"
                                iconColor="text-green-600"
                            />
                            <InfoCard
                                icon={Calendar}
                                label="Due Date"
                                value={format(
                                    new Date(quiz.details.end_time),
                                    'EEEE, dd MMM yyyy, HH:mm',
                                )}
                                iconBgColor="bg-red-100"
                                iconColor="text-red-600"
                            />
                        </div>

                        {quiz.description && (
                            <MessageBox
                                icon={Info}
                                title="Instructions"
                                color="blue"
                            >
                                <p>{quiz.description}</p>
                            </MessageBox>
                        )}

                        <MessageBox
                            icon={AlertTriangle}
                            title="Important Notes"
                            color="yellow"
                        >
                            <ul className="list-disc space-y-1 pl-5">
                                <li>
                                    Make sure you have a stable internet connection
                                </li>
                                <li>You cannot pause the quiz once started</li>
                                <li>Answer all questions before time runs out</li>
                                <li>You can only submit the quiz once</li>
                            </ul>
                        </MessageBox>

                        <div className="flex items-center justify-between">
                            <div className="text-sm text-gray-600">
                                <span className="flex items-center">
                                    <Clock className="mr-1.5 h-4 w-4" />
                                    Ready to begin when you are
                                </span>
                            </div>

                            <Button
                                disabled={isFutureQuiz}
                                asChild
                                size="lg"
                                className={cn(
                                    isFutureQuiz && 'cursor-not-allowed opacity-50',
                                )}
                            >
                                <Link
                                    href={
                                        isFutureQuiz
                                            ? '#'
                                            : route('student.quizzes.start', quiz.id)
                                    }
                                    className="flex items-center"
                                >
                                    <Play className="mr-2 h-5 w-5" />
                                    Start Quiz
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>

                {/* Tips */}
                <div className="mt-6 rounded-lg border border-gray-200 bg-white p-4">
                    <h3 className="mb-3 flex items-center text-sm font-medium text-gray-900">
                        <Lightbulb className="mr-1.5 h-4 w-4 text-yellow-500" />
                        Tips for Success
                    </h3>
                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div className="flex items-start">
                            <CheckCircle className="mt-0.5 mr-2 h-4 w-4 flex-shrink-0 text-green-500" />
                            <span className="text-sm text-gray-600">
                                Read each question carefully
                            </span>
                        </div>
                        <div className="flex items-start">
                            <CheckCircle className="mt-0.5 mr-2 h-4 w-4 flex-shrink-0 text-green-500" />
                            <span className="text-sm text-gray-600">
                                Manage your time wisely
                            </span>
                        </div>
                        <div className="flex items-start">
                            <CheckCircle className="mt-0.5 mr-2 h-4 w-4 flex-shrink-0 text-green-500" />
                            <span className="text-sm text-gray-600">
                                Review answers before submitting
                            </span>
                        </div>
                        <div className="flex items-start">
                            <CheckCircle className="mt-0.5 mr-2 h-4 w-4 flex-shrink-0 text-green-500" />
                            <span className="text-sm text-gray-600">
                                Stay calm and focused
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </StudentLayout>
    );
};

export default QuizShowPage;
