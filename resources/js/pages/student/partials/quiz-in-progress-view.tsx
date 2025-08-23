import { Progress } from '@/components/ui/progress';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { BookCopy, Clock } from 'lucide-react';

interface QuizInProgressViewProps {
    quiz: TContentQuiz;
    currentQuestionIndex: number;
    onAnswerSelect: (questionId: number, answerId: number) => void;
    formattedTime: string;
}

export const QuizInProgressView = ({
    quiz,
    currentQuestionIndex,
    onAnswerSelect,
    formattedTime,
}: QuizInProgressViewProps) => {
    const currentQuestion = quiz.details.questions[currentQuestionIndex];
    const progressPercentage =
        (currentQuestionIndex / quiz.details.questions.length) * 100;

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
                <div className="grid w-full max-w-4xl grid-cols-1 gap-4 md:grid-cols-2">
                    {currentQuestion.answers.map((answer, index) => (
                        <button
                            key={answer.id}
                            onClick={() =>
                                onAnswerSelect(currentQuestion.id, answer.id)
                            }
                            className="flex items-center rounded-lg border-2 border-slate-700 p-4 text-left transition-colors hover:border-purple-500 hover:bg-slate-800"
                        >
                            <span className="mr-4 font-bold text-slate-500">
                                {String.fromCharCode(65 + index)}
                            </span>
                            <span>{answer.answer_text}</span>
                        </button>
                    ))}
                </div>
            </main>
        </div>
    );
};
