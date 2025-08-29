import { TClassroom } from '@/pages/admin/classroom/types';
import { TContentStudent } from '@/pages/admin/monitoring';
import { TContent } from '@/pages/teacher/material/types';
import { PaginatedData, SharedData } from '@/types';
import { TUser } from '@/types/users';
import { TAchievement } from '../achievement/types';

interface TStudentClassroom extends TClassroom {
    contents?: TContent[]; // Optional property for contents
    progress: number;
}

interface StudentClassroomPageProps extends SharedData {
    classrooms: PaginatedData<TStudentClassroom>;
}

interface StudentClassroomJoinFormProps extends SharedData {
    classroom: {
        data: TStudentClassroom;
    };
}

interface ShowStudentClassroomPageProps extends SharedData {
    classroom: {
        data: TStudentClassroom;
    };
    student: {
        data: TUser;
    };
    classroomStudent: {
        data: TStudentClassroom;
    };
    achievements: {
        data: TAchievement[];
    };
    contentStudents: {
        data: TContentStudent[];
    };
}
