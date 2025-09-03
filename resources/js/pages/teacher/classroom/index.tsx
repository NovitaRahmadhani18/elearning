import { ActionButton } from '@/components/action-button';
import TablePagination from '@/components/data-table/table-pagination';
import HeadingSmall from '@/components/heading-small';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import useDebouncedSearch from '@/hooks/use-debounce-search';
import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { TClassroom } from '@/pages/admin/classroom/types';
import AdminClassroomCard from '@/pages/admin/partials/components/admin-classroom-card';
import { BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { TeacherClassroomPageProps } from './types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Classrooms Management',
        href: '/teacher/classrooms',
    },
];

const ClassroomIndexPage = () => {
    const { classrooms, filters, categories } =
        usePage<TeacherClassroomPageProps>().props;

    const { setParams, params } = useDebouncedSearch(
        route(route().current() as string),
        filters,
        500,
    );

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Classrooms Management" />
            <div className="flex-1 flex-col gap-4 space-y-4">
                <section className="flex items-center justify-between">
                    <HeadingSmall
                        title="My Classrooms"
                        description="Manage your classrooms, add new ones, and track student progress."
                    />
                    <Link href={route('teacher.classrooms.create')}>
                        <ActionButton action="create">
                            Add New Classroom
                        </ActionButton>
                    </Link>
                </section>
                <section className="flex w-full justify-between">
                    <Input
                        className="max-w-md"
                        placeholder="Search classrooms..."
                        type="text"
                        // @ts-expect-error value bisa string atau number
                        value={params.search || ''}
                        onChange={(e) => {
                            setParams({
                                // @ts-expect-error value bisa string atau number
                                ...params,
                                search: e.target.value,
                            });
                        }}
                        aria-label="Search classrooms"
                        autoComplete="off"
                    />
                    <Select
                        onValueChange={(value) => {
                            // @ts-expect-error value bisa string atau number
                            setParams({ ...params, category: value });
                        }}
                        // @ts-expect-error value bisa string atau number
                        value={params.category || ''}
                    >
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Select filter" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="all">All</SelectItem>
                                {categories.length > 0 ? (
                                    categories.map((category) => (
                                        <SelectItem
                                            key={category.value}
                                            value={category.value}
                                        >
                                            {category.label}
                                        </SelectItem>
                                    ))
                                ) : (
                                    <p className="p-2 text-sm text-gray-500">
                                        No categories available.
                                    </p>
                                )}
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </section>
                <section className="grid auto-rows-min gap-6 md:grid-cols-3">
                    {classrooms.data.length > 0 ? (
                        classrooms.data.map((classroom: TClassroom) => (
                            <AdminClassroomCard
                                classroom={classroom}
                                key={classroom.id}
                                routeName="teacher.classrooms"
                            />
                        ))
                    ) : (
                        <p className="text-gray-500">No classrooms found.</p>
                    )}
                </section>

                {classrooms.data.length !== 0 && (
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
