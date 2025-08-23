import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { cn } from '@/lib/utils';

// 1. Kita bisa gunakan lagi interface Option yang sama
export interface Option {
    value: string;
    label: string;
}

// 2. Definisikan props untuk komponen RadioGroupInput
interface RadioGroupInputProps {
    id: string;
    label: string;
    options: Option[];
    value?: string;
    onChange: (value: string) => void;
    orientation?: 'vertical' | 'horizontal';
    className?: string;
    required?: boolean;
    disabled?: boolean;
    error?: string;
}

export function RadioGroupInput({
    id,
    label,
    options,
    value,
    onChange,
    orientation = 'vertical',
    className,
    required = true,
    disabled = false,
    error,
}: RadioGroupInputProps) {
    return (
        <div className={cn('grid w-full items-center gap-1', className)}>
            <Label>
                {label}
                {required && <sup className="text-sm text-red-500">*</sup>}
            </Label>
            <RadioGroup
                value={value}
                onValueChange={onChange}
                disabled={disabled}
                required={required}
                className={cn(
                    'flex gap-4',
                    orientation === 'vertical' ? 'flex-col' : 'flex-row',
                )}
            >
                {options.map((option) => (
                    <div className="flex items-center gap-2" key={option.value}>
                        <RadioGroupItem
                            value={option.value}
                            id={`${id}-${option.value}`}
                        />
                        <Label
                            htmlFor={`${id}-${option.value}`}
                            className="font-normal"
                        >
                            {option.label}
                        </Label>
                    </div>
                ))}
            </RadioGroup>
            <InputError message={error} />
        </div>
    );
}
