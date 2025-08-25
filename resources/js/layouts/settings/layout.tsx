import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-react';
import { type PropsWithChildren } from 'react';

export default function SettingsLayout({ children }: PropsWithChildren) {
    return (
        <div className="flex min-h-screen flex-col gap-6 bg-[url('/resources/pattern.png')]">
            <header className="w-full border-b border-primary bg-sidebar py-4">
                <div className="mx-auto flex flex-row items-center justify-between md:max-w-4xl">
                    <Heading
                        title="Profile"
                        description="Manage your profile information and account settings."
                    />

                    <Button variant="outline" onClick={() => window.history.back()}>
                        <ArrowLeft className="mr-2 h-4 w-4" />
                        Back
                    </Button>
                </div>
            </header>

            <div className="mx-auto w-full max-w-4xl flex-1 px-4">
                <section className="w-full">{children}</section>
            </div>
        </div>
    );
}
