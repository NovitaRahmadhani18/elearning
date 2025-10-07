import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SharedData } from '@/types';
import { Form, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';

export function JoinClassroomDialog() {
    const { errors: errorData } = usePage<SharedData>().props;

    return (
        <Dialog
            onOpenChange={(open) => {
                if (!open) {
                    if (errorData) {
                        for (const key in errorData) {
                            if (
                                Object.prototype.hasOwnProperty.call(errorData, key)
                            ) {
                                delete errorData[key];
                            }
                        }
                    }
                }
            }}
        >
            <DialogTrigger asChild>
                <div className="flex w-full justify-end">
                    <Button type="button">
                        <Plus />
                        Join Classroom
                    </Button>
                </div>
            </DialogTrigger>

            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Join Classroom</DialogTitle>
                    <DialogDescription>
                        Ask your teacher for the class code, then enter it here.
                    </DialogDescription>
                </DialogHeader>

                <Form method="post" action={route('student.classrooms.direct-join')}>
                    {({ processing }) => (
                        <>
                            {Object.keys(errorData).length > 0 && (
                                // display simple error message
                                <div className="mb-4 rounded-lg bg-red-50 p-4">
                                    <div className="flex">
                                        <div className="ml-3">
                                            <h3 className="text-sm font-medium text-red-800">
                                                {Object.values(errorData).map(
                                                    (error, index) => (
                                                        <div key={index}>
                                                            {error}
                                                        </div>
                                                    ),
                                                )}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            )}
                            <div className="grid gap-4">
                                <div className="grid gap-3">
                                    <Label htmlFor="classroom_code">
                                        Classroom code
                                    </Label>
                                    <Input
                                        id="classroom-code"
                                        name="classroom_code"
                                    />
                                </div>
                            </div>
                            <DialogFooter className="mt-4">
                                <DialogClose asChild>
                                    <Button variant="outline" disabled={processing}>
                                        Cancel
                                    </Button>
                                </DialogClose>
                                <Button type="submit" disabled={processing}>
                                    Join
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
