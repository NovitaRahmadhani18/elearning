import { TContentMaterial } from '@/pages/teacher/material/types';

export type TLeaderboardItem = {
    user: {
        id: number;
        name: string;
        avatar: string | null;
    };
    rank: number | null;
    score: number | null;
    completed_at: string | null;
    duration_seconds: number | null;
};

export interface TContentLeaderboard extends TContentMaterial {
    leaderboard: TLeaderboardItem[];
}
