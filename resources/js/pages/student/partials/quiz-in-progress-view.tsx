import { Progress } from '@/components/ui/progress';
import { cn } from '@/lib/utils';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { BookCopy, Clock } from 'lucide-react';

interface QuizInProgressViewProps {
    quiz: TContentQuiz;
    currentQuestionIndex: number;
    onAnswerSelect: (questionId: number, answerId: number) => void;
    formattedTime: string;
    isSubmitting: boolean; // Tambahkan prop ini dari komponen utama
}

// Pindahkan map di luar komponen agar tidak dibuat ulang pada setiap render
const gridColsByAnswerCount: Record<number, string> = {
    2: 'md:grid-cols-2',
    3: 'md:grid-cols-3',
    4: 'md:grid-cols-2', // Menghasilkan 2x2 grid yang rapi
    5: 'md:grid-cols-3', // Menghasilkan 3 di atas, 2 di bawah
};

export const QuizInProgressView = ({
    quiz,
    currentQuestionIndex,
    onAnswerSelect,
    formattedTime,
    isSubmitting, // Terima prop isSubmitting
}: QuizInProgressViewProps) => {
    // Ambil pertanyaan saat ini, pastikan tidak error jika index di luar batas
    const currentQuestion = quiz.details.questions[currentQuestionIndex];
    if (!currentQuestion) {
        // Tampilkan pesan loading atau error jika pertanyaan tidak ditemukan
        return (
            <div className="flex min-h-screen items-center justify-center bg-slate-900 text-white">
                Loading question...
            </div>
        );
    }

    // Kalkulasi progress yang lebih intuitif
    const progressPercentage =
        (currentQuestionIndex / quiz.details.questions.length) * 100;

    // Logika pemilihan kelas grid yang aman dari error
    const numAnswers = currentQuestion.answers?.length || 0;
    const responsiveGridClass =
        gridColsByAnswerCount[numAnswers] || 'md:grid-cols-2'; // Fallback aman

    return (
        <div className="flex min-h-screen flex-col bg-slate-900 p-4 text-white sm:p-6 lg:p-8">
            <header className="mb-6 flex items-center justify-between">
                <div className="flex items-center gap-3">
                    <BookCopy className="h-8 w-8 text-slate-400" />
                    <div>
                        <h1 className="text-xl font-bold">{quiz.title}</h1>
                        <p className="text-sm text-slate-400">
                            {quiz.classroom.name}
                        </p>
                    </div>
                </div>
                <div className="flex items-center gap-2 rounded-lg bg-yellow-400 px-3 py-1.5 font-bold text-black">
                    <Clock className="h-5 w-5" />
                    <span>{formattedTime}</span>
                </div>
            </header>

            <div className="mb-8">
                <div className="mb-1 flex justify-between text-sm text-slate-400">
                    <span>
                        Question {currentQuestionIndex + 1} of{' '}
                        {quiz.details.questions.length}
                    </span>
                    <span>{Math.round(progressPercentage)}% Complete</span>
                </div>
                <Progress value={progressPercentage} className="h-2" />
            </div>

            <main className="flex flex-grow flex-col items-center justify-center text-center">
                <h2 className="mb-10 text-3xl font-bold">
                    {currentQuestion.question_text}
                </h2>

                {currentQuestion.image_path && (
                    <img
                        src={currentQuestion.image_path}
                        alt={`Image for question: ${currentQuestion.question_text}`}
                        className="mb-6 max-h-64 w-full max-w-lg rounded-lg object-contain"
                    />
                )}

                <div
                    className={cn(
                        'grid w-full max-w-4xl grid-cols-1 gap-4',
                        responsiveGridClass,
                    )}
                >
                    {currentQuestion.answers.map((answer, index) => (
                        <button
                            key={answer.id}
                            onClick={() =>
                                onAnswerSelect(currentQuestion.id, answer.id)
                            }
                            disabled={isSubmitting} // Gunakan prop isSubmitting di sini
                            className={cn(
                                'flex h-full flex-col rounded-lg border-2 border-slate-700 p-4 text-left transition-colors',
                                'hover:border-primary hover:bg-slate-800 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-slate-900 focus:outline-none',
                                'disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-slate-700 disabled:hover:bg-transparent',
                            )}
                        >
                            <div className="flex items-center">
                                <span className="mr-4 font-bold text-slate-500">
                                    {String.fromCharCode(65 + index)}
                                </span>
                                <span>{answer.answer_text}</span>
                            </div>

                            {answer.image_path && (
                                <img
                                    src={answer.image_path}
                                    alt={`Image for answer option ${index + 1}`}
                                    className="mt-4 w-full flex-1 rounded-lg object-cover"
                                />
                            )}
                        </button>
                    ))}
                </div>
            </main>
        </div>
    );
};
