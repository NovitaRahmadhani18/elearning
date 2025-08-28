import { LucideIcon } from 'lucide-react';

export type TAchievement = {
    id: number;
    name: string;
    description: string;
    image: string; // URL or path to the icon image
    locked: boolean; // Indicates if the achievement is locked or not
    achieved_at?: string; // Optional date when the achievement was achieved
};

export type TSummaryCardData = {
    label: string;
    value: string;
    Icon: LucideIcon;
    color: 'blue' | 'yellow' | 'green' | 'red';
};
