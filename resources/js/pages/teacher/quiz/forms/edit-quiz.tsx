import { useForm, usePage } from '@inertiajs/react';
import React, { useState } from 'react';
import { toast } from 'sonner';

import { DateTimePicker24hForm } from '@/components/form/date-picker';
import { FormField } from '@/components/form/form-field';
import { SelectInput } from '@/components/form/select-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { FileInput } from '@/components/ui/file-input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { PlusCircle, Trash2 } from 'lucide-react';
import { EditQuizPageProps, TAnswerState, TQuestionState } from '../types';

// --- FACTORIES ---
const createNewAnswer = (is_correct = false): TAnswerState => ({
    answer_text: '',
    image: null,
    is_correct,
});

const createNewQuestion = (): TQuestionState => ({
    question_text: '',
    image: null,
    answers: [createNewAnswer(true), createNewAnswer()],
});

interface AnswerFormProps {
    qIndex: number;
    aIndex: number;
    answer: TAnswerState;
    canBeRemoved: boolean;
    errors: Record<string, string>;
    onAnswerChange: (
        qIndex: number,
        aIndex: number,
        field: 'answer_text' | 'image',
        value: string | File | null,
    ) => void;
    onRemoveAnswer: (qIndex: number, aIndex: number) => void;
}

const AnswerForm = ({
    qIndex,
    aIndex,
    answer,
    canBeRemoved,
    errors,
    onAnswerChange,
    onRemoveAnswer,
}: AnswerFormProps) => (
    <div className="flex items-start gap-4 rounded border p-3">
        <RadioGroupItem value={aIndex.toString()} id={`q${qIndex}a${aIndex}`} />
        <div className="flex-1 space-y-2">
            <FormField
                id={`q${qIndex}a${aIndex}_text`}
                label={`Option ${aIndex + 1}`}
                value={answer.answer_text}
                onChange={(e) =>
                    onAnswerChange(qIndex, aIndex, 'answer_text', e.target.value)
                }
                placeholder={`Enter option text ${aIndex + 1}`}
                error={errors[`questions.${qIndex}.answers.${aIndex}.answer_text`]}
            />
            {answer._existingImage && !answer.image && (
                <img
                    src={answer._existingImage}
                    alt="Current answer image"
                    className="my-2 h-24 w-24 rounded-md object-cover"
                />
            )}
            <FileInput
                onChange={(file: File) =>
                    onAnswerChange(qIndex, aIndex, 'image', file)
                }
            />
            <p className="text-xs text-gray-500">
                Max file size: 2MB. Allowed formats: jpg, jpeg, png.
            </p>
            <InputError
                message={errors[`questions.${qIndex}.answers.${aIndex}.image`]}
            />
        </div>
        {canBeRemoved && (
            <Button
                type="button"
                variant="ghost"
                size="icon"
                className="h-8 w-8"
                onClick={() => onRemoveAnswer(qIndex, aIndex)}
            >
                <Trash2 className="h-4 w-4 text-destructive" />
            </Button>
        )}
    </div>
);

// ====================================================================
// SUB-KOMPONEN 2: QuestionForm
// ====================================================================
interface QuestionFormProps {
    qIndex: number;
    question: TQuestionState;
    canBeRemoved: boolean;
    errors: Record<string, string>;
    onQuestionChange: (
        qIndex: number,
        field: 'question_text' | 'image',
        value: string | File | null,
    ) => void;
    onRemoveQuestion: (qIndex: number) => void;
    onAddAnswer: (qIndex: number) => void;
    onRemoveAnswer: (qIndex: number, aIndex: number) => void;
    onAnswerChange: (
        qIndex: number,
        aIndex: number,
        field: 'answer_text' | 'image',
        value: string | File | null,
    ) => void;
    onSetCorrectAnswer: (qIndex: number, aIndex: number) => void;
}

