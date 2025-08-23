import DataTable from '@/components/data-table/data-table';
import { useMemo } from 'react';
import { mockActivityLog } from '../../dashboard-admin';
import { TActvityUser } from '../../types';
import { monitoringTableColumns } from './columns';

const MonitoringTable = () => {
    const userActivities: TActvityUser[] = useMemo(() => {
        return mockActivityLog;
    }, []);

    return (
        <div>
            <DataTable
                data={userActivities}
                columns={monitoringTableColumns}
                title="Activity Log"
            />
        </div>
    );
};

export default MonitoringTable;
