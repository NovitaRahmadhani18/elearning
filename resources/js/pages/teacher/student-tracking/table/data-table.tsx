import DataTable from '@/components/data-table/data-table';
import TablePagination from '@/components/data-table/table-pagination';
import TableToolbar from '@/components/data-table/table-toolbar';
import useDebouncedSearch from '@/hooks/use-debounce-search';
import { usePage } from '@inertiajs/react';
import { TStudentTrackingPageProps } from '../types';
import { studentTrackingTableColumns } from './column';

const StudentTrackingTable = () => {
    const { studentClassrooms, filters } =
        usePage<TStudentTrackingPageProps>().props;

    const { setParams, params, setTimeDebounce } = useDebouncedSearch(
        route(route().current() as string),
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
            />
            <DataTable
                data={studentClassrooms.data || []}
                columns={studentTrackingTableColumns}
                title="Student Progress Tracking"
            />

            <TablePagination
                links={studentClassrooms.links}
                meta={studentClassrooms.meta}
            />
        </div>
    );
};

export default StudentTrackingTable;
