import { ActionButton } from '@/components/action-button';
import HeadingSmall from '@/components/heading-small';
import AdminTeacherLayout from '@/layouts/admin-teacher-layout';
import { ShowClassroomPageProps } from '@/pages/admin/classroom/types';
import AdminInviteClassroomDialog from '@/pages/admin/partials/components/admin-invite-classroom-dialog';
import { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';

import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import ClassroomShowStudents from './partials/classroom-show-students';

const ClassroomShowPage = () => {
    const { classroom } = usePage<ShowClassroomPageProps>().props;
    const [isOpen, setIsOpen] = useState(false);

    const breadcrumbs: BreadcrumbItem[] = useMemo(
        () => [
            {
                title: 'Classrooms Management',
                href: '/teacher/classrooms',
            },
            {
                title: classroom.data.fullName,
                href: route('teacher.classrooms.show', {
                    classroom: classroom.data.id,
                }),
            },
        ],
        [classroom],
    );

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

                <section>
                    <Tabs defaultValue="students" className="">
                        <TabsList>
                            <TabsTrigger value="students">Students</TabsTrigger>
                            <TabsTrigger value="contents">Contents</TabsTrigger>
                        </TabsList>
                        <TabsContent value="students">
                            <ClassroomShowStudents />
                        </TabsContent>
                        <TabsContent value="contents">
                            Change your password here.
                        </TabsContent>
                    </Tabs>
                </section>
            </div>
            <AdminInviteClassroomDialog
                isOpen={isOpen}
                onClose={() => setIsOpen(false)}
                key={classroom.data.id}
                classroom={classroom.data}
            />
        </AdminTeacherLayout>
    );
};

export default ClassroomShowPage;
