import { Progress } from '@/components/ui/progress';
import React from 'react';
import { TStudentClassroom } from '../../classrooms/types';

interface DashboardClassroomCardProps {
    classroom: TStudentClassroom;
}

const DashboardClassroomCard: React.FC<DashboardClassroomCardProps> = ({
    classroom,
}) => {
    return (
        <section className="flex flex-row gap-4 rounded-lg border bg-white p-4">
            <div className="h-16 w-16 rounded bg-secondary/30">
                <div className="flex h-full w-full items-center justify-center">
                    <span className="text-2xl font-bold text-secondary">C</span>
                </div>
            </div>
            <div className="flex-1 space-y-1">
                <h3 className="text-md font-semibold">{classroom.name}</h3>
                <p className="text-sm text-gray-500">
                    {classroom.contents?.length || 0} Lessons
                </p>
                <Progress value={50} className="h-2 w-full" />
            </div>
        </section>
    );
};

export default DashboardClassroomCard;
