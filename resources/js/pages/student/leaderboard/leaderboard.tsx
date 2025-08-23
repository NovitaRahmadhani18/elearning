import { cn } from '@/lib/utils';
import { usePage } from '@inertiajs/react';

import { BookOpen, CheckCircle2, HelpCircle, Trophy } from 'lucide-react';
// import StudentLayout from '@/layouts/student-layout';

// ====================================================================
// TIPE DATA & MOCKUP
// ====================================================================

export type TUser = { id: number; name: string };
export type TContent = {
    id: number;
    title: string;
    type: 'material' | 'quiz';
    classroom: { name: string };
    points: number;
};

export type TStudentSubmission = {
    id: number;
    student: TUser;
    completed_at: string;
    time_spent_seconds?: number;
    points_awarded: number;
    score_percentage?: number;
    correct_answers?: number;
    total_questions?: number;
};

export type TProgressContent = TContent & {
    your_rank: number;
    total_students: number;
    submissions: TStudentSubmission[];
};

export const mockCurrentUser: TUser = { id: 1, name: 'Tanek' };

export const mockProgressData: TProgressContent[] = [
    {
        id: 101,
        title: 'Pengenalan Aljabar Dasar',
        type: 'material',
        classroom: { name: 'Matematika - 8B' },
        points: 10,
        your_rank: 2,
        total_students: 4,
        submissions: [
            {
                id: 1002,
                student: { id: 2, name: 'Budi Santoso' },
                completed_at: '2025-08-22T11:00:00Z',
                points_awarded: 10,
            },
            {
                id: 1001,
                student: mockCurrentUser,
                completed_at: '2025-08-22T10:00:00Z',
                points_awarded: 10,
            },
            {
                id: 1003,
                student: { id: 3, name: 'Cici Paramida' },
                completed_at: '2025-08-22T09:30:00Z',
                points_awarded: 10,
            },
            {
                id: 1004,
                student: { id: 4, name: 'Dewi Lestari' },
                completed_at: '2025-08-23T12:00:00Z',
                points_awarded: 10,
            },
        ],
    },
    {
        id: 102,
        title: 'Kuis Bab 1: Persamaan Linear',
        type: 'quiz',
        classroom: { name: 'Matematika - 8B' },
        points: 50,
        your_rank: 3,
        total_students: 5,
        submissions: [
            {
                id: 2001,
                student: { id: 3, name: 'Cici Paramida' },
                completed_at: '2025-08-23T14:00:00Z',
                time_spent_seconds: 120,
                points_awarded: 50,
                score_percentage: 100.0,
                correct_answers: 5,
                total_questions: 5,
            },
            {
                id: 2004,
                student: { id: 4, name: 'Dewi Lestari' },
                completed_at: '2025-08-23T14:01:00Z',
                time_spent_seconds: 150,
                points_awarded: 40,
                score_percentage: 80.0,
                correct_answers: 4,
                total_questions: 5,
            },
            {
                id: 2002,
                student: { id: 1, name: 'Tanek' },
                completed_at: '2025-08-23T14:05:00Z',
                time_spent_seconds: 240,
                points_awarded: 30,
                score_percentage: 60.0,
                correct_answers: 3,
                total_questions: 5,
            },
            {
                id: 2005,
                student: { id: 2, name: 'Budi Santoso' },
                completed_at: '2025-08-23T14:08:00Z',
                time_spent_seconds: 300,
                points_awarded: 20,
                score_percentage: 40.0,
                correct_answers: 2,
                total_questions: 5,
            },
            {
                id: 2006,
                student: { id: 5, name: 'Eko Prasetyo' },
                completed_at: '2025-08-23T14:02:00Z',
                time_spent_seconds: 180,
                points_awarded: 40,
                score_percentage: 80.0,
                correct_answers: 4,
                total_questions: 5,
            },
        ],
    },
];

// ====================================================================
// SUB-KOMPONEN
// ====================================================================

export const StudentSubmissionRow = ({
    submission,
    isCurrentUser,
    type,
    rank,
}: {
    submission: TStudentSubmission;
    isCurrentUser: boolean;
    type: 'material' | 'quiz';
    rank: number;
}) => {
    const rankStyles = {
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

    const styles = rankStyles[rank] || rankStyles.default;

    const formatTime = (totalSeconds: number = 0) => {
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
                <Trophy className={cn('h-6 w-6', styles.icon)} />
                <div>
                    <p className="font-bold text-gray-800">
                        {submission.student.name}
                        {isCurrentUser && (
                            <span className="ml-1 text-sm font-medium text-primary">
                                (You)
                            </span>
                        )}
                    </p>
                    <div className="flex items-center gap-2 text-xs text-gray-500">
                        <CheckCircle2 className="h-3 w-3 text-green-500" />
                        <span>Completed</span>
                        {type === 'quiz' && (
                            <span>
                                • {formatTime(submission.time_spent_seconds)}
                            </span>
                        )}
                    </div>
                </div>
            </div>
            <div className="text-right">
                {type === 'material' ? (
                    <>
                        <p className="text-lg font-bold text-green-600">
                            +{submission.points_awarded} pts
                        </p>
                        <p className="text-xs text-gray-500">Completed</p>
                    </>
                ) : (
                    <>
                        <p className="text-lg font-bold text-gray-800">
                            {submission.score_percentage?.toFixed(1)}%
                        </p>
                        <p className="text-xs text-gray-500">
                            {submission.correct_answers}/{submission.total_questions}{' '}
                            correct
                        </p>
                        <p className="text-xs text-green-600">
                            +{submission.points_awarded} pts
                        </p>
                    </>
                )}
            </div>
        </div>
    );
};

export const ContentProgressBlock = ({ content }: { content: TProgressContent }) => {
    const { auth } = usePage().props as any;
    const currentUser = auth.user || mockCurrentUser;

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
                {/* <div className="text-right"> */}
                {/*     <div className="flex items-center gap-1 rounded-md bg-yellow-100 px-2 py-1 font-semibold text-yellow-800"> */}
                {/*         <Trophy className="h-4 w-4" /> */}
                {/*         <span>Your Rank: #{content.your_rank}</span> */}
                {/*     </div> */}
                {/*     <p className="mt-1 text-xs text-slate-500"> */}
                {/*         of {content.total_students} students */}
                {/*     </p> */}
                {/* </div> */}
            </header>
            <main className="space-y-2 p-4">
                {content.submissions
                    .sort(
                        (a, b) =>
                            (b.score_percentage ?? b.points_awarded) -
                            (a.score_percentage ?? a.points_awarded),
                    )
                    .map((submission, index) => (
                        <StudentSubmissionRow
                            key={submission.id}
                            submission={submission}
                            // isCurrentUser={submission.student.id === currentUser.id}
                            isCurrentUser={false}
                            type={content.type}
                            rank={index + 1}
                        />
                    ))}
            </main>
        </div>
    );
};

// ====================================================================
// KOMPONEN HALAMAN UTAMA
// ====================================================================

const StudentProgressPage = () => {
    // Di aplikasi nyata, data ini akan datang dari usePage().props
    const progressData = mockProgressData;

    return (
        // <StudentLayout>
        <div className="">
            {progressData.map((content) => (
                <ContentProgressBlock key={content.id} content={content} />
            ))}
        </div>
        // </StudentLayout>
    );
};

export default StudentProgressPage;
