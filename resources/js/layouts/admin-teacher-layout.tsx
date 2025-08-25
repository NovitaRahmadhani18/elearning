import AppLogo from '@/components/app-logo';
import { Breadcrumbs } from '@/components/breadcrumbs';
import { NavUser } from '@/components/nav-user';
import NotificationPopover from '@/components/notification-popover';
import {
    Sidebar,
    SidebarContent,
    SidebarGroup,
    SidebarHeader,
    SidebarInset,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarProvider,
    SidebarTrigger,
} from '@/components/ui/sidebar';
import { cn } from '@/lib/utils';
import { BreadcrumbItem, NavItem, SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import {
    BookMarked,
    BookOpen,
    LayoutGrid,
    List,
    SquareKanban,
    UsersRound,
} from 'lucide-react';
import { PropsWithChildren, useMemo } from 'react';
import { Toaster } from 'sonner';

interface AdminTeacherLayoutProps extends PropsWithChildren {
    breadcrumbs?: BreadcrumbItem[];
    className?: string;
    [key: string]: unknown;
}

const AdminTeacherLayout: React.FC<AdminTeacherLayoutProps> = ({
    children,
    breadcrumbs = [],
    className,
}) => {
    return (
        <SidebarProvider>
            <SidebarAdminTeacherLayout />
            <SidebarInset className="bg-[url('/resources/pattern.png')]">
                <AdminTeacherLayoutHeader breadcrumbs={breadcrumbs} />
                <main
                    className={cn(
                        'mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl p-6',
                        className,
                    )}
                >
                    {children}
                </main>
            </SidebarInset>
            <Toaster position="top-right" />
        </SidebarProvider>
    );
};

export default AdminTeacherLayout;

const AdminTeacherLayoutHeader: React.FC<{ breadcrumbs: BreadcrumbItem[] }> = ({
    breadcrumbs,
}) => {
    return (
        <header className="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/50 bg-sidebar px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
            <div className="flex w-full items-center justify-between gap-2">
                <div className="flex items-center gap-2">
                    <SidebarTrigger className="-ml-1" />
                    <Breadcrumbs breadcrumbs={breadcrumbs} />
                </div>

                <div className="flex items-center gap-4">
                    <NotificationPopover />
                    <NavUser />
                </div>
            </div>
        </header>
    );
};

const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Users Managament',
        href: '/admin/users',
        icon: UsersRound,
    },
    {
        title: 'Classrooms Managament',
        href: '/admin/classrooms',
        icon: BookMarked,
    },
    {
        title: 'Monitoring',
        href: '/admin/monitoring',
        icon: SquareKanban,
    },
];

const teacherNavItems: NavItem[] = [
    {
        title: 'Classrooms Management',
        href: '/teacher/classrooms',
        icon: BookMarked,
    },
    {
        title: 'Material Management',
        href: '/teacher/materials',
        icon: BookOpen,
    },
    {
        title: 'Quizzes Management',
        href: '/teacher/quizzes',
        icon: List,
    },
    {
        title: 'Student Tracking',
        href: '/teacher/student-tracking',
        icon: LayoutGrid,
    },
];

const SidebarAdminTeacherLayout = () => {
    const { auth } = usePage<SharedData>().props;
    const page = usePage();

    const sidebarItem = useMemo(() => {
        if (auth.user.role === 'admin') {
            return adminNavItems;
        } else if (auth.user.role === 'teacher') {
            return teacherNavItems;
        } else {
            return [];
        }
    }, [auth]);

    return (
        <Sidebar collapsible="icon">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <SidebarGroup>
                    <SidebarMenu>
                        {sidebarItem.map((item) => (
                            <SidebarMenuItem key={item.title}>
                                <SidebarMenuButton
                                    asChild
                                    isActive={page.url.startsWith(item.href)}
                                    tooltip={{ children: item.title }}
                                >
                                    <Link href={item.href} prefetch>
                                        {item.icon && <item.icon />}
                                        <span>{item.title}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        ))}
                    </SidebarMenu>
                </SidebarGroup>
            </SidebarContent>
        </Sidebar>
    );
};
