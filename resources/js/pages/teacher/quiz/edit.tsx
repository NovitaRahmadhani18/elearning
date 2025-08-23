import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import EditQuizForm from './forms/edit-quiz';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Quizzes Management',
        href: '/teacher/quizzes',
    },
    {
        title: 'Edit Quiz',
        href: '/teacher/quizzes/edit',
    },
];

export default function EditQuiz() {
    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Quiz" />
            <div className="">
                <EditQuizForm />
            </div>
        </AdminTeacherLayout>
    );
}
