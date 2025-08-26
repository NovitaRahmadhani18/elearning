import { cn } from '@/lib/utils';
import { ShowClassroomPageProps } from '@/pages/admin/classroom/types';
import { usePage } from '@inertiajs/react';
import { BookMarked, FileText, Star } from 'lucide-react';
import { TContent } from '../../material/types';

const ContentCard = ({ content }: { content: TContent }) => {
    const { classroom } = usePage<ShowClassroomPageProps>().props;

    return (
        <div
            className={cn(
                'flex flex-col overflow-hidden rounded-xl bg-white shadow-lg',
            )}
        >
            {/* Card Header */}
            <div className="relative h-32 bg-secondary/30 text-secondary">
                <div className="flex h-full w-full items-center justify-center">
                    <FileText className="h-12 w-12 opacity-70" />
                </div>
                <div className="absolute top-2 right-2 max-w-[50%] truncate rounded-full bg-secondary/40 px-2 py-1 text-xs font-semibold text-amber-800">
                    <BookMarked className="mr-1 inline h-3 w-3" />
                    {content.type === 'material' ? 'Material' : 'Quiz'}
                </div>
            </div>

            {/* Card Content */}
            <div className="flex flex-grow flex-col p-4">
                <h3 className="truncate text-lg font-bold" title={content.title}>
                    {content.title}
                </h3>
                <section className="mb-3 flex items-center gap-1">
                    <BookMarked className="h-3 w-3 text-gray-500" />
                    <p className="text-xs text-gray-400">
                        {classroom.data?.fullName}
                    </p>
                </section>

                {/* Stats Section */}
                <div className="my-2 grid grid-cols-1 gap-2 text-center text-sm text-gray-600">
                    {/* <div> */}
                    {/*     <p className="flex items-center justify-center gap-1 font-bold"> */}
                    {/*         <HelpCircle className="h-4 w-4 text-primary" /> */}
                    {/*     </p> */}
                    {/*     <p>Completed</p> */}
                    {/* </div> */}
                    <div>
                        <p className="flex items-center justify-center gap-1 font-bold">
                            <Star className="h-4 w-4 text-primary" />
                            {content.points || 0}
                        </p>
                        <p>Points</p>
                    </div>
                </div>

                <div className="flex-grow" />
            </div>
        </div>
    );
};

const ClassroomContentShow = () => {
    const { classroom } = usePage<ShowClassroomPageProps>().props;
    return (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            {classroom.data.contents && classroom.data.contents.length > 0 ? (
                classroom.data.contents.map((content) => (
                    <ContentCard key={content.id} content={content} />
                ))
            ) : (
                <p>No contents available.</p>
            )}
        </div>
    );
};

export default ClassroomContentShow;
