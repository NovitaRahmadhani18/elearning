import { Toaster } from '@/components/ui/sonner';
import { PropsWithChildren } from 'react';
import AppHeaderLayout from './app/app-header-layout';

const StudentLayout: React.FC<PropsWithChildren> = ({ children }) => {
    return (
        <AppHeaderLayout>
            {children}
            <Toaster position="top-right" richColors theme="light" />
        </AppHeaderLayout>
    );
};

export default StudentLayout;
