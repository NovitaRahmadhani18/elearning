import { FiltersQuery } from '.';

export interface DebouncedSearchReturn {
    params: FilterParams;
    setParams: React.Dispatch<React.SetStateAction<FilterParams>>;
    setTimeDebounce: React.Dispatch<React.SetStateAction<number>>;
}

export interface TableToolbarProps extends DebouncedSearchReturn {
    placeholder?: string;
    showPagination?: boolean;
    afterSearchComponent?: React.ReactNode;
}

export type FilterParams = FiltersQuery | string | number | boolean;

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    links: PaginationLink[];
    path: string;
    per_page: number;
    to: number;
    total: number;
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
