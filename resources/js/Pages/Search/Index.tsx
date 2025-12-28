import { useMemo, useState, useEffect } from 'react';
import { Activity } from 'react';
import { useSearchForm } from '@/hooks/useSearchForm';
import SearchResultsPanel from '@/Components/SearchResultsPanel';
import SearchPanel from '@/Components/SearchPanel';
import Header from '@/Components/Header';

interface Result {
    id: number;
    name?: string;
    title?: string;
    [key: string]: unknown;
}

interface Props {
    query?: string;
    type?: 'people' | 'movies';
    results?: Result[];
    resultsCount?: number;
}

export default function SearchIndex({ query: initialQuery = '', type: initialType = 'people', results, resultsCount }: Props) {
    const { query, type, errors, isLoading, setQuery, setType, handleSubmit } = useSearchForm({
        initialQuery,
        initialType,
    });

    // Preserve results for both search types using local state
    const [peopleResults, setPeopleResults] = useState<Result[] | undefined>(
        initialType === 'people' ? results : undefined
    );
    const [peopleResultsCount, setPeopleResultsCount] = useState<number | undefined>(
        initialType === 'people' ? resultsCount : undefined
    );
    const [moviesResults, setMoviesResults] = useState<Result[] | undefined>(
        initialType === 'movies' ? results : undefined
    );
    const [moviesResultsCount, setMoviesResultsCount] = useState<number | undefined>(
        initialType === 'movies' ? resultsCount : undefined
    );

    // Update the appropriate results state when new results arrive from server
    useEffect(() => {
        if (results === undefined) {
            return;
        }

        // Use setTimeout to defer state updates and avoid cascading renders
        const timeoutId = setTimeout(() => {
            if (type === 'people') {
                setPeopleResults(results);
                setPeopleResultsCount(resultsCount);
            } else {
                setMoviesResults(results);
                setMoviesResultsCount(resultsCount);
            }
        }, 0);

        return () => clearTimeout(timeoutId);
    }, [results, resultsCount, type]);

    // Memoize placeholder text to avoid recalculation
    const placeholderText = useMemo(
        () => (type === 'people' ? 'e.g. Chewbacca, Yoda, Boba Fett' : 'e.g. A New Hope, Empire Strikes Back'),
        [type]
    );

    return (
        <div className="min-h-screen bg-gray-100">
            <Header />
            <div className="container mx-auto px-4 py-8" style={{ maxWidth: '1200px' }}>

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
                                results={peopleResults}
                                resultsCount={peopleResultsCount}
                                isLoading={isLoading && type === 'people'}
                            />
                        </Activity>
                        <Activity mode={type === 'movies' ? 'visible' : 'hidden'}>
                            <SearchResultsPanel
                                type="movies"
                                results={moviesResults}
                                resultsCount={moviesResultsCount}
                                isLoading={isLoading && type === 'movies'}
                            />
                        </Activity>
                    </div>
                </div>
            </div>
        </div>
    );
}