import Heading from '@/components/heading';
import StudentLayout from '@/layouts/student-layout';
import { SharedData } from '@/types';
import StudentProgressPage from './leaderboard';
import { TContentLeaderboard } from './types';

export interface LeaderboardPageProps extends SharedData {
    contentLeaderboards: {
        data: TContentLeaderboard[];
    };
}

const LeaderboardPage: React.FC<LeaderboardPageProps> = ({
    contentLeaderboards,
}) => {
    return (
        <StudentLayout>
            <div className="flex flex-1 flex-col gap-4 space-y-4">
                <Heading
                    title="Leaderboard"
                    description="See how you rank among your peers."
                />

                <StudentProgressPage
                    contentLeaderboards={contentLeaderboards.data}
                />
            </div>
        </StudentLayout>
    );
};

export default LeaderboardPage;
