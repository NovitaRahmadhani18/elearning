import { TClassroom } from '@/pages/admin/classroom/types';
import { PaginatedData, SharedData } from '@/types';
import { TQuiz } from '../quiz/types';

export type TMaterial = {
    id: number;
    body: string;
    attachment_path: string | null;
};

type TContentBase = {
    id: number;
    title: string;
    description: string;
    points: number;
    order: number;
    classroom_id: number;
    classroom: TClassroom;
    created_at: string;
    updated_at: string;
    students_count?: number;

    status?: 'locked' | 'unlocked' | 'completed';
};

type TContentMaterial = TContentBase & {
    type: 'material';
    details: TMaterial;
};

type TContentQuiz = TContentBase & {
    type: 'quiz';
    details: TQuiz;
};

export type TContent = TContentMaterial | TContentQuiz;

export interface MaterialPageProps extends SharedData {
    materials: PaginatedData<TContent>;
}

export interface CreateMaterialPageProps extends SharedData {
    classrooms: {
        data: TClassroom[];
    };
}

export interface EditMaterialPageProps extends SharedData {
    material: {
        data: TContentMaterial;
    };
    classrooms: {
        data: TClassroom[];
    };
}