const QuestionForm = ({
    qIndex,
    question,
    canBeRemoved,
    errors,
    onQuestionChange,
    onRemoveQuestion,
    onAddAnswer,
    onRemoveAnswer,
    onAnswerChange,
    onSetCorrectAnswer,
}: QuestionFormProps) => {
    const correctAnswerIndex = question.answers.findIndex((ans) => ans.is_correct);

    return (
        <div className="relative space-y-4 rounded-lg border bg-white p-6">
            <FormField
                id={`q_${qIndex}_text`}
                label={`Question ${qIndex + 1}`}
                value={question.question_text}
                onChange={(e) =>
                    onQuestionChange(qIndex, 'question_text', e.target.value)
                }
                placeholder="Enter question text"
                error={errors[`questions.${qIndex}.question_text`]}
            />
            <div>
                <Label>Question Image (Optional)</Label>
                {question._existingImage && !question.image && (
                    <img
                        src={question._existingImage}
                        alt="Current question image"
                        className="my-2 h-32 w-32 rounded-md object-cover"
                    />
                )}
                <FileInput
                    onChange={(file: File) =>
                        onQuestionChange(qIndex, 'image', file)
                    }
                />
                <p className="text-xs text-gray-500">
                    Max file size: 2MB. Allowed formats: jpg, jpeg, png.
                </p>
                <InputError message={errors[`questions.${qIndex}.image`]} />
            </div>
            <h3 className="text-md pt-2 font-semibold">Options</h3>
            <RadioGroup
                onValueChange={(val) => onSetCorrectAnswer(qIndex, parseInt(val))}
                value={
                    correctAnswerIndex !== -1
                        ? correctAnswerIndex.toString()
                        : undefined
                }
                className="space-y-2"
            >
                {question.answers.map((answer, aIndex) => (
                    <AnswerForm
                        key={answer.id || aIndex} // Gunakan ID jika ada, untuk stabilitas render
                        qIndex={qIndex}
                        aIndex={aIndex}
                        answer={answer}
                        canBeRemoved={question.answers.length > 2}
                        errors={errors}
                        onAnswerChange={onAnswerChange}
                        onRemoveAnswer={onRemoveAnswer}
                    />
                ))}
            </RadioGroup>
            <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={() => onAddAnswer(qIndex)}
                className="mt-2"
            >
                <PlusCircle className="mr-2 h-4 w-4" /> Add Option
            </Button>
            {canBeRemoved && (
                <Button
                    type="button"
                    variant="destructive"
                    size="sm"
                    onClick={() => onRemoveQuestion(qIndex)}
                    className="absolute top-4 right-6"
                >
                    Remove Question
                </Button>
            )}
        </div>
    );
};

