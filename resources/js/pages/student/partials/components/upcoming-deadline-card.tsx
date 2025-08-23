import { Calendar } from 'lucide-react';

const UpcomingDeadlineCard = () => {
    return (
        <section className="flex flex-row gap-4 bg-white">
            <div className="h-12 w-12 rounded bg-secondary/30">
                <div className="flex h-full w-full items-center justify-center">
                    <Calendar className="h-6 w-6 text-secondary" />
                </div>
            </div>
            <div className="flex-1">
                <h3 className="text-md font-semibold">Final Project</h3>
                <p className="text-sm text-gray-500">Due in 3 days</p>
            </div>
        </section>
    );
};

export default UpcomingDeadlineCard;
