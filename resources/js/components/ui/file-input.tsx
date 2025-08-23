"use client";

import * as React from "react";
import { Button } from "@/components/ui/button";
import { Input, type InputProps } from "@/components/ui/input";
import { cn } from "@/lib/utils";

interface FileInputProps extends Omit<InputProps, 'onChange' | 'type' | 'value'> {
    onChange: (file: File | null) => void;
    value?: File | null;
}

const FileInput = React.forwardRef<HTMLInputElement, FileInputProps>(
    ({ className, onChange, ...props }, ref) => {
        const [file, setFile] = React.useState<File | null>(null);
        const fileInputRef = React.useRef<HTMLInputElement>(null);

        const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
            const selectedFile = e.target.files?.[0] || null;
            setFile(selectedFile);
            onChange(selectedFile);
        };

        const handleButtonClick = () => {
            fileInputRef.current?.click();
        };

        return (
            <div className={cn("flex items-center gap-2", className)}>
                <Input
                    ref={fileInputRef}
                    type="file"
                    className="hidden"
                    onChange={handleFileChange}
                    {...props}
                />
                <Button type="button" onClick={handleButtonClick} className="bg-primary/60">
                    Choose File
                </Button>
                <div className="flex-grow p-2 border border-input rounded-md text-sm text-muted-foreground bg-white">
                    {file ? file.name : "No file selected"}
                </div>
            </div>
        );
    }
);

FileInput.displayName = "FileInput";

export { FileInput };
