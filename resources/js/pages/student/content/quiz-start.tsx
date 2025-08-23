import { Toaster } from '@/components/ui/sonner';
import { useCountdown } from '@/hooks/use-countdown';
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
    const { content: contetData, quizSubmission } =
        usePage<QuizStartPageProps>().props;

    const content = contetData.data;

    console.log(content);
    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(
        quizSubmission?.data?.submitted_answers?.length ?? 0,
    );

    const getRemainingSecondsFromProps: number | null = useMemo(() => {
        if (!quizSubmission) return null;

        const startTime = new Date(quizSubmission.data.started_at).getTime();
        const durationMillis = (content.details.duration_minutes || 0) * 60 * 1000;
        const deadline = startTime + durationMillis;
        const now = new Date().getTime();
        return Math.max(0, Math.floor((deadline - now) / 1000));
    }, [quizSubmission, content.details.duration_minutes]);

    const handleSubmitQuiz = useCallback(() => {
        try {
            router.visit(route('student.quizzes.result', content.id));

            toast.success('Quiz submitted successfully!');
        } catch (error) {
            console.error('Error submitting quiz:', error);
            toast.error('Failed to submit quiz. Please try again.');
        }
    }, [content.id]);

    const { formattedTime } = useCountdown(
        getRemainingSecondsFromProps ?? 0,
        handleSubmitQuiz,
    );

    const handleAnswerSelect = async (questionId: number, answerId: number) => {
        if (
            quizSubmission &&
            currentQuestionIndex < content.details.questions.length - 1
        ) {
            router.post(
                route('student.quizzes.answer', content.id),
                {
                    question_id: questionId,
                    answer_id: answerId,
                },
                {
                    onSuccess: () => {
                        setCurrentQuestionIndex((prev) => prev + 1);
                        toast.success('Answer submitted successfully!');
                    },
                    onError: (e) => {
                        toast.error(
                            'Failed to submit answer. Please try again.' + e.message,
                        );
                    },
                },
            );
        } else {
            router.post(
                route('student.quizzes.answer', content.id),
                {
                    question_id: questionId,
                    answer_id: answerId,
                },
                {
                    onSuccess: () => {
                        toast.success('Answer submitted successfully!');
                    },
                    onError: (e) => {
                        toast.error(
                            'Failed to submit answer. Please try again.' + e.message,
                        );
                    },
                },
            );
            handleSubmitQuiz();
        }
    };

    return (
        <main>
            <Head title={`Taking Quiz: ${content.title}`} />
            <QuizInProgressView
                quiz={content}
                formattedTime={formattedTime}
                onAnswerSelect={handleAnswerSelect}
                currentQuestionIndex={currentQuestionIndex}
            />
            <Toaster position="top-right" richColors />
        </main>
    );
};

export default QuizTakingPage;
