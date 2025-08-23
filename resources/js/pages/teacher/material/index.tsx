import { ActionButton } from '@/components/action-button';
import TablePagination from '@/components/data-table/table-pagination';
import HeadingSmall from '@/components/heading-small';
import { Input } from '@/components/ui/input';
import useDebouncedSearch from '@/hooks/use-debounce-search';
import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import AdminDashboardCard from '@/pages/admin/partials/components/admin-dashboard-card';
import { BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { BookOpenText, ChartBar } from 'lucide-react';
import MaterialCard from '../partials/components/material-card';
import { MaterialPageProps } from './types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Materials Management',
        href: '/teacher/materials',
    },
];

const MaterialPage = () => {
    const { filters, materials } = usePage<MaterialPageProps>().props;

    const { setParams } = useDebouncedSearch(
        route(route().current() as string),
        filters,
        500,
    );

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title={`Materials Management`} />
            <div className="flex-1 flex-col gap-4 space-y-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <AdminDashboardCard
                        title="Daily Active Users"
                        value="0" // Placeholder value, replace with actual data if available'
                        icon={BookOpenText} // Replace with an actual icon if needed
                    />

                    <AdminDashboardCard
                        title="Course Completion Rate"
                        value="0" // Placeholder value, replace with actual data if available
                        icon={ChartBar} // Replace with an actual icon if needed
                    />
                </div>
                <section className="flex flex-col gap-4 rounded-lg bg-white p-6 shadow-md">
                    <section className="flex items-center justify-between">
                        <HeadingSmall
                            title="Materials Management"
                            description="Manage and organize your materials"
                        />
                        <Link href={route('teacher.materials.create')}>
                            <ActionButton action="create">
                                Add New Material
                            </ActionButton>
                        </Link>
                    </section>

                    <section>
                        <Input
                            className="max-w-md"
                            placeholder="Search materials..."
                            type="text"
                            onChange={(e) => setParams({ search: e.target.value })}
                            aria-label="Search classrooms"
                            autoComplete="off"
                        />
                    </section>
                    <section className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {materials.data.length > 0 ? (
                            materials.data.map((material) => (
                                <MaterialCard key={material.id} content={material} />
                            ))
                        ) : (
                            <p className="col-span-3 text-center text-gray-500">
                                No material found.
                            </p>
                        )}
                    </section>
                </section>

                {materials.data.length > 0 && (
                    <TablePagination links={materials.links} meta={materials.meta} />
                )}
            </div>
        </AdminTeacherLayout>
    );
};

export default MaterialPage;
