import { TAnswer, TQuestion } from '@/pages/teacher/quiz/types';

export type TSubmissionAnswer = {
    id: number;
    quiz_submission_id: number;
    question_id: number;
    answer_id: number;
    is_correct: boolean;
    question: TQuestion;
    answer: TAnswer;
};

export type TQuizSubmission = {
    id: number;
    student_id: number;
    started_at: string;
    completed_at: string | null;
    score: number | null;
    quiz: TContentQuiz['details'];
    submitted_answers: TSubmissionAnswer[];
    duration_seconds: number | null;

    accuracy: number | null;
    correct_answers_count: number | null;
    incorrect_answers_count: number | null;
};
