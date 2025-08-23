import { cn } from '@/lib/utils';
import { Slot } from '@radix-ui/react-slot';
import { VariantProps } from 'class-variance-authority';
import { PencilIcon, PlusIcon, TrashIcon } from 'lucide-react';
import React from 'react';
import { buttonVariants } from './ui/button';

type ActionType = 'create' | 'edit' | 'delete';

interface ActionButtonProps
    extends React.ButtonHTMLAttributes<HTMLButtonElement>,
        VariantProps<typeof buttonVariants> {
    action: ActionType;
    icon?: React.ReactNode;
    asChild?: boolean;
}

const mapVariant: Record<
    ActionType,
    VariantProps<typeof buttonVariants>['variant']
> = {
    create: 'default',
    edit: 'outline',
    delete: 'destructive',
};

const mapIcon: Record<ActionType, React.ReactNode> = {
    create: <PlusIcon className="size-3" />,
    edit: <PencilIcon className="size-3" />,
    delete: <TrashIcon className="size-3" />,
};

export const ActionButton = React.forwardRef<HTMLButtonElement, ActionButtonProps>(
    (
        {
            action,
            icon,
            variant,
            size = 'sm',
            className,
            children,
            asChild = false,
            ...props
        },
        ref,
    ) => {
        const Comp = asChild ? Slot : 'button';
        const v = variant ?? mapVariant[action];
        const SelectedIcon = icon ?? mapIcon[action];

        return (
            <Comp
                ref={ref}
                className={cn(
                    buttonVariants({ variant: v, size, className }),
                    'text-xs',
                    action === 'edit' &&
                        'bg-yellow-500 text-white hover:bg-secondary/80 hover:text-white',
                    className,
                )}
                {...props}
            >
                {SelectedIcon}
                {children}
            </Comp>
        );
    },
);
ActionButton.displayName = 'ActionButton';
