import InputError from '@/components/input-error'; // Asumsi Anda punya komponen ini
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';

// 1. Definisikan struktur untuk setiap opsi dalam select
export interface Option {
    value: string;
    label: string;
}

// 2. Definisikan props untuk komponen SelectInput
interface SelectInputProps {
    id: string;
    label: string;
    options: Option[];
    value?: string;
    onChange: (value: string) => void;
    placeholder?: string;
    className?: string;
    required?: boolean;
    disabled?: boolean;
    error?: string;
}

export function SelectInput({
    id,
    label,
    options,
    value,
    onChange,
    placeholder,
    className,
    required = true,
    disabled = false,
    error,
}: SelectInputProps) {
    return (
        <div className={cn('grid w-full items-center gap-1.5', className)}>
            <Label htmlFor={id}>
                {label}
                {required && <sup className="text-sm text-red-500">*</sup>}
            </Label>
            <Select
                value={value}
                onValueChange={onChange}
                disabled={disabled}
                required={required}
            >
                <SelectTrigger id={id}>
                    <SelectValue placeholder={placeholder} />
                </SelectTrigger>
                <SelectContent>
                    {options.map((option) => (
                        <SelectItem key={option.value} value={option.value}>
                            {option.label}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
            <InputError message={error} />
        </div>
    );
}
