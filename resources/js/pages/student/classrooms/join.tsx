import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import StudentLayout from '@/layouts/student-layout';
import { useForm, usePage } from '@inertiajs/react';
import { toast } from 'sonner';
import { StudentClassroomJoinFormProps } from './types';

const ClassroomJoinPage = () => {
    const { classroom } = usePage<StudentClassroomJoinFormProps>().props;

    const { data, setData, post } = useForm({
        code: '',
    });

    return (
        <StudentLayout>
            <div className="flex flex-1 flex-col gap-4 space-y-4">
                <section className="flex flex-row gap-4 rounded-lg bg-white p-4 shadow-md">
                    <div className="h-28 w-28 rounded bg-secondary">
                        {classroom.data.thumbnail ? (
                            <img
                                src={classroom.data.thumbnail}
                                alt={classroom.data.fullName}
                                className="h-full w-full rounded object-cover"
                            />
                        ) : (
                            <div className="flex h-full w-full items-center justify-center text-white">
                                <span className="text-2xl font-bold">
                                    {classroom.data.fullName.charAt(0).toUpperCase()}
                                </span>
                            </div>
                        )}
                    </div>
                    <div className="flex-grow">
                        <h2 className="text-xl font-semibold text-gray-900">
                            {classroom.data.fullName}
                        </h2>
                        <p className="mb-4 text-sm text-gray-600">
                            Enter the classroom code to join.
                        </p>

                        <form
                            className="space-y-4"
                            onSubmit={(e) => {
                                e.preventDefault();
                                post(
                                    route(
                                        'student.classrooms.join.store',
                                        classroom.data.invite_code,
                                    ),
                                    {
                                        preserveScroll: true,
                                        onSuccess: () => {
                                            toast.success(
                                                'Successfully joined classroom',
                                            );
                                        },
                                        onError: (errors) => {
                                            console.error(errors);
                                            toast.error('Failed to join classroom');
                                        },
                                    },
                                );
                            }}
                        >
                            <Input
                                type="text"
                                name="code"
                                id="code"
                                required
                                placeholder="Secret code"
                                value={data.code}
                                onChange={(e) => setData('code', e.target.value)}
                                autoComplete="off"
                            />
                            <Button type="submit">Join Classroom</Button>
                        </form>
                    </div>
                </section>
            </div>
        </StudentLayout>
    );
};

export default ClassroomJoinPage;
