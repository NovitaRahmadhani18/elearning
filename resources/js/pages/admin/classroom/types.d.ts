import { TContent } from '@/pages/teacher/material/types';
import { TStudentClassroom } from '@/pages/teacher/student-tracking/types';
import { SharedData } from '@/types';
import { TUser } from '@/types/users';

export interface TClassroomCategory {
    id: number;
    name: string;
    value: string;
}

export interface TStatus {
    id: number;
    name: string;
    value: string;
}

export interface TClassroom {
    id: number;
    name: string;
    fullName: string; // Full name for the classroom
    code: string;
    teacher: TUser;
    description: string;
    thumbnail?: string; // Optional property for thumbnail
    created_at: string;
    updated_at: string;
    category: TClassroomCategory;
    status: TStatus;
    invite_code: string;
    contents?: TContent[];
    students?: TStudentClassroom[];
    studentUsers?: TUser[];
    students_count?: number;
}

export type TCategory = {
    label: string;
    value: string;
};

export interface ClassroomIndexPageProps extends SharedData {
    classrooms: PaginatedData<TClassroom>;
    categories: TCategory[];
}

export interface CreateClassroomPageProps extends SharedData {
    categories: TClassroomCategory[];
    teachers: {
        data: TUser[];
    };
}

export interface EditClassroomPageProps extends SharedData {
    classroom: {
        data: TClassroom;
    };
    categories: TClassroomCategory[];
    teachers: {
        data: TUser[];
    };
}

export interface ShowClassroomPageProps extends SharedData {
    classroom: {
        data: TClassroom;
    };
    students?: {
        data: TUser;
    };
}
