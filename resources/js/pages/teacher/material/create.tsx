import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { CreateMaterialForm } from './forms/create-material-form';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Materials Management',
        href: route('teacher.materials.index'),
    },
    {
        title: 'Create Material',
        href: route('teacher.materials.create'),
    },
];

export default function CreateMaterial() {
    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Material" />
            <div className="">
                <CreateMaterialForm />
            </div>
        </AdminTeacherLayout>
    );
}
