import { TClassroom } from '@/pages/admin/classroom/types';
import { SharedData } from '@/types';
import { TUser } from '@/types/users';

export type TStudentClassroom = {
    student: TUser;
    classroom: TClassroom;
};

export interface TStudentTrackingPageProps extends SharedData {
    studentClassrooms: {
        data: TStudentClassroom[];
    };
}
