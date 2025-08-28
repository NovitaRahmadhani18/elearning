import DataTable from '@/components/data-table/data-table';
import { usePage } from '@inertiajs/react';
import { studentColumn } from './column';

import TableToolbar from '@/components/data-table/table-toolbar';
import useDebouncedSearch from '@/hooks/use-debounce-search';
import { ShowClassroomPageProps } from '@/pages/admin/classroom/types';

const StudentClassroomTable = () => {
    const { classroom, filters } = usePage<ShowClassroomPageProps>().props;

    const { setParams, params, setTimeDebounce } = useDebouncedSearch(
        route(route().current() as string, classroom.data.id),
        filters,
        500,
    );

    return (
        <div className="space-y-4">
            <TableToolbar
                params={params}
                setParams={setParams}
                setTimeDebounce={setTimeDebounce}
                placeholder="Search students..."
                showPagination={false}
            />
            <DataTable
                data={classroom.data.studentUsers || []}
                columns={studentColumn}
                title="Students"
            />
        </div>
    );
};

export default StudentClassroomTable;
