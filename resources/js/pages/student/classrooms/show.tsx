import StudentLayout from '@/layouts/student-layout';
import { Head, usePage } from '@inertiajs/react';

// Import Ikon
import { CheckCircle2, FileText, User } from 'lucide-react';

// Import Komponen UI
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { Separator } from '@/components/ui/separator';
import { TContent } from '@/pages/teacher/material/types';
import { useEffect } from 'react';
import { toast } from 'sonner';
import StudentMaterialCard from '../partials/components/student-material-card';
import StudentQuizCard from '../partials/components/student-quiz-card';
import { ShowStudentClassroomPageProps } from './types';

const ContentItem = ({ content }: { content: TContent }) => {
    if (content.type === 'material') {
        return <StudentMaterialCard content={content} />;
    }
    if (content.type === 'quiz') {
        return <StudentQuizCard content={content} />;
    }
    return null;
};

const ClassroomShow = () => {
    const {
        classroom: classroomData,
        contents: contentsData,
        errors,
    } = usePage<ShowStudentClassroomPageProps>().props;

    const classroom = classroomData.data;
    const contents = contentsData.data;

    const totalContents = contents.length;
    const completedContents = contents.filter(
        (c) => c.status === 'completed',
    ).length;
    const learningProgress =
        totalContents > 0 ? (completedContents / totalContents) * 100 : 0;

    useEffect(() => {
        if (errors && Object.keys(errors).length > 0) {
            Object.values(errors).forEach((error) => {
                toast.error(error as string);
            });
        }
    }, [errors]);

    return (
        <StudentLayout>
            <Head title={classroom.fullName} />
            <div className="container mx-auto bg-white p-8">
                {/* Header Classroom */}
                <div className="mb-6">
                    <h1 className="text-4xl font-extrabold text-slate-800">
                        {classroom.fullName}
                    </h1>
                    <div className="mt-2 flex items-center gap-4 text-slate-500">
                        <Badge variant="secondary">{classroom.category.name}</Badge>
                    </div>
                    <div className="mt-2 flex items-center text-slate-500">
                        <span>
                            {classroom.description || 'No description available.'}
                        </span>
                    </div>
                    <div className="mt-4 flex items-center gap-6 text-sm text-slate-600">
                        <span className="flex items-center gap-2">
                            <FileText className="h-4 w-4" /> {totalContents} Contents
                        </span>
                        <span className="flex items-center gap-2">
                            <CheckCircle2 className="h-4 w-4 text-green-500" />{' '}
                            {completedContents} Completed
                        </span>
                        <span className="flex items-center gap-2">
                            <User className="h-4 w-4" /> {classroom.teacher.name}
                        </span>
                    </div>
                </div>

                <div className="mb-8">
                    <div className="mb-1 flex items-center justify-between">
                        <span className="text-sm font-medium text-slate-600">
                            Learning Progress
                        </span>
                        <span className="text-sm font-bold">
                            {Math.round(learningProgress)}%
                        </span>
                    </div>
                    <Progress value={learningProgress} className="w-full" />
                </div>

                <Separator className="my-8" />

                {/* Daftar Konten */}
                <div className="space-y-6">
                    <div className="mb-4">
                        <h2 className="text-2xl font-bold text-slate-800">
                            Course Content
                        </h2>
                        <p className="text-slate-500">
                            Complete the materials and quizzes in order to progress
                            through the course.
                        </p>
                    </div>
                    <div className="flex flex-col gap-4">
                        {contents.map((content) => (
                            <ContentItem key={content.id} content={content} />
                        ))}
                    </div>
                </div>
            </div>
        </StudentLayout>
    );
};

export default ClassroomShow;
