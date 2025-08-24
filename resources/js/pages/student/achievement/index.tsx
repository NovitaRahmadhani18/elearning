import Heading from '@/components/heading';
import StudentLayout from '@/layouts/student-layout';
import { Star, TrendingUp, Trophy } from 'lucide-react';
import AchievementCard from './partials/components/achievement-card';
import { SummaryCard } from './partials/components/achievement-stat-card';
import { TAchievement, TSummaryCardData } from './types';

export const mockAchievements: TAchievement[] = [
    {
        id: 1,
        name: 'Quiz Champion',
        description: 'Menyelesaikan kuis dengan nilai ≥ 85',
        image: '/images/achievements/quiz-champion.png',
        locked: true,
    },
    {
        id: 2,
        name: 'Fast Learner',
        description: 'Menyelesaikan kuis dalam waktu < 10 menit',
        image: '/images/achievements/fast-learner.png',
        locked: false,
        achieved_at: '2025-08-12T10:30:00.000Z',
    },
    {
        id: 3,
        name: 'Perfect Score',
        description: 'Mendapatkan nilai sempurna (100) pada salah satu kuis',
        image: '/images/achievements/perfect-score.png',
        locked: true,
    },
    {
        id: 4,
        name: 'Streak Master',
        description: 'Mengerjakan kuis 5 hari berturut-turut',
        image: '/images/achievements/streak-master.png',
        locked: true,
    },
    {
        id: 5,
        name: 'Top Rank',
        description: 'Berada di peringkat 3 besar leaderboard',
        image: '/images/achievements/top-rank.png',
        locked: true,
    },
];

const summaryData: TSummaryCardData[] = [
    { id: 1, label: 'Total Lencana', value: '1/5', Icon: Trophy, color: 'blue' },
    { id: 2, label: 'Total Point', value: '86 Point', Icon: Star, color: 'yellow' },
    {
        id: 3,
        label: 'Tingkat Penyelesaian',
        value: '20%',
        Icon: TrendingUp,
        color: 'green',
    },
];

const AchievementPage = () => {
    return (
        <StudentLayout>
            <div className="flex flex-1 flex-col gap-4 space-y-4">
                <Heading
                    title="Achievements"
                    description="Track your accomplishments and milestones."
                />

                <section>
                    <div className="min-h-screen bg-slate-50 p-4 sm:p-6 md:p-8">
                        <div className="mx-auto max-w-7xl">
                            {/* Kartu Statistik */}
                            <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                                {summaryData.map((card) => (
                                    <SummaryCard
                                        key={card.id}
                                        icon={card.Icon}
                                        label={card.label}
                                        value={card.value}
                                        color={card.color}
                                    />
                                ))}
                            </div>

                            {/* Grid Lencana */}
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                {mockAchievements.map((achievement) => (
                                    <AchievementCard
                                        key={achievement.id}
                                        achievement={achievement}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </StudentLayout>
    );
};

export default AchievementPage;
