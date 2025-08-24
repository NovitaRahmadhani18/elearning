import { cn } from '@/lib/utils';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { BookOpen, CheckCircle2, HelpCircle, Trophy } from 'lucide-react';
import { TContentLeaderboard, TLeaderboardItem } from './types'; // Pastikan path tipe benar

// ====================================================================
// SUB-KOMPONEN
// ====================================================================

export const StudentSubmissionRow = ({
    submission,
    isCurrentUser,
    type,
}: {
    submission: TLeaderboardItem;
    isCurrentUser: boolean;
    type: 'material' | 'quiz';
}) => {
    const rankStyles: Record<
        number | 'default',
        {
            background: string;
            border: string;
            icon: string;
        }
    > = {
        1: {
            background: 'bg-yellow-50',
            border: 'border-yellow-300',
            icon: 'text-yellow-500',
        },
        2: {
            background: 'bg-green-50',
            border: 'border-green-300',
            icon: 'text-green-600',
        },
        3: {
            background: 'bg-amber-50',
            border: 'border-amber-300',
            icon: 'text-amber-700',
        },
        default: {
            background: 'bg-gray-50',
            border: 'border-gray-200',
            icon: 'text-gray-400',
        },
    };

    const styles =
        (submission.rank && rankStyles[submission.rank]) || rankStyles.default;

    const formatTime = (totalSeconds: number | null = 0) => {
        if (totalSeconds === null) return null;
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    };

    return (
        <div
            className={cn(
                'flex items-center justify-between rounded-lg border-2 p-4 transition-all',
                styles.background,
                isCurrentUser
                    ? 'border-primary ring-2 ring-primary/20'
                    : styles.border,
            )}
        >
            <div className="flex items-center gap-3">
                {submission.rank ? (
                    <div
                        className={cn(
                            'flex h-8 w-8 items-center justify-center rounded-full border-2',
                            styles.border,
                        )}
                    >
                        <span className={cn('text-lg font-bold', styles.icon)}>
                            {submission.rank}
                        </span>
                    </div>
                ) : (
                    <div className="h-8 w-8 flex-shrink-0" />
                )}
                {submission.completed_at && (
                    <Trophy className={cn('h-6 w-6', styles.icon)} />
                )}
                <div>
                    <p className="font-bold text-gray-800">
                        {submission.user.name}
                        {isCurrentUser && (
                            <span className="ml-1 text-sm font-medium text-primary">
                                (You)
                            </span>
                        )}
                    </p>
                    {submission.completed_at ? (
                        <div className="flex items-center gap-2 text-xs text-gray-500">
                            <CheckCircle2 className="h-3 w-3 text-green-500" />
                            <span>Completed</span>
                            {type === 'quiz' && (
                                <span>
                                    • {formatTime(submission.duration_seconds)}
                                </span>
                            )}
                        </div>
                    ) : (
                        <div className="flex items-center gap-2 text-xs text-gray-500">
                            <span>Not yet completed</span>
                        </div>
                    )}
                </div>
            </div>

            <div className="text-right">
                {type === 'material'
                    ? submission.completed_at && (
                          <>
                              <p className="text-lg font-bold text-green-600">
                                  +{submission.score} pts
                              </p>
                              <p className="text-xs text-gray-500">Completed</p>
                          </>
                      )
                    : submission.completed_at && (
                          <>
                              <p className="text-lg font-bold text-gray-800">
                                  {submission.score?.toFixed(1)}%
                              </p>
                              <p className="text-xs text-green-600">
                                  +{submission.score} pts
                              </p>
                          </>
                      )}
            </div>
        </div>
    );
};

export const ContentProgressBlock = ({
    content,
    limit = 10,
}: {
    content: TContentLeaderboard;
    limit?: number; // Batas jumlah item yang ditampilkan
}) => {
    const { auth } = usePage<SharedData>().props;
    const currentUser = auth.user;

    // Cari data pengerjaan user saat ini untuk mendapatkan rankingnya
    const currentUserRankData = content.leaderboard.find(
        (item) => item.user.id === currentUser.id,
    );

    const leaderboardToShow = content.leaderboard.slice(0, limit);

    return (
        <div className="mb-8 w-full overflow-hidden rounded-lg bg-white shadow-md">
            <header className="flex flex-wrap items-center justify-between gap-y-2 bg-gray-100 p-4">
                <div className="flex items-center gap-3">
                    <div
                        className={cn(
                            'flex h-10 w-10 items-center justify-center rounded-md',
                            content.type === 'material'
                                ? 'bg-blue-100 text-blue-600'
                                : 'bg-purple-100 text-purple-600',
                        )}
                    >
                        {content.type === 'material' ? <BookOpen /> : <HelpCircle />}
                    </div>
                    <div>
                        <h2 className="text-xl font-bold text-slate-800">
                            {content.title}
                        </h2>
                        <p className="text-sm text-slate-500">
                            {content.classroom.name} •{' '}
                            {content.type.charAt(0).toUpperCase() +
                                content.type.slice(1)}{' '}
                            • {content.points} points
                        </p>
                    </div>
                </div>
                {currentUserRankData?.rank && (
                    <div className="text-right">
                        <div className="flex items-center gap-1 rounded-md bg-yellow-100 px-2 py-1 font-semibold text-yellow-800">
                            <Trophy className="h-4 w-4" />
                            <span>Your Rank: #{currentUserRankData.rank}</span>
                        </div>
                        <p className="mt-1 text-xs text-slate-500">
                            of {content.leaderboard.length} students
                        </p>
                    </div>
                )}
            </header>
            <main className="space-y-2 p-4">
                {leaderboardToShow.map((submission) => (
                    <StudentSubmissionRow
                        key={submission.user.id}
                        submission={submission}
                        isCurrentUser={submission.user.id === currentUser.id}
                        type={content.type}
                    />
                ))}
                {content.leaderboard.length > limit && (
                    <div className="mt-4 text-center text-sm text-slate-500">
                        Showing top {limit} of {content.leaderboard.length} students
                    </div>
                )}
            </main>
        </div>
    );
};

// ====================================================================
// KOMPONEN HALAMAN UTAMA (Sekarang menerima data dari props)
// ====================================================================

interface StudentProgressProps {
    contentLeaderboards: TContentLeaderboard[];
    limit?: number; // Batas jumlah item yang ditampilkan per konten
}

const StudentProgressPage = ({
    contentLeaderboards,
    limit = 10,
}: StudentProgressProps) => {
    return (
        <div className="">
            {contentLeaderboards.length > 0 ? (
                contentLeaderboards.map((content) => (
                    <ContentProgressBlock
                        key={content.id}
                        content={content}
                        limit={limit} // Menggunakan limit jika diberikan
                    />
                ))
            ) : (
                <div className="rounded-lg bg-white py-16 text-center shadow-md">
                    <h2 className="text-xl font-semibold text-slate-700">
                        No leaderboard data available
                    </h2>
                </div>
            )}
        </div>
    );
};

export default StudentProgressPage;
