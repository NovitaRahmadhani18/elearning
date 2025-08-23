import { useEditor, EditorContent, type Editor } from "@tiptap/react";
import StarterKit from "@tiptap/starter-kit";
import Image from '@tiptap/extension-image';
import { Bold, Italic, List, ListOrdered, Heading2, Image as ImageIcon } from "lucide-react";
import React, { useCallback, useRef } from "react";
import axios from "axios";
import { toast } from "sonner";

import { Toggle } from "@/components/ui/toggle";
import { cn } from "@/lib/utils";

// ========= PROPS DEFINITION =========
type RichTextEditorProps = {
    value: string;
    onChange: (value: string) => void;
    className?: string;
    disabled?: boolean;
};

// ========= TOOLBAR COMPONENT =========
const EditorToolbar = ({
    editor,
    onImageUpload
}: {
    editor: Editor | null,
    onImageUpload: (file: File) => Promise<string>
}) => {
    const fileInputRef = useRef<HTMLInputElement>(null);

    if (!editor) {
        return null;
    }


    const handleFileChange = async (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        if (!file) return;

        try {
            // Panggil fungsi upload dari props dan tunggu URL-nya
            const url = await onImageUpload(file);
            // Sisipkan gambar ke editor
            editor.chain().focus().setImage({ src: url }).run();
        } catch (error) {
            toast.error("Image upload failed.");
            console.error(error);
        }
    };

    return (
        <div className="border border-input bg-transparent rounded-t-md p-1 flex items-center gap-1 flex-wrap">
            <Toggle
                size="sm"
                pressed={editor.isActive("heading", { level: 2 })}
                onPressedChange={() =>
                    editor.chain().focus().toggleHeading({ level: 2 }).run()
                }
                disabled={!editor.isEditable}
            >
                <Heading2 className="h-4 w-4" />
            </Toggle>
            <Toggle
                size="sm"
                pressed={editor.isActive("bold")}
                onPressedChange={() => editor.chain().focus().toggleBold().run()}
                disabled={!editor.isEditable}
            >
                <Bold className="h-4 w-4" />
            </Toggle>
            <Toggle
                size="sm"
                pressed={editor.isActive("italic")}
                onPressedChange={() => editor.chain().focus().toggleItalic().run()}
                disabled={!editor.isEditable}
            >
                <Italic className="h-4 w-4" />
            </Toggle>
            <Toggle
                size="sm"
                pressed={editor.isActive("bulletList")}
                onPressedChange={() =>
                    editor.chain().focus().toggleBulletList().run()
                }
                disabled={!editor.isEditable}
            >
                <List className="h-4 w-4" />
            </Toggle>
            <Toggle
                size="sm"
                pressed={editor.isActive("orderedList")}
                onPressedChange={() =>
                    editor.chain().focus().toggleOrderedList().run()
                }
                disabled={!editor.isEditable}
            >
                <ListOrdered className="h-4 w-4" />
            </Toggle>

            {/* Tombol untuk upload gambar */}
            <Toggle
                size="sm"
                onPressedChange={() => fileInputRef.current?.click()}
                disabled={!editor.isEditable}
            >
                <ImageIcon className="h-4 w-4" />
            </Toggle>
            <input
                type="file"
                ref={fileInputRef}
                onChange={handleFileChange}
                className="hidden"
                accept="image/jpeg, image/png, image/gif, image/webp"
            />
        </div>
    );
};

// ========= MAIN EDITOR COMPONENT =========
export function RichTextEditor({
    value,
    onChange,
    className,
    disabled = false,
}: RichTextEditorProps) {

    // Fungsi terpusat untuk menangani logika upload file
    const handleImageUpload = useCallback(async (file: File): Promise<string> => {
        const formData = new FormData();
        formData.append("image", file);

        // Kirim request ke endpoint Laravel Anda
        const response = await axios.post(route('images.upload'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // Kembalikan URL yang diberikan oleh server
        return response.data.url;
    }, []);

    const editor = useEditor({
        extensions: [
            StarterKit.configure({
                // Nonaktifkan ekstensi yang tidak Anda butuhkan untuk performa lebih baik
                // contoh: codeBlock: false,
            }),
            Image.configure({
                inline: true,
                allowBase64: false, // Penting untuk memaksa upload ke server
            }),
        ],
        content: value,
        editable: !disabled, // Atur status editable berdasarkan prop
        onUpdate: ({ editor }) => {
            onChange(editor.getHTML());
        },
        editorProps: {
            attributes: {
                class: cn(
                    "prose prose-sm min-h-[150px] w-full max-w-full rounded-b-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
                    "prose-headings:font-semibold prose-headings:leading-tight prose-headings:text-foreground prose-headings:my-0",
                    "prose-p:leading-relaxed prose-p:text-foreground prose-p:my-0",
                    "prose-a:text-primary hover:prose-a:text-primary/80",
                    "prose-pre:bg-muted prose-pre:text-foreground",
                    "prose-code:bg-muted prose-code:text-foreground",
                    "prose-strong:text-foreground",
                    "prose-em:text-foreground",
                    "prose-ul:my-0 prose-ol:my-0",
                    className
                ),
            },
            // Menangani event drop untuk fungsionalitas drag-and-drop
            handleDrop: (view, event, slice, moved) => {
                if (moved || !event.dataTransfer) {
                    return false;
                }

                const files = event.dataTransfer.files;
                if (!files || files.length === 0) {
                    return false;
                }

                const imageFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
                if (imageFiles.length === 0) {
                    return false;
                }

                event.preventDefault();

                imageFiles.forEach(async (file) => {
                    try {
                        const url = await handleImageUpload(file);
                        const { schema } = view.state;
                        const coordinates = view.posAtCoords({ left: event.clientX, top: event.clientY });

                        if (!coordinates) return;

                        const node = schema.nodes.image.create({ src: url });
                        const transaction = view.state.tr.insert(coordinates.pos, node);
                        view.dispatch(transaction);

                    } catch (error) {
                        toast.error("Image upload by drop failed.");
                        console.error(error);
                    }
                });

                return true;
            },
        },
    });

    return (
        <div>
            <EditorToolbar editor={editor} onImageUpload={handleImageUpload} />
            <EditorContent editor={editor} />
        </div>
    );
}
