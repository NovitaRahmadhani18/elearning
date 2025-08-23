import { cn } from '@/lib/utils';
import { format } from 'date-fns';
import { CheckCircle2, Lock } from 'lucide-react';
import { TAchievement } from '../../types';

interface AchievementCardProps {
    achievement: TAchievement;
}

const AchievementCard = ({ achievement }: AchievementCardProps) => {
    const { name, description, image, locked, achieved_at } = achievement;

    return (
        <div
            className={cn(
                'relative flex aspect-square flex-col items-center justify-center rounded-lg p-6 text-center transition-all duration-300',
                locked ? 'bg-black/10 text-slate-400' : 'bg-white shadow-md',
            )}
        >
            {/* Ikon Kunci atau Ceklis */}
            {locked ? (
                <Lock className="absolute z-10 h-12 w-12 text-white" />
            ) : (
                <CheckCircle2 className="absolute top-3 right-3 h-6 w-6 fill-white text-green-500" />
            )}

            {/* Gambar Lencana */}
            <img
                src={image}
                alt={name}
                className={cn(
                    'mb-4 h-16 w-16 transition-opacity',
                    locked && 'opacity-10', // Faded out saat terkunci
                )}
            />

            {/* Teks Konten */}
            <h3 className={cn('text-lg font-bold', !locked && 'text-slate-800')}>
                {name}
            </h3>
            <p className="text-sm">{description}</p>

            {!locked && achieved_at && (
                <p className="mt-2 text-xs text-green-600">
                    Diperoleh: {format(new Date(achieved_at), 'dd MMM yyyy')}
                </p>
            )}
        </div>
    );
};

export default AchievementCard;
