import { Toaster } from '@/components/ui/sonner';
import { useCountdown } from '@/hooks/use-countdown';
import api from '@/lib/api';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { SharedData } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useCallback, useState } from 'react';
import { toast } from 'sonner';
import { QuizInProgressView } from '../partials/quiz-in-progress-view';

export type QuizSubmission = {
    id: number;
    student_id: number;
    started_at: string;
    completed_at: string | null;
    score: number | null;
    quiz: TContentQuiz['details'];
    submitted_answers: Record<number, number>;
};

export type UserAnswers = Record<number, number>; // { [questionId]: answerId }

interface QuizStartPageProps extends SharedData {
    content: {
        data: TContentQuiz;
    };
    quizSubmission?: {
        data: QuizSubmission;
    };
}

const QuizTakingPage = () => {
    const { content: contetData, quizSubmission } =
        usePage<QuizStartPageProps>().props;

    const content = contetData.data;

    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
    const [userAnswers, setUserAnswers] = useState<UserAnswers>({});
    const [remainingSeconds, setRemainingSeconds] = useState<number | null>(null);

    const { formattedTime } = useCountdown(remainingSeconds ?? 0, () => {});

    const submitSelectedAnswer = useCallback(async () => {
        try {
            const response = await api.get<{
                data: QuizSubmission;
            }>(`/quizzes/${content.id}/submissions/current`);
            const submissionData = response.data.data;

            // --- Server-Authoritative Timer Calculation ---
            const startTime = new Date(submissionData.started_at).getTime();
            const durationMillis =
                (submissionData.quiz.duration_minutes || 0) * 60 * 1000;
            const deadline = startTime + durationMillis;
            const now = new Date().getTime();
            const initialRemainingSeconds = Math.max(
                0,
                Math.floor((deadline - now) / 1000),
            );

            setRemainingSeconds(initialRemainingSeconds);
        } catch (error) {
            console.error('Error fetching quiz submission:', error);
            toast.error('Failed to fetch quiz submission. Please try again.');
        }
    }, [content.id]);

    const handleAnswerSelect = async (questionId: number, answerId: number) => {
        const newAnswers = { ...userAnswers, [questionId]: answerId };
        setUserAnswers(newAnswers);

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
            handleSubmitQuiz();
        }
    };

    const handleSubmitQuiz = useCallback(async () => {
        try {
            router.visit(route('student.quizzes.result', content.id));

            toast.success('Quiz submitted successfully!');
        } catch (error) {
            console.error('Error submitting quiz:', error);
            toast.error('Failed to submit quiz. Please try again.');
        }
    }, [content.id]);

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
