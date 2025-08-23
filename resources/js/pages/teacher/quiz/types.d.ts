import { TClassroom } from '@/pages/admin/classroom/types';
import { PaginatedData, SharedData } from '@/types';
import { TContent, TContentQuiz } from '../material/types';

export type TAnswer = {
    id: number;
    answer_text: string;
    image_path: string | null;
    is_correct: boolean;
};

export type TQuestion = {
    id: number;
    question_text: string;
    image_path: string | null;
    answers: TAnswer[];
};

export type TQuiz = {
    id: number;
    start_time: string;
    end_time: string;
    duration_minutes: number;
    questions: TQuestion[];
};

export type TAnswerState = {
    id?: number; // Opsional, karena belum ada saat pembuatan
    answer_text: string;
    image: File | null; // Kita gunakan 'image' untuk menampung File object
    is_correct: boolean;
};

export type TQuestionState = {
    id?: number;
    question_text: string;
    image: File | null;
    answers: TAnswerState[];
};

export type TQuizFormState = {
    title: string;
    description: string;
    classroom_id: string;
    points: number;
    start_time: Date | null;
    end_time: Date | null;
    duration_minutes: number;
    questions: TQuestionState[];
};

export interface QuizPageProps extends SharedData {
    quizzes: PaginatedData<TContent>;
}

interface CreateQuizPageProps extends SharedData {
    classrooms: {
        data: TClassroom[];
    };
}

interface EditQuizPageProps extends SharedData {
    quiz: {
        data: TContentQuiz;
    };
    classrooms: {
        data: TClassroom[];
    };
}
