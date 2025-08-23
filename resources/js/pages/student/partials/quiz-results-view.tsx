import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
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

export const QuizResultsView = ({ result, quiz }: QuizResultsViewProps) => (
    <div className="flex min-h-screen flex-col items-center justify-center bg-slate-900 p-4 text-white sm:p-6">
        <div className="mx-auto w-full max-w-2xl text-center">
            <Trophy className="mx-auto mb-4 h-16 w-16 text-amber-400" />
            <h1 className="text-4xl font-extrabold">Quiz Completed!</h1>
            <p className="mt-2 text-lg text-slate-400">Fair</p>

            <div className="my-8 flex items-center justify-center gap-3">
                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-purple-600 text-xl font-bold">
                    U
                </div>
                <div>
                    <p className="text-left font-semibold">User</p>
                    <p className="text-left text-sm text-slate-400">
                        {quiz.classroom.name} • {quiz.title}
                    </p>
                </div>
            </div>

            <div className="mb-8 grid grid-cols-3 gap-4 text-center">
                <div className="rounded-lg bg-red-500/90 p-4">
                    <p className="text-sm opacity-80">Final Score</p>
                    <p className="text-3xl font-bold">{result.final_score.toFixed(1)}%</p>
                </div>
                <div className="rounded-lg bg-green-500/90 p-4">
                    <p className="text-sm opacity-80">Correct Answers</p>
                    <p className="text-3xl font-bold">
                        {result.correct_answers_count}/{result.total_questions}
                    </p>
                </div>
                <div className="rounded-lg bg-blue-500/90 p-4">
                    <p className="text-sm opacity-80">Time Spent</p>
                    <p className="text-3xl font-bold">
                        {`${Math.floor(result.time_spent_seconds / 60)
                            .toString()
                            .padStart(2, '0')}:${(result.time_spent_seconds % 60).toString().padStart(2, '0')}`}
                    </p>
                </div>
            </div>

            <div className="mb-6 rounded-lg bg-slate-800 p-6 text-left">
                <h2 className="mb-4 text-lg font-bold">Performance Breakdown</h2>
                <div className="mb-1 flex items-center justify-between text-sm text-slate-400">
                    <span>Accuracy</span>
                    <span>{result.accuracy.toFixed(1)}%</span>
                </div>
                <Progress
                    value={result.accuracy}
                    className="h-2 [&>div]:bg-gradient-to-r [&>div]:from-amber-500 [&>div]:to-orange-500"
                />
                <div className="mt-4 grid grid-cols-2 gap-4">
                    <div className="rounded bg-slate-700 p-4">
                        <p className="text-sm text-slate-400">Incorrect</p>
                        <p className="text-2xl font-bold">{result.incorrect_answers_count}</p>
                    </div>
                    <div className="rounded bg-slate-700 p-4">
                        <p className="text-sm text-slate-400">Accuracy</p>
                        <p className="text-2xl font-bold">{result.accuracy.toFixed(1)}%</p>
                    </div>
                </div>
            </div>

            <div className="mb-8 flex items-center justify-center gap-3 rounded-lg bg-blue-900/50 p-4">
                <Lightbulb className="h-6 w-6 text-yellow-300" />
                <p>Not bad! Review the materials and try again.</p>
            </div>

            <div className="space-y-3">
                <Button size="lg" className="w-full bg-amber-500 font-bold text-black hover:bg-amber-600">
                    Review Answers
                </Button>
                <Button asChild size="lg" variant="link" className="w-full text-slate-300">
                    <Link href={route('student.classrooms.show', quiz.classroom_id)}>
                        <ArrowLeft className="mr-2 h-4 w-4" />
                        Back to Classroom
                    </Link>
                </Button>
            </div>
        </div>
    </div>
);
