import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import CreateQuizForm from './forms/create-quiz-form';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Quizzes Management',
        href: '/teacher/quizzes',
    },
    {
        title: 'Create Quiz',
        href: route('teacher.quizzes.create'),
    },
];

export default function CreateQuiz() {
    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Quiz" />
            <div className="">
                <CreateQuizForm />
            </div>
        </AdminTeacherLayout>
    );
}
