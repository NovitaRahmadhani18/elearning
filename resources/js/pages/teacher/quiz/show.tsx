import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { ContentProgressBlock } from '@/pages/student/leaderboard/leaderboard';
import { TContentLeaderboard } from '@/pages/student/leaderboard/types';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Quizzes Management',
        href: route('teacher.quizzes.index'),
    },
    {
        title: 'Detail Quizzes',
        href: '/teacher/quizzes/show',
    },
];

interface QuizPageProps extends SharedData {
    quiz: {
        data: TContentLeaderboard;
    };
}

export default function ShowQuiz() {
    const { quiz } = usePage<QuizPageProps>().props;

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Detail Material" />
            <div className="">
                <ContentProgressBlock
                    content={quiz.data}
                    limit={quiz.data.leaderboard.length}
                />
                {/* <StudentQuizTable /> */}
            </div>
        </AdminTeacherLayout>
    );
}
