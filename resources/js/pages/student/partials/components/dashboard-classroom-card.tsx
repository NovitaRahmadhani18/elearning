import { Progress } from '@/components/ui/progress';
import { Link } from '@inertiajs/react';
import React from 'react';
import { TStudentClassroom } from '../../classrooms/types';

interface DashboardClassroomCardProps {
    classroom: TStudentClassroom;
}

const DashboardClassroomCard: React.FC<DashboardClassroomCardProps> = ({
    classroom,
}) => {
    return (
        <Link
            className="flex flex-row gap-4 rounded-lg border bg-white p-4"
            href={route('student.classrooms.show', classroom.id)}
        >
            <div className="h-16 w-16 rounded bg-secondary/30">
                <div className="flex h-full w-full items-center justify-center">
                    {classroom.thumbnail ? (
                        <img
                            src={classroom.thumbnail}
                            alt={classroom.name}
                            className="h-full w-full rounded object-cover"
                        />
                    ) : (
                        <span className="text-2xl font-bold text-secondary">
                            {classroom.name.charAt(0).toUpperCase()}
                        </span>
                    )}
                </div>
            </div>
            <div className="flex-1 space-y-1">
                <h3 className="text-md font-semibold">{classroom.fullName}</h3>
                <p className="text-sm text-gray-500">
                    {classroom.contents?.length || 0} Lessons
                </p>
                <div className="flex items-center justify-between">
                    <span className="text-xs text-gray-400">Progress</span>
                    <span className="text-xs font-semibold text-gray-600">
                        {classroom.progress || 0}% Complete
                    </span>
                </div>
                <Progress value={classroom.progress} className="h-2 w-full" />
            </div>
        </Link>
    );
};

export default DashboardClassroomCard;
