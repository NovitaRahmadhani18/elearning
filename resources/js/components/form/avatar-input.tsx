import { cn } from '@/lib/utils';
import { Camera, User2 } from 'lucide-react';
import * as React from 'react';

// Definisikan props untuk komponen AvatarInput
interface AvatarInputProps {
    id: string;
    /** URL gambar yang ada atau File yang baru dipilih */
    value: string | File | null;
    /** Callback yang dipanggil saat file baru dipilih */
    onChange: (file: File | null) => void;
    /** ClassName tambahan untuk kontainer utama */
    className?: string;
    /** Ukuran avatar */
    size?: 'sm' | 'md' | 'lg';
    /** Status disabled */
    disabled?: boolean;
}

// Mapping ukuran ke kelas Tailwind CSS
const sizeClasses = {
    sm: 'h-20 w-20',
    md: 'h-28 w-28',
    lg: 'h-36 w-36',
};

export function AvatarInput({
    id,
    value,
    onChange,
    className,
    size = 'md',
    disabled = false,
}: AvatarInputProps) {
    const inputRef = React.useRef<HTMLInputElement>(null);
    const [previewUrl, setPreviewUrl] = React.useState<string | null>(null);

    // Efek untuk mengelola URL preview
    React.useEffect(() => {
        if (typeof value === 'string') {
            setPreviewUrl(value);
        } else if (value instanceof File) {
            const url = URL.createObjectURL(value);
            setPreviewUrl(url);
            // Cleanup function untuk mencegah memory leak
            return () => URL.revokeObjectURL(url);
        } else {
            setPreviewUrl(null);
        }
    }, [value]);

    // Fungsi untuk memicu klik pada input file yang tersembunyi
    const handleTriggerClick = () => {
        if (!disabled) {
            inputRef.current?.click();
        }
    };

    // Fungsi yang menangani perubahan pada input file
    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] || null;
        onChange(file);
        // Reset nilai input agar bisa memilih file yang sama lagi
        e.target.value = '';
    };

    return (
        <div className={cn('relative inline-flex', sizeClasses[size], className)}>
            <div
                className={cn(
                    'group relative flex h-full w-full items-center justify-center rounded-full bg-muted',
                    'border-2 border-dashed border-gray-300',
                )}
            >
                {previewUrl ? (
                    <img
                        src={previewUrl}
                        alt="Avatar preview"
                        className="h-full w-full rounded-full object-cover"
                    />
                ) : (
                    <User2
                        className={cn(
                            'h-1/2 w-1/2 text-gray-400',
                            size === 'sm' && 'h-10 w-10',
                        )}
                    />
                )}
            </div>

            {/* Tombol Pemicu Input */}
            <button
                type="button"
                onClick={handleTriggerClick}
                disabled={disabled}
                className={cn(
                    'absolute right-0 bottom-0 flex h-8 w-8 items-center justify-center rounded-full bg-gray-900 text-white',
                    'hover:bg-gray-700 focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none',
                    'disabled:cursor-not-allowed disabled:opacity-50',
                    'transition-colors',
                    size === 'lg' && 'right-1 bottom-1 h-10 w-10',
                )}
                aria-label="Ubah avatar"
            >
                <Camera className={cn(size === 'lg' ? 'h-5 w-5' : 'h-4 w-4')} />
            </button>

            {/* Input File yang Sebenarnya (Tersembunyi) */}
            <input
                id={id}
                ref={inputRef}
                type="file"
                accept="image/png, image/jpeg, image/webp"
                onChange={handleFileChange}
                className="hidden"
                disabled={disabled}
            />
        </div>
    );
}
