export interface TUser {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    address: string; // Optional property for address
    gender: 'male' | 'female';
    id_number: string; // Optional property for ID number
    is_active: boolean; // Optional property for active status
    role: 'admin' | 'teacher' | 'student';
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}
