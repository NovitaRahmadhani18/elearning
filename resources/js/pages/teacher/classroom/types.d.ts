import { TCategory, TClassroom } from '@/pages/admin/classroom/types';
import { PaginatedData, SharedData } from '@/types';

export interface TeacherClassroomPageProps extends SharedData {
    classrooms: PaginatedData<TClassroom>;
    categories: TCategory[];
}

export interface TeacherCreateClassroomPageProps extends SharedData {
    categories: TClassroomCategory[];
}

export interface TeacherEditClassroomPageProps extends SharedData {
    classroom: {
        data: TClassroom;
    };
    categories: TClassroomCategory[];
}
