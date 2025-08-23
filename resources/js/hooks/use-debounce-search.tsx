import { DebouncedSearchReturn, FilterParams } from '@/types/datatable';
import { router } from '@inertiajs/react';
import pkg from 'lodash';
import { useCallback, useEffect, useState } from 'react';
import { Router } from 'vendor/tightenco/ziggy/src/js';
import usePrevious from './use-previous';
const { debounce, pickBy } = pkg;

const useDebouncedSearch = (url: Router | string, initialParams: FilterParams = {}, initialTimeDebounce = 500): DebouncedSearchReturn => {
    const [params, setParams] = useState<FilterParams>(initialParams);
    const [timeDebounce, setTimeDebounce] = useState(initialTimeDebounce);
    const prevParams = usePrevious(params);

    const search = useCallback(
        debounce((p: FilterParams) => {
            router.get(url as string, pickBy(p), {
                replace: true,
                preserveScroll: true,
                preserveState: true,
                queryStringArrayFormat: 'indices',
            });
        }, timeDebounce),
        [timeDebounce, url],
    );

    useEffect(() => {
        if (prevParams) {
            search(params);
        }
    }, [params, prevParams, search]);

    return { params, setParams, setTimeDebounce };
};

export default useDebouncedSearch;
