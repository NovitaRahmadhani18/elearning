import { Toaster } from '@/components/ui/sonner';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import { QuizResult, QuizResultsView } from '../partials/quiz-results-view';
import { TQuizSubmission } from './types';

interface QuizResultPageProps extends SharedData {
    content: {
        data: TContentQuiz;
    };

    quizSubmission: {
        data: TQuizSubmission;
    };
}

const QuizResultPage = () => {
    const { quizSubmission, content } = usePage<QuizResultPageProps>().props;

    const result: QuizResult = useMemo(() => {
        return {
            // total jumlah pertanyaan yang benar
            accuracy: quizSubmission.data.accuracy || 0,

            correct_answers_count: quizSubmission.data.correct_answers_count || 0,
            final_score: quizSubmission.data.score || 0,
            incorrect_answers_count:
                quizSubmission.data.incorrect_answers_count || 0,
            time_spent_seconds: quizSubmission.data.duration_seconds || 0,
            total_questions: content.data.details.questions.length || 0,
        };
    }, [quizSubmission, content.data.details.questions.length]);

    return (
        <main>
            <Head title={`Taking Quiz: ${content.data.title}`} />
            <QuizResultsView quiz={content.data} result={result} />
            <Toaster position="top-right" richColors />
        </main>
    );
};

export default QuizResultPage;
