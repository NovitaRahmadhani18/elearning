export type TActvityUser = {
    user: {
        id: number;
        name: string;
        email: string;
        avatar?: string | null;
    };
    desc: string;
    created_at: string;
};
