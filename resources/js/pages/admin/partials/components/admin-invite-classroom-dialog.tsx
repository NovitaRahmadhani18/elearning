import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { router } from '@inertiajs/react';
import { Repeat } from 'lucide-react';
import { toast } from 'sonner';
import { TClassroom } from '../../classroom/types';

interface AdminInviteClassroomDialogProps {
    isOpen: boolean;
    onClose: () => void;
    classroom: TClassroom;
}

const AdminInviteClassroomDialog: React.FC<AdminInviteClassroomDialogProps> = ({
    isOpen,
    onClose,
    classroom,
}) => {
    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className="max-w-2xl md:max-w-3xl">
                <DialogHeader>
                    <DialogTitle className="text-lg font-semibold">
                        Share Classroom {classroom.fullName} link
                    </DialogTitle>
                    <DialogDescription>
                        You can share this link with students to invite them to the
                        classroom.
                    </DialogDescription>
                </DialogHeader>

                <section className="flex flex-col gap-4">
                    <div className="flex flex-col gap-1">
                        <Label className="">Invitation Link</Label>
                        <div className="flex flex-col items-center justify-between gap-1 md:flex-row">
                            <div className="w-full rounded-md border border-primary p-2 text-sm">
                                {route(
                                    'student.classrooms.join.form',
                                    classroom.invite_code,
                                )}
                            </div>

                            <Button
                                id="copy-secret-code"
                                variant="outline"
                                onClick={(e) => {
                                    router.post(
                                        route(
                                            'classrooms.generate-invite-code',
                                            classroom.id,
                                        ),
                                        {},
                                        {
                                            onBefore: () =>
                                                confirm(
                                                    'Are you sure you want to regenerate the link?',
                                                ),
                                        },
                                    );
                                }}
                            >
                                <Repeat className="h-4 w-4" />
                                Regenerate Link
                            </Button>
                            <Button
                                id="copy-invite-link"
                                className="text-white"
                                onClick={(e) => {
                                    navigator.clipboard.writeText(
                                        route(
                                            'student.classrooms.join.form',
                                            classroom.invite_code,
                                        ),
                                    );
                                    toast.success(
                                        'Invite link copied to clipboard!',
                                    );
                                    // change text to "Copied!" for a few seconds
                                    e.currentTarget.textContent = 'Copied!';
                                }}
                            >
                                Copy Link
                            </Button>
                        </div>
                    </div>
                    <div className="flex flex-col gap-1">
                        <Label className="">Secret Code</Label>
                        <div className="flex flex-col items-center justify-between gap-1 md:flex-row">
                            <div className="w-full rounded-md border border-primary p-2 text-sm">
                                {classroom.code}
                            </div>
                            <Button
                                id="copy-secret-code"
                                variant="outline"
                                onClick={(e) => {
                                    router.post(
                                        route(
                                            'classrooms.generate-code',
                                            classroom.id,
                                        ),
                                        {},
                                        {
                                            onBefore: () =>
                                                confirm(
                                                    'Are you sure you want to regenerate the code?',
                                                ),
                                        },
                                    );
                                }}
                            >
                                <Repeat className="h-4 w-4" />
                                Regenerate Code
                            </Button>

                            <Button
                                id="copy-invite-code"
                                className="text-white"
                                onClick={(e) => {
                                    navigator.clipboard.writeText(classroom.code);
                                    toast.success('code copied to clipboard!');
                                    // change text to "Copied!" for a few seconds
                                    e.currentTarget.textContent = 'Copied!';
                                }}
                            >
                                Copy Code
                            </Button>
                        </div>
                        <span className="text-xs text-gray-500">
                            Students can use this code to join the classroom
                        </span>
                    </div>
                </section>
            </DialogContent>
        </Dialog>
    );
};

export default AdminInviteClassroomDialog;
