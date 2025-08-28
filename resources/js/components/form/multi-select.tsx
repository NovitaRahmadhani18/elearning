import { cn } from '@/lib/utils';
import * as React from 'react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Check, X } from 'lucide-react';

// Tipe data dasar yang kita harapkan. Tipe data Anda harus setidaknya memiliki ini.
export type SelectOption = {
    value: string;
    label: string;
    [key: string]: any; // Izinkan properti lain
};

// Props sekarang menggunakan Tipe Generik <T>
interface MultiSelectSearchProps<T extends SelectOption> {
    options: T[];
    selected: T[];
    onSelectionChange: (selection: T[]) => void;

    // BARU: Prop untuk merender item kustom
    renderItem?: (option: T) => React.ReactNode;

    placeholder?: string;
    emptyText?: string;
    className?: string;
}

export function MultiSelectSearch<T extends SelectOption>({
    options,
    selected,
    onSelectionChange,
    renderItem, // <-- Prop baru
    placeholder = 'Search...',
    emptyText = 'No results found.',
    className,
}: MultiSelectSearchProps<T>) {
    const [open, setOpen] = React.useState(false);

    const handleSelect = (option: T) => {
        const isSelected = selected.some((s) => s.value === option.value);
        if (isSelected) {
            onSelectionChange(selected.filter((s) => s.value !== option.value));
        } else {
            onSelectionChange([...selected, option]);
        }
    };

    const handleDeselect = (option: T) => {
        onSelectionChange(selected.filter((s) => s.value !== option.value));
    };

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    role="combobox"
                    aria-expanded={open}
                    className={cn('h-auto w-full justify-between', className)}
                >
                    <div className="flex flex-wrap gap-1">
                        {selected.length > 0 ? (
                            selected.map((option) => (
                                <Badge
                                    key={option.value}
                                    variant="secondary"
                                    className="mr-1 mb-1"
                                >
                                    {option.label}
                                    <button
                                        className="ml-1 rounded-full ring-offset-background outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                        onClick={() => handleDeselect(option)}
                                    >
                                        <X className="h-3 w-3 text-muted-foreground hover:text-foreground" />
                                    </button>
                                </Badge>
                            ))
                        ) : (
                            <span className="text-muted-foreground">
                                {placeholder}
                            </span>
                        )}
                    </div>
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-[--radix-popover-trigger-width] p-0">
                <Command>
                    <CommandInput placeholder={placeholder} />
                    <CommandList>
                        <CommandEmpty>{emptyText}</CommandEmpty>
                        <CommandGroup>
                            {options.map((option) => {
                                const isSelected = selected.some(
                                    (s) => s.value === option.value,
                                );
                                return (
                                    <CommandItem
                                        key={option.value}
                                        value={option.label} // Command 'cmdk' mencari berdasarkan ini
                                        onSelect={() => handleSelect(option)}
                                    >
                                        <Check
                                            className={cn(
                                                'mr-2 h-4 w-4',
                                                isSelected
                                                    ? 'opacity-100'
                                                    : 'opacity-0',
                                            )}
                                        />

                                        {/* --- PERUBAHAN UTAMA DI SINI --- */}
                                        {renderItem ? (
                                            // Jika ada renderItem, gunakan itu
                                            <div className="flex-1">
                                                {renderItem(option)}
                                            </div>
                                        ) : (
                                            // Jika tidak, gunakan perilaku default
                                            <span>{option.label}</span>
                                        )}
                                    </CommandItem>
                                );
                            })}
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
