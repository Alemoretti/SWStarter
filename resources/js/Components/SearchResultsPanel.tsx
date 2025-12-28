import { memo } from 'react';
import LoadingState from './LoadingState';
import SearchResultItem from './SearchResultItem';

interface Result {
    id: number;
    name?: string;
    title?: string;
    [key: string]: unknown;
}

interface SearchResultsPanelProps {
    type: 'people' | 'movies';
    results?: Result[];
    resultsCount?: number;
    isLoading: boolean;
}

/**
 * Reusable search results panel component
 * Memoized to prevent unnecessary re-renders
 */
function SearchResultsPanel({ type, results, resultsCount, isLoading }: SearchResultsPanelProps) {
    return (
        <div className="bg-white rounded-lg shadow p-6">
            <h2 className="font-montserrat text-lg font-bold text-black mb-[10px] block">
                Results
            </h2>
            <div className="w-full h-px mt-2 mb-0" style={{ backgroundColor: 'var(--color-pinkish-grey)' }} />
            <div>
                {isLoading ? (
                    <LoadingState message="Searching..." />
                ) : results && results.length > 0 ? (
                    <div>
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
                ) : results && resultsCount === 0 ? (
                    <div className="text-center text-gray-600 py-8">
                        There are zero matches.
                    </div>
                ) : (
                    <div className="text-center text-gray-400 py-8">
                        No search performed yet.
                    </div>
                )}
            </div>
        </div>
    );
}

export default memo(SearchResultsPanel);

