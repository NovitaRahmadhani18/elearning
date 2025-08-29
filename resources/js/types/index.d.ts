import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
    permissions?: string[] | string; // Optional permission for access control
    roles?: string[] | string; // Optional role for access control
    children?: NavItem[];
}

export interface FiltersQuery {
    search?: string;
    limit?: number;
    col?: string;
    sort?: string;
    filters?: Record<string, string | number | boolean>;
    province?: Record<number, string> | Option | null; // Province can be a record or an option for combobox
}

export interface Flash {
    type?: 'success' | 'error' | 'info' | 'warning';
    message?: string | string[]; // Optional message or array of messages
    error?: string | string[]; // Optional error message
    data?: Record<string, unknown>; // Optional data for flash messages
    [key: string]: unknown; // This allows for additional properties...
}

export interface SharedData {
    name: string;
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    filters: FiltersQuery;
    query?: Record<string, string | number | boolean>; // Optional query parameters
    flash?: Flash; // Optional flash messages
    [key: string]: unknown; // This allows for additional properties...
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    role: 'admin' | 'teacher' | 'student';
    email_verified_at: string | null;
    id_number?: string;
    gender: string;

    created_at: string;
    updated_at: string;
    total_points?: number;
    [key: string]: unknown; // This allows for additional properties...
}

export interface PaginatedData<TData> {
    data: TData[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
    meta: PaginationMeta;
}
