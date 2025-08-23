import {
    BookOpen,
    Calculator,
    FlaskConical,
    Globe,
    GraduationCap,
    Palette,
} from 'lucide-react';

export const pastelColors = [
    'bg-blue-500',
    'bg-green-500',
    'bg-purple-500',
    'bg-indigo-500',
    'bg-pink-500',
    'bg-teal-500',
];

export const textColors = [
    'text-blue-500',
    'text-green-500',
    'text-purple-500',
    'text-indigo-500',
    'text-pink-500',
    'text-teal-500',
];

export const opcityColors = [
    'bg-blue-500/50',
    'bg-green-500/50',
    'bg-purple-500/50',
    'bg-indigo-500/50',
    'bg-pink-500/50',
    'bg-teal-500/50',
];

export const cardIcons = [
    GraduationCap,
    BookOpen,
    Calculator,
    FlaskConical,
    Globe,
    Palette,
];

export const getRandomCardAppearance = (id: number) => {
    const colorIndex = id % pastelColors.length;
    const iconIndex = id % cardIcons.length;

    return {
        color: pastelColors[colorIndex],
        Icon: cardIcons[iconIndex],
        textColor: textColors[colorIndex],
        opcityColor: opcityColors[colorIndex],
    };
};
