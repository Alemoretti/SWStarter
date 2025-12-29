import { useState, useCallback } from 'react';
import { router } from '@inertiajs/react';
import { searchSchema } from '@/features/search/schemas/searchSchema';
import { SearchType } from '@/types/api';

interface UseSearchFormOptions {
    initialQuery?: string;
    initialType?: SearchType;
    initialPage?: number;
}

interface UseSearchFormReturn {
    query: string;
    type: SearchType;
    page: number;
    errors: Record<string, string>;
    isLoading: boolean;
    setQuery: (query: string) => void;
    setType: (type: SearchType) => void;
    setPage: (page: number) => void;
    handleSubmit: (e: React.FormEvent<HTMLFormElement>) => void;
    handlePageChange: (page: number) => void;
}

/**
 * Custom hook for managing search form state and submission
 */
export function useSearchForm({
    initialQuery = '',
    initialType = 'people',
    initialPage = 1,
}: UseSearchFormOptions = {}): UseSearchFormReturn {
    const [query, setQuery] = useState(initialQuery);
    const [type, setType] = useState<SearchType>(initialType);
    const [page, setPage] = useState(initialPage);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [isLoading, setIsLoading] = useState(false);

    const performSearch = useCallback((searchQuery: string, searchType: SearchType, searchPage: number) => {
        setErrors({});

        // Validate with Zod
        const result = searchSchema.safeParse({ query: searchQuery, type: searchType });

        if (!result.success) {
            const fieldErrors: Record<string, string> = {};
            result.error.issues.forEach((issue) => {
                if (issue.path[0]) {
                    fieldErrors[issue.path[0].toString()] = issue.message;
                }
            });
            setErrors(fieldErrors);
            return;
        }

        setIsLoading(true);

        // Submit search via Inertia using GET with query parameters
        router.get('/api/v1/search', {
            ...result.data,
            page: searchPage,
        }, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setIsLoading(false),
        });
    }, []);

    const handleSubmit = useCallback((e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        // Reset to page 1 when submitting a new search
        setPage(1);
        performSearch(query, type, 1);
    }, [query, type, performSearch]);

    const handlePageChange = useCallback((newPage: number) => {
        setPage(newPage);
        performSearch(query, type, newPage);
    }, [query, type, performSearch]);

    const handleTypeChange = useCallback((newType: SearchType) => {
        setType(newType);
        setPage(1);
        setQuery('');
    }, []);

    return {
        query,
        type,
        page,
        errors,
        isLoading,
        setQuery,
        setType: handleTypeChange,
        setPage,
        handleSubmit,
        handlePageChange,
    };
}