// ====================================================================
// KOMPONEN UTAMA: EditQuizForm
// ====================================================================
const EditQuizForm = () => {
    const { quiz: quizData, classrooms } = usePage<EditQuizPageProps>().props;

    const quiz = quizData.data;

    // Logika untuk menentukan minimal date untuk start_time
    const originalStartTime = new Date(quiz.details.start_time);
    const today = new Date();

    // Jika start_time asli > hari ini (belum lewat), gunakan hari ini sebagai minimal
    // Jika start_time asli <= hari ini (sudah lewat), gunakan start_time asli sebagai minimal
    const minStartDate = originalStartTime > today ? today : originalStartTime;

    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT',
        title: quiz.title,
        description: quiz.description || '',
        classroom_id: quiz.classroom_id.toString(),
        points: quiz.points,
        start_time: new Date(quiz.details.start_time),
        end_time: quiz.details.end_time ? new Date(quiz.details.end_time) : null,
        duration_minutes: quiz.details.duration_minutes,
        questions: quiz.details.questions.map((q) => ({
            id: q.id,
            question_text: q.question_text,
            image: null,
            _existingImage: q.image_path,
            answers: q.answers.map((a) => ({
                id: a.id,
                answer_text: a.answer_text,
                image: null,
                _existingImage: a.image_path,
                is_correct: a.is_correct,
            })),
        })),
    });

    const [timeLimitOption, setTimeLimitOption] = useState(() => {
        const customValues = [5, 10, 30, 60];
        return customValues.includes(data.duration_minutes)
            ? data.duration_minutes.toString()
            : 'custom';
    });

    const handleTimeLimitChange = (value: string) => {
        setTimeLimitOption(value);
        if (value !== 'custom') {
            setData('duration_minutes', parseInt(value));
        }
    };

    const addQuestion = () =>
        setData('questions', [...data.questions, createNewQuestion()]);
    const removeQuestion = (qIndex: number) =>
        setData(
            'questions',
            data.questions.filter((_, index) => index !== qIndex),
        );
    const handleQuestionChange = (
        qIndex: number,
        field: keyof TQuestionState,
        value: any,
    ) => {
        setData(
            'questions',
            data.questions.map((q, index) =>
                index === qIndex ? { ...q, [field]: value } : q,
            ),
        );
    };
    const addAnswer = (qIndex: number) => {
        const updatedQuestions = [...data.questions];
        if (updatedQuestions[qIndex].answers.length < 5) {
            updatedQuestions[qIndex].answers.push(createNewAnswer());
            setData('questions', updatedQuestions);
        }
    };
    const removeAnswer = (qIndex: number, aIndex: number) => {
        const updatedQuestions = [...data.questions];
        updatedQuestions[qIndex].answers = updatedQuestions[qIndex].answers.filter(
            (_, index) => index !== aIndex,
        );
        setData('questions', updatedQuestions);
    };
    const handleAnswerChange = (
        qIndex: number,
        aIndex: number,
        field: keyof TAnswerState,
        value: any,
    ) => {
        const updatedQuestions = [...data.questions];
        updatedQuestions[qIndex].answers[aIndex] = {
            ...updatedQuestions[qIndex].answers[aIndex],
            [field]: value,
        };
        setData('questions', updatedQuestions);
    };
    const setCorrectAnswer = (qIndex: number, aIndex: number) => {
        const updatedQuestions = [...data.questions];
        updatedQuestions[qIndex].answers = updatedQuestions[qIndex].answers.map(
            (ans, index) => ({ ...ans, is_correct: index === aIndex }),
        );
        setData('questions', updatedQuestions);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('teacher.quizzes.update', quiz.id), {
            onSuccess: () => toast.success('Quiz updated successfully!'),
            onError: (errs) => {
                console.error(errs);
                toast.error('Failed to update quiz. Please check for errors.');
            },
            preserveState: true,
        });
    };

    return (
        <form
            onSubmit={handleSubmit}
            className="grid grid-cols-1 gap-8 lg:grid-cols-3"
        >
            <div className="space-y-6 lg:col-span-2">
                <div className="rounded-lg border bg-white p-6">
                    <FormField
                        id="title"
                        label="Quiz Title"
                        value={data.title}
                        onChange={(e) => setData('title', e.target.value)}
                        placeholder="Enter the quiz title"
                        error={errors.title}
                        required
                    />
                    <FormField
                        id="description"
                        label="Description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder="Enter quiz description"
                        textarea
                        error={errors.description}
                        className="mt-4"
                    />
                </div>
                <div className="space-y-4">
                    <h2 className="text-xl font-bold">Questions</h2>
                    {data.questions.map((question, qIndex) => (
                        <QuestionForm
                            key={question.id || qIndex} // Gunakan ID jika ada, untuk stabilitas render
                            qIndex={qIndex}
                            question={question}
                            canBeRemoved={data.questions.length > 1}
                            errors={errors}
                            onQuestionChange={handleQuestionChange}
                            onRemoveQuestion={removeQuestion}
                            onAddAnswer={addAnswer}
                            onRemoveAnswer={removeAnswer}
                            onAnswerChange={handleAnswerChange}
                            onSetCorrectAnswer={setCorrectAnswer}
                        />
                    ))}
                    <Button
                        type="button"
                        variant="secondary"
                        onClick={addQuestion}
                        className="w-full"
                    >
                        <PlusCircle className="mr-2 h-4 w-4" /> Add Question
                    </Button>
                </div>
            </div>

            <div className="lg:col-span-1">
                <div className="sticky top-4 space-y-4 rounded-lg border bg-white p-6">
                    <h2 className="text-xl font-bold">Quiz Settings</h2>
                    <SelectInput
                        id="classroom_id"
                        label="Select Course"
                        placeholder="Select a course"
                        value={data.classroom_id}
                        disabled // Umumnya kelas tidak diubah saat edit
                        onChange={() => {}}
                        options={classrooms.data.map((c) => ({
                            value: c.id.toString(),
                            label: c.name,
                        }))}
                        error={errors.classroom_id}
                    />
                    <div className="space-y-2">
                        <SelectInput
                            id="time_limit_option"
                            label="Time Limit"
                            value={timeLimitOption}
                            onChange={handleTimeLimitChange}
                            options={[
                                { value: '5', label: '5 minutes' },
                                { value: '10', label: '10 minutes' },
                                { value: '30', label: '30 minutes' },
                                { value: '60', label: '60 minutes' },
                                { value: 'custom', label: 'Custom time...' },
                            ]}
                        />
                        {timeLimitOption === 'custom' && (
                            <FormField
                                id="duration_minutes"
                                label="Custom Duration (minutes)"
                                type="number"
                                value={data.duration_minutes.toString()}
                                onChange={(e) =>
                                    setData(
                                        'duration_minutes',
                                        parseInt(e.target.value) || 0,
                                    )
                                }
                                placeholder="Enter total minutes"
                                error={errors.duration_minutes}
                                required
                            />
                        )}
                    </div>
                    <div className="rounded-md border border-red-500 bg-red-100 p-2">
                        <p className="text-xs text-red-700">
                            Make sure the start and due times are set correctly to
                            avoid confusion for students.
                        </p>
                    </div>
                    <DateTimePicker24hForm
                        id="start_time"
                        label="Start Time"
                        value={data.start_time}
                        error={errors.start_time}
                        onChange={(date) => setData('start_time', date as Date)}
                        minDate={minStartDate}
                    />
                    <DateTimePicker24hForm
                        id="end_time"
                        label="Due Time"
                        value={data.end_time}
                        error={errors.end_time}
                        onChange={(date) => setData('end_time', date)}
                        minDate={data.start_time}
                    />
                    <FormField
                        id="points"
                        label="Total Point"
                        type="number"
                        value={data.points.toString()}
                        onChange={(e) => setData('points', parseInt(e.target.value))}
                        error={errors.points}
                    />
                    <div className="flex items-center justify-end gap-2 border-t pt-4">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => window.history.back()}
                            disabled={processing}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saving...' : 'Save Changes'}
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    );
};

export default EditQuizForm;
