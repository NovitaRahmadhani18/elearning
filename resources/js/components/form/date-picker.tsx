import { format } from 'date-fns';
import { Calendar as CalendarIcon } from 'lucide-react';

import InputError from '@/components/input-error'; // Asumsi Anda punya komponen ini
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ScrollArea } from '@/components/ui/scroll-area';
import { cn } from '@/lib/utils';

// Definisikan props untuk komponen kita
interface DateTimePicker24hFormProps {
    id: string;
    label: string;
    value: Date | null;
    onChange: (date: Date | null) => void;
    placeholder?: string;
    error?: string;
    required?: boolean;
    disabled?: boolean;
    className?: string;
    minDate?: Date; // Tanggal minimum yang bisa dipilih
}

export function DateTimePicker24hForm({
    id,
    label,
    value,
    onChange,
    placeholder = 'Select date and time',
    error,
    required = false,
    disabled = false,
    className,
    minDate,
}: DateTimePicker24hFormProps) {
    /**
     * Menangani perubahan tanggal dari kalender.
     * Menggabungkan tanggal baru dengan waktu yang sudah ada.
     */
    const handleDateSelect = (newDate: Date | undefined) => {
        if (!newDate) return;

        // Ambil jam dan menit dari nilai yang sudah ada, atau default ke waktu saat ini jika null
        const hours = value ? value.getHours() : new Date().getHours();
        const minutes = value ? value.getMinutes() : new Date().getMinutes();

        const combinedDate = new Date(
            newDate.getFullYear(),
            newDate.getMonth(),
            newDate.getDate(),
            hours,
            minutes,
        );

        onChange(combinedDate);
    };

    /**
     * Menangani perubahan waktu dari scroll area.
     * Menggabungkan waktu baru dengan tanggal yang sudah ada.
     */
    const handleTimeChange = (type: 'hour' | 'minute', timeValue: number) => {
        // Jika belum ada tanggal, gunakan tanggal hari ini sebagai dasar
        const baseDate = value ? new Date(value) : new Date();

        if (type === 'hour') {
            baseDate.setHours(timeValue);
        } else {
            baseDate.setMinutes(timeValue);
        }

        onChange(new Date(baseDate));
    };

    const selectedHour = value?.getHours();
    const selectedMinute = value?.getMinutes();

    return (
        <div className={cn('grid w-full items-center gap-1.5', className)}>
            <Label htmlFor={id}>
                {label}
                {required && <sup className="text-sm text-red-500">*</sup>}
            </Label>
            <Popover>
                <PopoverTrigger asChild>
                    <Button
                        id={id}
                        variant={'outline'}
                        disabled={disabled}
                        className={cn(
                            'w-full justify-start text-left font-normal',
                            !value && 'text-muted-foreground',
                        )}
                    >
                        <CalendarIcon className="mr-2 h-4 w-4" />
                        {value ? (
                            format(value, 'PPP, HH:mm') // Format: Aug 20, 2025, 18:05
                        ) : (
                            <span>{placeholder}</span>
                        )}
                    </Button>
                </PopoverTrigger>
                <PopoverContent className="w-auto p-0">
                    <div className="flex">
                        <Calendar
                            mode="single"
                            selected={value || undefined}
                            onSelect={handleDateSelect}
                            initialFocus
                            disabled={minDate ? { before: minDate } : undefined}
                        />
                        <div className="flex h-[300px] border-l">
                            {/* Scroll Area untuk Jam */}
                            <ScrollArea className="w-20">
                                <div className="flex flex-col items-center p-2">
                                    {Array.from({ length: 24 }, (_, i) => (
                                        <Button
                                            key={`hour-${i}`}
                                            variant={
                                                selectedHour === i
                                                    ? 'default'
                                                    : 'ghost'
                                            }
                                            className="w-full"
                                            onClick={() =>
                                                handleTimeChange('hour', i)
                                            }
                                        >
                                            {i.toString().padStart(2, '0')}
                                        </Button>
                                    ))}
                                </div>
                            </ScrollArea>
                            {/* Scroll Area untuk Menit */}
                            <ScrollArea className="w-20 border-l">
                                <div className="flex flex-col items-center p-2">
                                    {Array.from({ length: 12 }, (_, i) => i * 5).map(
                                        (minute) => (
                                            <Button
                                                key={`minute-${minute}`}
                                                variant={
                                                    selectedMinute === minute
                                                        ? 'default'
                                                        : 'ghost'
                                                }
                                                className="w-full"
                                                onClick={() =>
                                                    handleTimeChange(
                                                        'minute',
                                                        minute,
                                                    )
                                                }
                                            >
                                                {minute.toString().padStart(2, '0')}
                                            </Button>
                                        ),
                                    )}
                                </div>
                            </ScrollArea>
                        </div>
                    </div>
                </PopoverContent>
            </Popover>
            <InputError message={error} />
        </div>
    );
}
