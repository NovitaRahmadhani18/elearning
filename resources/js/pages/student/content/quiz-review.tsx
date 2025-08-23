import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { cn } from '@/lib/utils';
import { TContentQuiz } from '@/pages/teacher/material/types';
import { TAnswer, TQuestion } from '@/pages/teacher/quiz/types';
import { SharedData } from '@/types';
import { router } from '@inertiajs/react';
import {
    BookCopy,
    CheckCircle,
    ChevronLeft,
    ChevronRight,
    XCircle,
} from 'lucide-react';
import React, { useState } from 'react';
import { TQuizSubmission } from './types';

interface ReviewQuizProps extends SharedData {
    content: {
        data: TContentQuiz;
    };
    quizSubmission: {
        data: TQuizSubmission;
    };
}

const AnswerOption: React.FC<{ answer: TAnswer; status: string }> = ({
    answer,
    status,
}) => {
    const isCorrect = status === 'correct';
    const isIncorrect = status === 'incorrect';
    const isMissedCorrect = status === 'missed_correct';

    return (
        <div
            className={cn(
                'flex items-start space-x-4 rounded-xl border-2 p-4 transition-all duration-300',
                {
                    'border-primary bg-primary/10 font-semibold text-primary shadow-lg':
                        isCorrect,
                    'border-destructive bg-destructive/10 font-semibold text-destructive shadow-lg':
                        isIncorrect,
                    'border-dashed border-primary/80 bg-primary/5': isMissedCorrect,
                    'border-border bg-card hover:border-primary/50':
                        status === 'unselected',
                },
            )}
        >
            <div className="mt-1 flex-shrink-0">
                {isCorrect ? (
                    <CheckCircle className="h-6 w-6 text-primary" />
                ) : isIncorrect ? (
                    <XCircle className="h-6 w-6 text-destructive" />
                ) : isMissedCorrect ? (
                    <CheckCircle className="h-6 w-6 text-primary/80" />
                ) : (
                    <div className="h-6 w-6 rounded-full border-2 border-muted-foreground/50"></div>
                )}
            </div>
            <div
                className="flex-1"
                dangerouslySetInnerHTML={{ __html: answer.answer_text }}
            />
            <div className="flex-shrink-0">
                {isIncorrect && (
                    <Badge variant="destructive" className="font-bold">
                        Your Answer
                    </Badge>
                )}
                {isCorrect && (
                    <Badge className="border-primary bg-primary font-bold text-primary-foreground hover:bg-primary/90">
                        Correct
                    </Badge>
                )}
                {isMissedCorrect && (
                    <Badge
                        variant="outline"
                        className="border-primary/80 text-primary/80"
                    >
                        Correct Answer
                    </Badge>
                )}
            </div>
        </div>
    );
};

const QuestionReviewCard: React.FC<{
    question: TQuestion;
    studentAnswerId?: number;
}> = ({ question, studentAnswerId }) => {
    const getAnswerStatus = (
        answer: TAnswer,
    ): 'correct' | 'incorrect' | 'unselected' | 'missed_correct' => {
        const isSelected = answer.id === studentAnswerId;
        const isCorrect = !!answer.is_correct;

        if (isSelected && isCorrect) return 'correct';
        if (isSelected && !isCorrect) return 'incorrect';
        if (!isSelected && isCorrect) return 'missed_correct';
        return 'unselected';
    };

    return (
        <Card className="shadow-xl">
            <CardHeader>
                <CardTitle
                    className="text-xl leading-relaxed font-bold"
                    dangerouslySetInnerHTML={{ __html: question.question_text }}
                />
            </CardHeader>
            <CardContent>
                <div className="space-y-4">
                    {question.answers.map((answer) => (
                        <AnswerOption
                            key={answer.id}
                            answer={answer}
                            status={getAnswerStatus(answer)}
                        />
                    ))}
                </div>
            </CardContent>
        </Card>
    );
};

const ReviewQuiz: React.FC<ReviewQuizProps> = ({ content, quizSubmission }) => {
    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
    const questions = content.data.details.questions;
    const totalQuestions = questions.length;
    const progress = ((currentQuestionIndex + 1) / totalQuestions) * 100;

    const currentQuestion = questions[currentQuestionIndex];
    const studentAnswer = quizSubmission.data.submitted_answers.find(
        (sa) => sa.question_id === currentQuestion.id,
    );

    const handleNext = () => {
        if (currentQuestionIndex < totalQuestions - 1) {
            setCurrentQuestionIndex((prev) =>
                Math.min(prev + 1, totalQuestions - 1),
            );
        } else {
            router.visit(
                route('student.classrooms.show', content.data.classroom.id),
            );
        }
    };

    const handlePrevious = () => {
        setCurrentQuestionIndex((prev) => Math.max(prev - 1, 0));
    };

    return (
        <div
            className="min-h-screen bg-muted/20 py-12"
            style={{ backgroundImage: "url('/pattern.png')" }}
        >
            <div className="mx-auto max-w-4xl space-y-4">
                <header className="mb-6 flex items-center justify-between rounded-lg bg-white p-6 shadow-md">
                    <div className="flex items-center gap-3">
                        <BookCopy className="h-8 w-8 text-slate-400" />
                        <div>
                            <h1 className="text-xl font-bold">
                                {content.data.title}
                            </h1>
                            <p className="text-sm text-slate-400">
                                {content.data.classroom.name}
                            </p>
                        </div>
                    </div>
                    <div className="flex flex-col gap-2 rounded-lg bg-green-600 px-3 py-1.5 text-white">
                        <span className="text-xs">Correct Answer</span>
                        <div className="text-lg leading-none font-semibold">
                            <span>
                                {quizSubmission.data.correct_answers_count} /{' '}
                            </span>
                            <span>{totalQuestions}</span>
                        </div>
                    </div>
                </header>

                <div>
                    <div className="mb-3 flex items-center justify-between px-1">
                        <p className="text-base font-semibold text-foreground">
                            Question {currentQuestionIndex + 1}
                            <span className="text-sm font-medium text-muted-foreground">
                                {' '}
                                / {totalQuestions}
                            </span>
                        </p>
                    </div>
                    <Progress
                        value={progress}
                        className="h-3 [&>div]:bg-gradient-to-r [&>div]:from-secondary [&>div]:to-amber-400"
                    />
                </div>

                <QuestionReviewCard
                    question={currentQuestion}
                    studentAnswerId={studentAnswer?.answer_id}
                />

                <div className="flex items-center justify-between bg-card p-2 shadow-lg">
                    <Button
                        onClick={handlePrevious}
                        disabled={currentQuestionIndex === 0}
                        variant="ghost"
                        className="px-8 py-6 text-lg font-bold"
                    >
                        <ChevronLeft className="mr-2 h-5 w-5" />
                        Back
                    </Button>
                    <Button
                        onClick={handleNext}
                        className="bg-gradient-to-r from-primary to-teal-500 px-8 py-6 text-lg font-bold text-primary-foreground shadow-md transition-all hover:shadow-xl"
                    >
                        {currentQuestionIndex === totalQuestions - 1
                            ? 'Back to Classroom'
                            : 'Next'}
                        <ChevronRight className="ml-2 h-5 w-5" />
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default ReviewQuiz;
