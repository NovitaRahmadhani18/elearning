import { Toaster } from '@/components/ui/sonner';
import { useCountdown } from '@/hooks/use-countdown';
import api from '@/lib/api';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useCallback, useEffect, useState } from 'react';
import { toast } from 'sonner';
import { QuizInProgressView } from '../partials/quiz-in-progress-view';
import { QuizResult, QuizResultsView } from '../partials/quiz-results-view';

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
}

const QuizTakingPage = () => {
    const { content: contetData } = usePage<QuizStartPageProps>().props;

    const content = contetData.data;

    const [submission, setSubmission] = useState<QuizSubmission | null>(null);
    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
    const [userAnswers, setUserAnswers] = useState<UserAnswers>({});
    const [remainingSeconds, setRemainingSeconds] = useState<number | null>(null);

    // --- API INTERACTIONS ---

    // Starts a new quiz submission or resumes an existing one
    const startOrResumeQuiz = async () => {
        try {
            const response = await api.post<{
                data: QuizSubmission;
            }>(`/quizzes/${content.id}/submissions`);
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
            setSubmission(submissionData);
            setUserAnswers(submissionData.submitted_answers || {});
            setQuizState('taking');
        } catch (error) {
            toast.error('Failed to start or resume the quiz.');
            console.error(error);
        }
    };

    // Auto-saves progress to the backend
    useEffect(() => {
        if (
            quizState !== 'taking' ||
            !submission?.id ||
            Object.keys(userAnswers).length === 0
        ) {
            return;
        }

        const answersPayload = Object.entries(userAnswers).map(
            ([questionId, answerId]) => ({
                question_id: parseInt(questionId, 10),
                answer_id: answerId,
            }),
        );

        const handler = setTimeout(() => {
            if (answersPayload.length === 0) return;

            api.patch(`/quiz-submissions/${submission.id}`, {
                answers: answersPayload,
            }).catch((error: any) => {
                console.error('Autosave failed', error);
                if (error.response?.status === 422) {
                    toast.error(
                        error.response?.data?.message ||
                            'A validation error occurred.',
                    );
                    // If time is up, immediately show results (even if partial)
                    if (
                        submission &&
                        error.response?.data?.message?.includes("Time's up!")
                    ) {
                        setQuizResult({
                            final_score: 0,
                            correct_answers_count: 0,
                            total_questions: submission.quiz.questions.length,
                            time_spent_seconds: Math.floor(
                                (new Date().getTime() -
                                    new Date(submission.started_at).getTime()) /
                                    1000,
                            ),
                            accuracy: 0,
                            incorrect_answers_count:
                                submission.quiz.questions.length,
                        });
                        setQuizState('completed');
                    } else {
                        setQuizState('taking'); // Revert state on other 422 errors
                    }
                } else {
                    toast.error('Failed to save progress.');
                }
            });
        }, 2000); // Debounce autosave

        return () => clearTimeout(handler);
    }, [userAnswers, submission?.id, quizState]);

    // Submits the quiz for final grading
    const handleSubmitQuiz = useCallback(
        (finalAnswers: UserAnswers) => {
            if (!submission) return;
            setQuizState('submitting');

            const finalAnswersPayload = Object.entries(finalAnswers).map(
                ([questionId, answerId]) => ({
                    question_id: parseInt(questionId, 10),
                    answer_id: answerId,
                }),
            );

            api.post<QuizSubmission>(`/quiz-submissions/${submission.id}/complete`, {
                answers: finalAnswersPayload,
            })
                .then((response) => {
                    const finalSubmission = response.data;
                    const result: QuizResult = {
                        final_score: finalSubmission.score ?? 0,
                        correct_answers_count:
                            finalSubmission.correct_answers_count ?? 0,
                        total_questions: finalSubmission.total_questions ?? 0,
                        time_spent_seconds: finalSubmission.duration_seconds ?? 0,
                        accuracy: finalSubmission.accuracy ?? 0,
                        incorrect_answers_count:
                            finalSubmission.incorrect_answers_count ?? 0,
                    };

                    setQuizResult(result);
                    setQuizState('completed');
                    toast.success('Quiz submitted successfully!');
                })
                .catch((error: any) => {
                    console.error('Failed to submit quiz.', error);
                    if (error.response?.status === 422) {
                        toast.error(
                            error.response?.data?.message ||
                                'A validation error occurred.',
                        );
                    } else {
                        toast.error('An unexpected error occurred.');
                    }
                    setQuizState('taking'); // Revert state on failure
                });
        },
        [submission],
    );

    // --- INITIALIZATION ---
    useEffect(() => {
        startOrResumeQuiz();
    }, []);

    // --- UI LOGIC ---

    const onTimerEnd = useCallback(() => {
        handleSubmitQuiz(userAnswers);
    }, [handleSubmitQuiz, userAnswers]);

    const { formattedTime } = useCountdown(remainingSeconds ?? 0, onTimerEnd);

    const handleAnswerSelect = (questionId: number, answerId: number) => {
        const newAnswers = { ...userAnswers, [questionId]: answerId };
        setUserAnswers(newAnswers);

        if (
            submission &&
            currentQuestionIndex < submission.quiz.questions.length - 1
        ) {
            setCurrentQuestionIndex((prev) => prev + 1);
        } else {
            handleSubmitQuiz(newAnswers);
        }
    };

    if (quizState === 'completed' && quizResult) {
        return <QuizResultsView result={quizResult} quiz={content} />;
    }

    if (quizState === 'loading' || !submission) {
        return (
            <div className="flex min-h-screen items-center justify-center bg-slate-900 text-white">
                <p className="text-2xl font-bold">Loading Quiz...</p>
            </div>
        );
    }

    if (quizState === 'submitting') {
        return (
            <div className="flex min-h-screen items-center justify-center bg-slate-900 text-white">
                <div className="text-center">
                    <p className="text-2xl font-bold">Submitting your answers...</p>
                    <p className="text-slate-400">Please wait.</p>
                </div>
            </div>
        );
    }

    return (
        <main>
            <Head title={`Taking Quiz: ${content.title}`} />
            <QuizInProgressView
                quiz={content}
                currentQuestionIndex={currentQuestionIndex}
                onAnswerSelect={handleAnswerSelect}
                formattedTime={formattedTime}
            />
            <Toaster position="top-right" richColors />
        </main>
    );
};

export default QuizTakingPage;
