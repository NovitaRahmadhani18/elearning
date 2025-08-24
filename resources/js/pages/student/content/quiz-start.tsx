import { Toaster } from '@/components/ui/sonner';
import { useCountdown } from '@/hooks/use-countdown'; // Asumsi hook ini ada
import { TContentQuiz } from '@/pages/teacher/material/types';
import { SharedData } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useCallback, useMemo, useState } from 'react';
import { toast } from 'sonner';
import { QuizInProgressView } from '../partials/quiz-in-progress-view';
import { TQuizSubmission } from './types';

interface QuizStartPageProps extends SharedData {
    content: {
        data: TContentQuiz;
    };
    quizSubmission?: {
        data: TQuizSubmission;
    };
}

const QuizTakingPage = () => {
    const { content: contentData, quizSubmission } =
        usePage<QuizStartPageProps>().props;
    const content = contentData.data;

    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(
        quizSubmission?.data?.submitted_answers?.length ?? 0,
    );
    const [isSubmitting, setIsSubmitting] = useState(false);

    const getRemainingSecondsFromProps: number | null = useMemo(() => {
        if (!quizSubmission) return null;
        const startTime = new Date(quizSubmission.data.started_at).getTime();
        const durationMillis = (content.details.duration_minutes || 0) * 60 * 1000;
        const deadline = startTime + durationMillis;
        const now = new Date().getTime();
        return Math.max(0, Math.floor((deadline - now) / 1000));
    }, [quizSubmission, content.details.duration_minutes]);

    const handleSubmitQuiz = useCallback(() => {
        router.visit(route('student.quizzes.result', content.id));
    }, [content.id]);

    const { formattedTime } = useCountdown(
        getRemainingSecondsFromProps ?? 0,
        handleSubmitQuiz,
    );

    const handleAnswerSelect = (questionId: number, answerId: number) => {
        const isLastQuestion =
            currentQuestionIndex >= content.details.questions.length - 1;

        router.post(
            route('student.quizzes.answer', content.id),
            { question_id: questionId, answer_id: answerId },
            {
                onStart: () => setIsSubmitting(true),
                onSuccess: () => {
                    if (isLastQuestion) {
                        handleSubmitQuiz();
                    } else {
                        setCurrentQuestionIndex((prev) => prev + 1);
                    }
                },
                onError: (e) => {
                    console.error('Error submitting answer:', e);
                    toast.error('Failed to submit answer. Please try again.');
                },
                onFinish: () => setIsSubmitting(false),
            },
        );
    };

    return (
        <main>
            <Head title={`Taking Quiz: ${content.title}`} />
            <QuizInProgressView
                quiz={content}
                formattedTime={formattedTime}
                onAnswerSelect={handleAnswerSelect}
                currentQuestionIndex={currentQuestionIndex}
                isSubmitting={isSubmitting}
            />
            <Toaster position="top-right" richColors />
        </main>
    );
};

export default QuizTakingPage;
