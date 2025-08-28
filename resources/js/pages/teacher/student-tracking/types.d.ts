import { TClassroom } from '@/pages/admin/classroom/types';
import { PaginatedData, SharedData } from '@/types';
import { TUser } from '@/types/users';

export type TStudentClassroom = {
    student: TUser;
    classroom: TClassroom;
    progress: number; // percentage of progress in the classroom
};

export interface TStudentTrackingPageProps extends SharedData {
    studentClassrooms: PaginatedData<TStudentClassroom>;
    averageCompletionRate: number;
}
