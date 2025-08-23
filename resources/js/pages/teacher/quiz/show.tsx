import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import {
    ContentProgressBlock,
    mockProgressData,
} from '@/pages/student/leaderboard/leaderboard';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { TContentQuiz } from '../material/types';

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
        data: TContentQuiz;
    };
}

export default function ShowQuiz() {
    const { quiz } = usePage<QuizPageProps>().props;

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Detail Material" />
            <div className="">
                <ContentProgressBlock content={mockProgressData[1]} />
                {/* <StudentQuizTable /> */}
            </div>
        </AdminTeacherLayout>
    );
}
