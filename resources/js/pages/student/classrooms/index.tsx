import Heading from '@/components/heading';
import StudentLayout from '@/layouts/student-layout';
import { usePage } from '@inertiajs/react';
import FeaturedClassroomCard from '../partials/components/featured-classroom-card';
import StudentClassroomCard from '../partials/components/student-classroom-card';
import { StudentClassroomPageProps } from './types';

const ClassroomPage = () => {
    const { classrooms } = usePage<StudentClassroomPageProps>().props;

    const orderedClassrooms = classrooms.data.sort((a, b) => {
        const progressA = a.progress || 0;
        const progressB = b.progress || 0;
        return progressA - progressB; // Sort descending by progress
    });

    return (
        <StudentLayout>
            <div className="flex flex-1 flex-col gap-4 space-y-4">
                <Heading
                    title="My Classrooms"
                    description="Continue your learning journey."
                />

                <section>
                    {orderedClassrooms.length > 0 && (
                        <FeaturedClassroomCard classroom={orderedClassrooms[0]} />
                    )}
                </section>

                <section className="grid auto-rows-min gap-6 md:grid-cols-3">
                    {orderedClassrooms.map((classroom, index) => (
                        <StudentClassroomCard
                            classroom={classroom}
                            key={index}
                            progress={
                                classroom.progress || 0 // Ensure progress is defined
                            } // Simulated progress, replace with actual data if available
                        />
                    ))}
                </section>
            </div>
        </StudentLayout>
    );
};

export default ClassroomPage;
