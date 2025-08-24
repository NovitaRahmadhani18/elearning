import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { ContentProgressBlock } from '@/pages/student/leaderboard/leaderboard';
import { TContentLeaderboard } from '@/pages/student/leaderboard/types';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

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
        data: TContentLeaderboard;
    };
}

export default function ShowMaterial() {
    const { material } = usePage<MaterialProps>().props;

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Detail Material" />
            <div className="">
                <ContentProgressBlock
                    content={material.data}
                    limit={material.data.leaderboard.length}
                />
            </div>
        </AdminTeacherLayout>
    );
}
