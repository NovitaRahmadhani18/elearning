import DataTable from '@/components/data-table/data-table';
import TablePagination from '@/components/data-table/table-pagination';
import TableToolbar from '@/components/data-table/table-toolbar';
import useDebouncedSearch from '@/hooks/use-debounce-search';
import { usePage } from '@inertiajs/react';
import { MonitoringPageProps } from '..';
import { monitoringTableColumns } from './columns';

const MonitoringTable = () => {
    const { monitorings, filters } = usePage<MonitoringPageProps>().props;

    const { setParams, setTimeDebounce, params } = useDebouncedSearch(
        route(route().current() as string),
        filters,
        500,
    );

    return (
        <div className="space-y-4">
            <TableToolbar
                setParams={setParams}
                placeholder="Search activity..."
                setTimeDebounce={setTimeDebounce}
                params={params}
            />
            <DataTable
                data={monitorings.data}
                columns={monitoringTableColumns}
                title="Activity Log"
            />
            <TablePagination meta={monitorings.meta} links={monitorings.links} />
        </div>
    );
};

export default MonitoringTable;
