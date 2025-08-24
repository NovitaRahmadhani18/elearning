import DataTable from '@/components/data-table/data-table';
import { studentQuizColumn } from './column';

const StudentQuizTable = () => {
    return (
        <div>
            <DataTable
                data={[]}
                columns={studentQuizColumn}
                title="Completed Students"
            />
        </div>
    );
};

export default StudentQuizTable;
