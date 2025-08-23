import Heading from '@/components/heading';
import StudentLayout from '@/layouts/student-layout';
import StudentProgressPage from './leaderboard';

const LeaderboardPage = () => {
    return (
        <StudentLayout>
            <div className="flex flex-1 flex-col gap-4 space-y-4">
                <Heading
                    title="Leaderboard"
                    description="See how you rank among your peers."
                />

                <StudentProgressPage />
            </div>
        </StudentLayout>
    );
};

export default LeaderboardPage;
