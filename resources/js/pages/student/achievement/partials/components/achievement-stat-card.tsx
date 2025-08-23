import { cn } from '@/lib/utils';
import type { LucideIcon } from 'lucide-react'; // Tipe untuk ikon dari lucide

// Tipe untuk props komponen
interface SummaryCardProps {
    icon: LucideIcon;
    label: string;
    value: string | number;
    color?: 'blue' | 'yellow' | 'green' | 'red'; // Tema warna
    className?: string;
}

// Objek pemetaan untuk tema warna agar Tailwind dapat mendeteksinya
const colorVariants = {
    blue: {
        bg: 'bg-blue-100',
        text: 'text-blue-500',
    },
    yellow: {
        bg: 'bg-yellow-100',
        text: 'text-yellow-500',
    },
    green: {
        bg: 'bg-green-100',
        text: 'text-green-500',
    },
    red: {
        bg: 'bg-red-100',
        text: 'text-red-500',
    },
};

export const SummaryCard = ({
    icon: Icon, // Ganti nama prop agar bisa digunakan sebagai komponen (diawali huruf kapital)
    label,
    value,
    color = 'blue', // Warna default
    className,
}: SummaryCardProps) => {
    const colors = colorVariants[color];

    return (
        <div
            className={cn(
                'flex items-center rounded-lg bg-white p-6 shadow-xs',
                className,
            )}
        >
            <div className={cn('mr-4 rounded-full p-3', colors.bg)}>
                <Icon className={cn('h-6 w-6', colors.text)} />
            </div>
            <div>
                <p className="text-sm text-slate-500">{label}</p>
                <p className="text-2xl font-bold text-slate-800">{value}</p>
            </div>
        </div>
    );
};
