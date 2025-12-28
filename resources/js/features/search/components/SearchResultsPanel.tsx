import { memo } from 'react';
import LoadingState from '@/components/ui/LoadingState';
import SearchResultItem from './SearchResultItem';
import Pagination from '@/components/pagination';

interface Result {
    id: number;
    name?: string;
    title?: string;
    [key: string]: unknown;
}

interface PaginationData {
    current_page: number;
    per_page: number;
    total: number;
    total_pages: number;
}

interface SearchResultsPanelProps {
    type: 'people' | 'movies';
    results?: Result[];
    resultsCount?: number;
    isLoading: boolean;
    pagination?: PaginationData;
    onPageChange?: (page: number) => void;
}

/**
 * Reusable search results panel component
 * Memoized to prevent unnecessary re-renders
 */
function SearchResultsPanel({ type, results, isLoading, pagination, onPageChange }: SearchResultsPanelProps) {
    return (
        <div 
            className="bg-white rounded-sm p-6 min-h-[600px] flex flex-col"
            style={{ boxShadow: '0 2px 4px var(--color-warm-grey-shadow)' }}
        >
            <h2 className="font-montserrat text-xl font-bold text-black block mt-1 mb-1">
                Results
            </h2>
            <div className="w-full h-px mt-2 mb-0" style={{ backgroundColor: 'var(--color-pinkish-grey)' }} />
            {isLoading ? (
                <div className="flex-1 flex items-center justify-center">
                    <LoadingState message="Searching..." />
                </div>
            ) : results && results.length > 0 ? (
                <div className="w-full flex flex-col flex-1">
                    <div className="flex-1">
                        {results.map((result, index) => (
                            <SearchResultItem
                                key={result.id}
                                id={result.id}
                                name={result.name}
                                title={result.title}
                                type={type}
                                showSeparator={index < results.length - 1}
                            />
                        ))}
                    </div>
                    {pagination && onPageChange && (
                        <Pagination
                            currentPage={pagination.current_page}
                            totalPages={pagination.total_pages}
                            onPageChange={onPageChange}
                        />
                    )}
                </div>
            ) : (
                <div className="flex-1 flex items-center justify-center">
                    <div className="text-center font-montserrat font-bold text-base" style={{ color: 'var(--color-pinkish-grey)' }}>
                        <div>There are zero matches.</div>
                        <div>Use the form to search for People or Movies.</div>
                    </div>
                </div>
            )}
        </div>
    );
}

export default memo(SearchResultsPanel);

