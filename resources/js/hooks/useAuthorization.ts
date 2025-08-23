import { usePage } from '@inertiajs/react';

// @ts-expect-error
import { PageProps } from '@/types';

export function useAuthorization() {
    const { auth } = usePage<PageProps>().props;
    const user = auth.user;

    const hasRole = (roleName: string) => {
        return user?.role === roleName;
    };

    const hasAnyRole = (roleNames: string[]) => {
        if (!user?.role) {
            return false;
        }
        return roleNames.includes(user.role);
    };

    return { user, hasRole, hasAnyRole };
}
