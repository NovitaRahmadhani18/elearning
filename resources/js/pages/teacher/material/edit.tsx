import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { EditMaterialForm } from './forms/edit-material-form';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Materials Management',
        href: route('teacher.materials.index'),
    },
    {
        title: 'Edit Material',
        href: '/teacher/materials/edit',
    },
];

export default function EditMaterial() {
    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Material" />
            <div className="">
                <EditMaterialForm />
            </div>
        </AdminTeacherLayout>
    );
}
