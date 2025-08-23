import DataTable from '@/components/data-table/data-table';
import { usePage } from '@inertiajs/react';
import { TStudentTrackingPageProps } from '../types';
import { studentTrackingTableColumns } from './column';

const StudentTrackingTable = () => {
    const { studentClassrooms } = usePage<TStudentTrackingPageProps>().props;

    return (
        <div>
            <DataTable
                data={studentClassrooms.data}
                columns={studentTrackingTableColumns}
                title="Student Progress Tracking"
            />
        </div>
    );
};

export default StudentTrackingTable;
