import { ActionButton } from '@/components/action-button';
import HeadingSmall from '@/components/heading-small';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import ClassroomContentShow from '@/pages/teacher/classroom/partials/classroom-content.show';
import StudentClassroomTable from '@/pages/teacher/classroom/partials/table/student/data-table';
import { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AdminInviteClassroomDialog from '../partials/components/admin-invite-classroom-dialog';
import ShowRekap from './show-recap';
import { ShowClassroomPageProps } from './types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
    {
        title: 'Classrooms Management',
        href: '/admin/classrooms',
    },
    {
        title: 'Classroom Details',
        href: '/admin/classrooms/show',
    },
];

const ClassroomShowPage = () => {
    const { classroom } = usePage<ShowClassroomPageProps>().props;
    const [isOpen, setIsOpen] = useState(false);

    return (
        <AdminTeacherLayout breadcrumbs={breadcrumbs}>
            <Head title="Classrooms Management" />
            <div className="flex flex-1 flex-col gap-4">
                <section className="flex items-center justify-between">
                    <HeadingSmall
                        title={classroom.data.fullName}
                        description={`1 Students`}
                    />
                    <ActionButton action="create" onClick={() => setIsOpen(true)}>
                        Add Student
                    </ActionButton>
                </section>

                <section className="space-y-4">
                    <Tabs defaultValue="students" className="">
                        <TabsList>
                            <TabsTrigger value="students">Students</TabsTrigger>
                            <TabsTrigger value="contents">Contents</TabsTrigger>
                            <TabsTrigger value="recap">Recap</TabsTrigger>
                        </TabsList>
                        <TabsContent value="students">
                            <StudentClassroomTable />
                        </TabsContent>
                        <TabsContent value="contents">
                            <ClassroomContentShow />
                        </TabsContent>
                        <TabsContent value="recap">
                            <ShowRekap />
                        </TabsContent>
                    </Tabs>
                </section>
            </div>
            <AdminInviteClassroomDialog
                isOpen={isOpen}
                onClose={() => setIsOpen(false)}
                classroom={classroom.data}
            />
        </AdminTeacherLayout>
    );
};

export default ClassroomShowPage;
