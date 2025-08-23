import { ActionButton } from '@/components/action-button';
import DataTable from '@/components/data-table/data-table';
import TableToolbar from '@/components/data-table/table-toolbar';
import useDebouncedSearch from '@/hooks/use-debounce-search';
import { Link, usePage } from '@inertiajs/react';
import { MaterialPageProps } from '../types';
import { materialColumns } from './columns';

const MaterialDataTable = () => {
    const { materials, classroom, filters, ziggy } =
        usePage<MaterialPageProps>().props;

    const { params, setParams, setTimeDebounce } = useDebouncedSearch(
        ziggy.location as string,
        filters,
        500,
    );

    return (
        <div className="space-y-2">
            <TableToolbar
                placeholder="Search materials..."
                params={params}
                setTimeDebounce={setTimeDebounce}
                setParams={setParams}
                afterSearchComponent={
                    <Link
                        href={route('teacher.materials.create')}
                        className="ml-auto"
                    >
                        <ActionButton action="create">Add Material</ActionButton>
                    </Link>
                }
            />
            <DataTable data={materials.data} columns={materialColumns} />
        </div>
    );
};

export default MaterialDataTable;
