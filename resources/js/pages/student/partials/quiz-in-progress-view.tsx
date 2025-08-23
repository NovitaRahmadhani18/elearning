import { Progress } from '@/components/ui/progress';
import { cn } from '@/lib/utils';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { BookCopy, Clock } from 'lucide-react';

interface QuizInProgressViewProps {
    quiz: TContentQuiz;
    currentQuestionIndex: number;
    onAnswerSelect: (questionId: number, answerId: number) => void;
    formattedTime: string;
}

const gridColsByAnswerCount = {
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
}: QuizInProgressViewProps) => {
    const currentQuestion = quiz.details.questions[currentQuestionIndex];
    const progressPercentage =
        (currentQuestionIndex / quiz.details.questions.length) * 100;

    const responsiveGridClass =
        // @ts-expect-error Unknown length
        gridColsByAnswerCount[currentQuestion.answers?.length] || 'md:grid-cols-2';

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
                {/* // image */}
                {currentQuestion.image_path && (
                    <img
                        src={currentQuestion.image_path}
                        alt="Question Image"
                        className="mb-6 max-h-64 w-full max-w-md rounded-lg object-cover"
                    />
                )}
                <div
                    className={cn(
                        'grid w-full grid-cols-1 gap-4', // Default 1 kolom untuk HP (Mobile-first)
                        responsiveGridClass, // Terapkan kelas dinamis untuk layar lebih besar
                    )}
                >
                    {currentQuestion.answers.map((answer, index) => (
                        <button
                            key={answer.id}
                            onClick={() =>
                                onAnswerSelect(currentQuestion.id, answer.id)
                            }
                            // Anda bisa membuat tinggi item konsisten jika perlu dengan `h-full`
                            className="flex h-full flex-col rounded-lg border-2 border-slate-700 p-4 text-left transition-colors hover:border-purple-500 hover:bg-slate-800"
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
                                    alt="Answer Image" // Beri alt text yang lebih deskriptif jika memungkinkan
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
