import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { TableToolbarProps } from '@/types/datatable';
import React, { useState } from 'react';
import { Input } from '../ui/input';

const SearchInput: React.FC<TableToolbarProps> = ({
    placeholder,
    params,
    setParams,
    setTimeDebounce,
}) => {
    return (
        <Input
            placeholder={placeholder || 'Cari...'}
            className="w-full shadow-none lg:w-[250px]"
            value={(params.search as string) || ''}
            onChange={(e) => {
                setTimeDebounce(500);
                setParams({ ...params, search: e.target.value });
            }}
        />
    );
};

const RowPerPageSelect: React.FC<TableToolbarProps> = ({
    params,
    setParams,
    setTimeDebounce,
}) => {
    const [limit, setLimit] = useState((params.limit || 10).toString());

    return (
        <Select
            value={limit}
            onValueChange={(value) => {
                setLimit(value);
                setTimeDebounce(50);
                setParams({ ...params, limit: value, page: 1 }); // Reset ke halaman 1 saat limit berubah
            }}
        >
            <SelectTrigger className="mr-2 w-[70px] bg-white shadow-none">
                <SelectValue placeholder="10" />
            </SelectTrigger>
            <SelectContent side="bottom">
                {[10, 25, 50, 100].map((pageSize) => (
                    <SelectItem
                        key={pageSize}
                        value={`${pageSize}`}
                        className="cursor-pointer"
                    >
                        {pageSize}
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );
};

const TableToolbar: React.FC<TableToolbarProps> = ({
    showPagination = true,
    ...props
}) => {
    return (
        <div className="flex items-center justify-between gap-2">
            <div className="flex items-center">
                {showPagination && (
                    <>
                        <p className="mr-2 hidden text-sm font-medium sm:block">
                            Rows
                        </p>
                        <RowPerPageSelect {...props} />
                    </>
                )}
            </div>

            <div className="flex flex-grow items-center justify-end gap-2 md:flex-grow-0">
                <SearchInput {...props} />
                {props.afterSearchComponent}
            </div>
        </div>
    );
};

export default TableToolbar;
