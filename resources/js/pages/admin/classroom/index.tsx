import { ActionButton } from '@/components/action-button';
import TablePagination from '@/components/data-table/table-pagination';
import HeadingSmall from '@/components/heading-small';
import { Input } from '@/components/ui/input';
import useDebouncedSearch from '@/hooks/use-debounce-search';
import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import AdminClassroomCard from '../partials/components/admin-classroom-card';
import { ClassroomIndexPageProps, TClassroom } from './types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
    {
        title: 'Classrooms Management',
        href: '/admin/classrooms',
    },
];

const ClassroomIndexPage = () => {
    const { classrooms, filters } = usePage<ClassroomIndexPageProps>().props;

    const { setParams } = useDebouncedSearch(
        route(route().current() as string),
        filters,
        500,
    );

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Classrooms Management" />
            <div className="flex flex-1 flex-col gap-4">
                <section className="flex items-center justify-between">
                    <HeadingSmall
                        title="Classrooms Management"
                        description="Manage and organize your classrooms"
                    />
                    <Link href={route('admin.classrooms.create')}>
                        <ActionButton action="create">
                            Add New Classroom
                        </ActionButton>
                    </Link>
                </section>
                <section>
                    <Input
                        className="max-w-md"
                        placeholder="Search classrooms..."
                        type="text"
                        onChange={(e) => setParams({ search: e.target.value })}
                        aria-label="Search classrooms"
                        autoComplete="off"
                    />
                </section>
                <section className="grid auto-rows-min gap-6 md:grid-cols-3">
                    {classrooms.data.length > 0 ? (
                        classrooms.data.map((classroom: TClassroom) => (
                            <AdminClassroomCard
                                classroom={classroom}
                                key={classroom.id}
                            />
                        ))
                    ) : (
                        <p className="col-span-3 text-center text-gray-500">
                            No classrooms found.
                        </p>
                    )}
                </section>
                {classrooms.data.length > 0 && (
                    <TablePagination
                        links={classrooms.links}
                        meta={classrooms.meta}
                    />
                )}
            </div>
        </AdminTeacherLayout>
    );
};

export default ClassroomIndexPage;
