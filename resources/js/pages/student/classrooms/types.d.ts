import { TClassroom } from '@/pages/admin/classroom/types';
import { TContent } from '@/pages/teacher/material/types';
import { PaginatedData, SharedData } from '@/types';

interface TStudentClassroom extends TClassroom {
    contents?: TContent[]; // Optional property for contents
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
    contents: {
        data: TContent[];
    };
}
