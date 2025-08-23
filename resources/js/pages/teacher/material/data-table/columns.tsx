import { ActionButton } from '@/components/action-button';
import { TContent } from '@/pages/teacher/material/types';
import { Link, router } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { toast } from 'sonner';

export const materialColumns: ColumnDef<TContent>[] = [
    {
        accessorKey: 'order',
        header: 'Order',
    },
    {
        accessorKey: 'title',
        header: 'Title',
    },
    {
        accessorKey: 'points',
        header: 'Points',
    },
    {
        accessorKey: 'created_at',
        header: 'Created At',
        cell: ({ row }) =>
            new Date(row.getValue('created_at')).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            }),
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => (
            <div className="flex items-center space-x-2">
                <Link href={route('materials.edit', row.original.id)}>
                    <ActionButton action="edit">
                        <span className="text-sm">Edit</span>
                    </ActionButton>
                </Link>
                <DeleteButton contentId={row.original.id} />
            </div>
        ),
    },
];

const DeleteButton = ({ contentId }: { contentId: number }) => {
    return (
        <ActionButton
            action="delete"
            onClick={() => {
                if (confirm('Are you sure you want to delete this material?')) {
                    router.delete(route('materials.destroy', contentId), {
                        preserveScroll: true,
                        onSuccess: () => toast.success('Material deleted successfully'),
                        onError: () => toast.error('Failed to delete material'),
                    });
                }
            }}
        >
            <span className="text-sm">Delete</span>
        </ActionButton>
    );
};
