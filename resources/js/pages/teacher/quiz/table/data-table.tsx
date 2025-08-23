import DataTable from '@/components/data-table/data-table';
import { mockProgressData } from '@/pages/student/leaderboard/leaderboard';
import { studentQuizColumn } from './column';

const StudentQuizTable = () => {
    return (
        <div>
            <DataTable
                data={mockProgressData[1].submissions}
                columns={studentQuizColumn}
                title="Completed Students"
            />
        </div>
    );
};

export default StudentQuizTable;
