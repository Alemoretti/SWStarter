import { useState, useCallback } from 'react';
import { router } from '@inertiajs/react';
import { searchSchema } from '@/schemas/searchSchema';

interface UseSearchFormOptions {
    initialQuery?: string;
    initialType?: 'people' | 'movies';
}

interface UseSearchFormReturn {
    query: string;
    type: 'people' | 'movies';
    errors: Record<string, string>;
    isLoading: boolean;
    setQuery: (query: string) => void;
    setType: (type: 'people' | 'movies') => void;
    handleSubmit: (e: React.FormEvent<HTMLFormElement>) => void;
}

/**
 * Custom hook for managing search form state and submission
 */
export function useSearchForm({
    initialQuery = '',
    initialType = 'people',
}: UseSearchFormOptions = {}): UseSearchFormReturn {
    const [query, setQuery] = useState(initialQuery);
    const [type, setType] = useState<'people' | 'movies'>(initialType);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = useCallback((e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        setErrors({});

        // Validate with Zod
        const result = searchSchema.safeParse({ query, type });

        if (!result.success) {
            const fieldErrors: Record<string, string> = {};
            result.error.issues.forEach((error) => {
                if (error.path[0]) {
                    fieldErrors[error.path[0].toString()] = error.message;
                }
            });
            setErrors(fieldErrors);
            return;
        }

        setIsLoading(true);

        // Submit search via Inertia using GET with query parameters
        router.get('/api/v1/search', result.data, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setIsLoading(false),
        });
    }, [query, type]);

    return {
        query,
        type,
        errors,
        isLoading,
        setQuery,
        setType,
        handleSubmit,
    };
}

