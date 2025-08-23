import { cn } from '@/lib/utils';
import { UploadCloud, X } from 'lucide-react';
import * as React from 'react';

interface ImageDropzoneProps {
    id: string;
    value: File | string | null;
    onChange: (file: File | null) => void;
    className?: string;
    disabled?: boolean;
}

export function ImageDropzone({
    id,
    value,
    onChange,
    className,
    disabled = false,
}: ImageDropzoneProps) {
    const inputRef = React.useRef<HTMLInputElement>(null);
    const [previewUrl, setPreviewUrl] = React.useState<string | null>(null);
    const [isDragging, setIsDragging] = React.useState(false);

    React.useEffect(() => {
        if (typeof value === 'string') {
            setPreviewUrl(value);
        } else if (value instanceof File) {
            const url = URL.createObjectURL(value);
            setPreviewUrl(url);
            return () => URL.revokeObjectURL(url);
        } else {
            setPreviewUrl(null);
        }
    }, [value]);

    const handleFileChange = (files: FileList | null) => {
        const file = files?.[0] || null;
        onChange(file);
    };

    const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        if (!disabled) setIsDragging(true);
    };

    const handleDragLeave = (e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        setIsDragging(false);
    };

    const handleDrop = (e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        setIsDragging(false);
        if (!disabled) {
            handleFileChange(e.dataTransfer.files);
        }
    };

    const handleRemoveImage = (e: React.MouseEvent<HTMLButtonElement>) => {
        e.stopPropagation();
        onChange(null);
        if (inputRef.current) {
            inputRef.current.value = '';
        }
    };

    return (
        <div
            className={cn(
                'relative flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-8 text-center transition-colors hover:border-primary',
                {
                    'border-primary bg-primary/10': isDragging,
                    'cursor-not-allowed opacity-50': disabled,
                },
                className,
            )}
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
            onDrop={handleDrop}
            onClick={() => inputRef.current?.click()}
        >
            {previewUrl ? (
                <>
                    <img
                        src={previewUrl}
                        alt="Preview"
                        className="max-h-48 w-full rounded-md object-contain"
                    />
                    <button
                        type="button"
                        onClick={handleRemoveImage}
                        className="absolute top-2 right-2 rounded-full bg-gray-900/50 p-1.5 text-white hover:bg-gray-900"
                        aria-label="Remove image"
                    >
                        <X className="h-4 w-4" />
                    </button>
                </>
            ) : (
                <div className="flex flex-col items-center gap-2 text-muted-foreground">
                    <UploadCloud className="h-10 w-10" />
                    <p className="font-semibold">
                        Drag and drop your image here or{' '}
                        <span className="text-primary">Browse Files</span>
                    </p>
                    <p className="text-xs">PNG, JPG, WEBP up to 2MB</p>
                </div>
            )}
            <input
                ref={inputRef}
                id={id}
                type="file"
                accept="image/png, image/jpeg, image/webp"
                className="hidden"
                onChange={(e) => handleFileChange(e.target.files)}
                disabled={disabled}
            />
        </div>
    );
}
