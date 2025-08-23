import { PaginatedData, SharedData } from '@/types';
import { TUser } from '@/types/users';

export interface UserIndexPageProps extends SharedData {
    users: PaginatedData<TUser>;
    count: {
        admin: number;
        teacher: number;
        student: number;
    };
}

export interface EditUserFormProps extends SharedData {
    user: {
        data: TUser;
    }; // Terima data user sebagai prop
}
