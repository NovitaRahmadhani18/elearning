import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import {
    ContentProgressBlock,
    mockProgressData,
} from '@/pages/student/leaderboard/leaderboard';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { TContentMaterial } from './types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Materials Management',
        href: route('teacher.materials.index'),
    },
    {
        title: 'Detail Material',
        href: '/teacher/materials/edit',
    },
];

interface MaterialProps extends SharedData {
    material: {
        data: TContentMaterial;
    };
}

export default function ShowMaterial() {
    const { material } = usePage<MaterialProps>().props;

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Detail Material" />
            <div className="">
                <ContentProgressBlock content={mockProgressData[0]} />
            </div>
        </AdminTeacherLayout>
    );
}
