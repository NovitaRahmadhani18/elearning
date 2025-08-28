import Heading from '@/components/heading';
import StudentLayout from '@/layouts/student-layout';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { Star, TrendingUp, Trophy } from 'lucide-react';
import { useMemo } from 'react';
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

interface AchievementPageProps extends SharedData {
    achievements: {
        data: TAchievement[];
    };
}

const AchievementPage = () => {
    const { achievements, auth } = usePage<AchievementPageProps>().props;

    const sumaryData: TSummaryCardData[] = useMemo(
        () => [
            {
                id: 1,
                label: 'Total Lencana',
                value: `${achievements.data.filter((a) => !a.locked).length}/${achievements.data.length}`,
                Icon: Trophy,
                color: 'blue',
            },
            {
                id: 2,
                label: 'Total Point',
                value: `${auth.user?.total_points || 0} Points`,
                Icon: Star,
                color: 'yellow',
            },
            {
                id: 3,
                label: 'Tingkat Penyelesaian',
                value: `${((achievements.data.filter((a) => !a.locked).length / achievements.data.length) * 100).toFixed(0)}%`,
                Icon: TrendingUp,
                color: 'green',
            },
        ],
        [achievements, auth],
    );

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
                                {sumaryData.map((card, i) => (
                                    <SummaryCard
                                        key={`summary-card-${i}`}
                                        icon={card.Icon}
                                        label={card.label}
                                        value={card.value}
                                        color={card.color}
                                    />
                                ))}
                            </div>

                            {/* Grid Lencana */}
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                {achievements.data.length > 0 ? (
                                    achievements.data.map((achievement) => (
                                        <AchievementCard
                                            key={achievement.id}
                                            achievement={achievement}
                                        />
                                    ))
                                ) : (
                                    <p className="text-center text-slate-500">
                                        No achievements earned yet. Keep going!
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </StudentLayout>
    );
};

export default AchievementPage;
