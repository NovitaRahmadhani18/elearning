import { TContentQuiz } from '@/pages/teacher/material/types';
import { Link } from '@inertiajs/react';
import { intlFormatDistance } from 'date-fns';
import { Calendar } from 'lucide-react';

interface UpcomingDeadlineCardProps {
    content: TContentQuiz; // Adjust the type based on your data structure
}

const UpcomingDeadlineCard = ({ content }: UpcomingDeadlineCardProps) => {
    return (
        <Link
            className="flex flex-row gap-4 bg-white"
            href={route('student.contents.show', content.id)}
        >
            <div className="h-12 w-12 rounded bg-secondary/30">
                <div className="flex h-full w-full items-center justify-center">
                    <Calendar className="h-6 w-6 text-secondary" />
                </div>
            </div>
            <div className="flex-1">
                <h3 className="text-md font-semibold">{content.title}</h3>
                <p className="text-sm text-gray-500">
                    {intlFormatDistance(
                        new Date(content.details.end_time),
                        new Date(),
                    )}
                </p>
            </div>
        </Link>
    );
};

export default UpcomingDeadlineCard;
