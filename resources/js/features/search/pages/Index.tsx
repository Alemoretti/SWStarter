import { useMemo, useReducer, useEffect } from 'react';
import { Activity } from 'react';
import { useSearchForm } from '@/features/search/hooks/useSearchForm';
import SearchResultsPanel from '@/features/search/components/SearchResultsPanel';
import SearchPanel from '@/features/search/components/SearchPanel';
import Header from '@/components/layout/Header';
import { SearchResult, PaginationData, SearchType } from '@/types/api';
import { searchResultsReducer, initialSearchResultsState } from '@/features/search/reducers/searchResultsReducer';

interface Props {
    query?: string;
    type?: SearchType;
    results?: SearchResult[];
    resultsCount?: number;
    pagination?: PaginationData;
}

export default function SearchIndex({ 
    query: initialQuery = '', 
    type: initialType = 'people', 
    results, 
    resultsCount,
    pagination: initialPagination 
}: Props) {
    const { query, type, errors, isLoading, setQuery, setType, handleSubmit, handlePageChange } = useSearchForm({
        initialQuery,
        initialType,
        initialPage: initialPagination?.current_page ?? 1,
    });

    const [resultsState, dispatch] = useReducer(
        searchResultsReducer,
        {
            ...initialSearchResultsState,
            people: {
                results: initialType === 'people' ? results : undefined,
                count: initialType === 'people' ? resultsCount : undefined,
                pagination: initialType === 'people' ? initialPagination : undefined,
            },
            movies: {
                results: initialType === 'movies' ? results : undefined,
                count: initialType === 'movies' ? resultsCount : undefined,
                pagination: initialType === 'movies' ? initialPagination : undefined,
            },
        }
    );

    useEffect(() => {
        if (results === undefined) {
            return;
        }

        if (type === 'people') {
            dispatch({
                type: 'SET_PEOPLE_RESULTS',
                payload: {
                    results,
                    count: resultsCount ?? 0,
                    pagination: initialPagination,
                },
            });
        } else {
            dispatch({
                type: 'SET_MOVIES_RESULTS',
                payload: {
                    results,
                    count: resultsCount ?? 0,
                    pagination: initialPagination,
                },
            });
        }
    }, [results, resultsCount, type, initialPagination]);

    // Memoize placeholder text to avoid recalculation
    const placeholderText = useMemo(
        () => (type === 'people' ? 'e.g. Chewbacca, Yoda, Boba Fett' : 'e.g. A New Hope, Empire Strikes Back'),
        [type]
    );

    return (
        <div className="min-h-screen bg-gray-100">
            <Header />
            <div className="container mx-auto px-4" style={{ maxWidth: '1200px' }}>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Left Panel - Search Form */}
                    <SearchPanel
                        query={query}
                        type={type}
                        placeholder={placeholderText}
                        errors={errors}
                        isLoading={isLoading}
                        onQueryChange={setQuery}
                        onTypeChange={setType}
                        onSubmit={handleSubmit}
                    />

                    {/* Right Panel - Results */}
                    {/* Use Activity to preserve results for both search types */}
                    <div className="lg:col-span-2">
                        <Activity mode={type === 'people' ? 'visible' : 'hidden'}>
                            <SearchResultsPanel
                                type="people"
                                results={resultsState.people.results}
                                resultsCount={resultsState.people.count}
                                isLoading={isLoading && type === 'people'}
                                pagination={resultsState.people.pagination}
                                onPageChange={handlePageChange}
                            />
                        </Activity>
                        <Activity mode={type === 'movies' ? 'visible' : 'hidden'}>
                            <SearchResultsPanel
                                type="movies"
                                results={resultsState.movies.results}
                                resultsCount={resultsState.movies.count}
                                isLoading={isLoading && type === 'movies'}
                                pagination={resultsState.movies.pagination}
                                onPageChange={handlePageChange}
                            />
                        </Activity>
                    </div>
                </div>
            </div>
        </div>
    );
}