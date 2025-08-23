import { Toaster } from '@/components/ui/sonner';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import { QuizResult, QuizResultsView } from '../partials/quiz-results-view';
import { QuizSubmission } from './quiz-start';

interface QuizResultPageProps extends SharedData {
    content: {
        data: TContentQuiz;
    };

    quizSubmission: {
        data: QuizSubmission;
    };
}

const QuizResultPage = () => {
    const { quizSubmission, content } = usePage<QuizResultPageProps>().props;

    const quiz = content.data?.details;
    const submission = quizSubmission.data;

    const result: QuizResult = useMemo(() => {
        return {
            accuracy: 10,
            correct_answers_count: submission.score || 0,
            final_score: submission.score || 0,
            incorrect_answers_count: quiz.questions.length - (submission.score || 0),
            time_spent_seconds: 10,
            total_questions: quiz.questions.length || 0,
        };
    }, [quiz.questions.length, submission.score]);

    return (
        <main>
            <Head title={`Taking Quiz: ${content.data.title}`} />
            <QuizResultsView quiz={content.data} result={result} />
            <Toaster position="top-right" richColors />
        </main>
    );
};

export default QuizResultPage;
