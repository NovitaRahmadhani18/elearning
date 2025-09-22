import { ActionButton } from '@/components/action-button';
import TablePagination from '@/components/data-table/table-pagination';
import HeadingSmall from '@/components/heading-small';
import { Input } from '@/components/ui/input';
import useDebouncedSearch from '@/hooks/use-debounce-search';
import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import QuizCard from '../partials/components/quiz-card';
import { QuizPageProps } from './types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Quizzes Management',
        href: '/teacher/quizzes',
    },
];

const QuizPage = () => {
    const { quizzes, filters, errors } = usePage<QuizPageProps>().props;

    const { setParams } = useDebouncedSearch(
        route(route().current() as string),
        filters,
        500,
    );
    console.log({ errors });

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Quizzes Management" />
            <div className="flex-1 flex-col gap-4 space-y-4 rounded-lg bg-white p-6 shadow-md">
                <section className="flex items-center justify-between">
                    <HeadingSmall
                        title="Quizzes Management"
                        description="Manage and organize your quizzes"
                    />
                    <Link href={route('teacher.quizzes.create')}>
                        <ActionButton action="create">Add New Quiz</ActionButton>
                    </Link>
                </section>

                <section>
                    <Input
                        className="max-w-md"
                        placeholder="Search quiz..."
                        type="text"
                        onChange={(e) => setParams({ search: e.target.value })}
                        aria-label="Search classrooms"
                        autoComplete="off"
                    />
                </section>
                <section>
                    {Object.keys(errors).length > 0 && (
                        <div className="rounded-md bg-red-50 p-4">
                            <div className="flex">
                                <div className="ml-3">
                                    <h3 className="text-sm font-medium text-red-800">
                                        {Object.values(errors).map(
                                            (error, index) => (
                                                <div key={index}>{error}</div>
                                            ),
                                        )}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    )}
                </section>

                <section className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {quizzes.data.length > 0 ? (
                        quizzes.data.map((quiz) => (
                            <QuizCard key={quiz.id} content={quiz} />
                        ))
                    ) : (
                        <p className="col-span-3 text-center text-gray-500">
                            No quiz found.
                        </p>
                    )}
                </section>

                {quizzes.data.length > 0 && (
                    <TablePagination links={quizzes.links} meta={quizzes.meta} />
                )}
            </div>
        </AdminTeacherLayout>
    );
};

export default QuizPage;
