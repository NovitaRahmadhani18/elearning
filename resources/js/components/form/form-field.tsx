import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';
import { memo } from 'react';

interface FormFieldProps {
    id: string;
    label: string;
    value: string;
    onChange: (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => void;
    placeholder?: string;
    type?: string;
    error?: string;
    required?: boolean;
    className?: string;
    readonly?: boolean;
    disabled?: boolean;
    textarea?: boolean;
}

export const FormField = memo(function FormField({
    id,
    label,
    value,
    onChange,
    placeholder = '',
    type = 'text',
    error,
    required = true,
    className = '',
    textarea = false,
    ...props
}: FormFieldProps) {
    return (
        <div className={cn('space-y-2', className)}>
            <Label htmlFor={id}>
                {label}
                {required && <sup className="text-sm text-red-500">*</sup>}
            </Label>
            {textarea ? (
                <Textarea
                    id={id}
                    name={id}
                    value={value}
                    onChange={onChange}
                    placeholder={placeholder}
                    required={required}
                    {...props}
                />
            ) : (
                <Input
                    id={id}
                    name={id}
                    type={type}
                    value={value}
                    onChange={onChange}
                    placeholder={placeholder}
                    required={required}
                    autoComplete="off"
                    {...props}
                />
            )}
            <InputError message={error} />
        </div>
    );
});
