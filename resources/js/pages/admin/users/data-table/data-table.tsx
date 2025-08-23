import { ActionButton } from '@/components/action-button';
import DataTable from '@/components/data-table/data-table';
import TablePagination from '@/components/data-table/table-pagination';
import TableToolbar from '@/components/data-table/table-toolbar';
import useDebouncedSearch from '@/hooks/use-debounce-search';
import { Link, usePage } from '@inertiajs/react';
import { UserIndexPageProps } from '../types';
import { userColumns } from './column';

const UserDataTable = () => {
    const { users, filters } = usePage<UserIndexPageProps>().props;

    const { params, setParams, setTimeDebounce } = useDebouncedSearch(
        route(route().current() as string),
        filters,
        500,
    );

    return (
        <div className="space-y-2">
            <TableToolbar
                placeholder="Cari user..."
                params={params}
                setTimeDebounce={setTimeDebounce}
                setParams={setParams}
                afterSearchComponent={
                    <Link href={route('admin.users.create')} className="ml-auto">
                        <ActionButton action="create">Add User</ActionButton>
                    </Link>
                }
            />
            <DataTable data={users.data} columns={userColumns} />
            <TablePagination links={users.links} meta={users.meta} />
        </div>
    );
};

export default UserDataTable;
