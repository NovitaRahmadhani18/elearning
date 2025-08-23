import DataTable from '@/components/data-table/data-table';
import { usePage } from '@inertiajs/react';
import { studentColumn } from './column';

import { ShowClassroomPageProps } from '@/pages/admin/classroom/types';

const StudentClassroomTable = () => {
    const { classroom } = usePage<ShowClassroomPageProps>().props;

    return (
        <div>
            <DataTable
                data={classroom.data.studentUsers || []}
                columns={studentColumn}
                title="Student Classrooms"
            />
        </div>
    );
};

export default StudentClassroomTable;
