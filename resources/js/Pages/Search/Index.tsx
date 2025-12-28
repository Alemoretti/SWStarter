import { useMemo, useState, useEffect } from 'react';
import { Activity } from 'react';
import { useSearchForm } from '@/hooks/useSearchForm';
import SearchResultsPanel from '@/Components/SearchResultsPanel';
import SearchTypeRadioButtons from '@/Components/SearchTypeRadioButtons';
import SearchInput from '@/Components/SearchInput';
import SearchButton from '@/Components/SearchButton';

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

    // Update the results state when new results arrive from server
    useEffect(() => {
        if (results !== undefined) {
            if (type === 'people') {
                setPeopleResults(results);
                setPeopleResultsCount(resultsCount);
            } else {
                setMoviesResults(results);
                setMoviesResultsCount(resultsCount);
            }
        }
    }, [results, resultsCount, type]);

    // Memoize placeholder text to avoid recalculation
    const placeholderText = useMemo(
        () => (type === 'people' ? 'e.g. Chewbacca, Yoda, Boba Fett' : 'e.g. A New Hope, Empire Strikes Back'),
        [type]
    );

    return (
        <div className="min-h-screen bg-gray-100">
            <header className="w-full h-[50px] mb-[30px] py-[14px] shadow-[0_1px_0_0_var(--color-light-grey-shadow)] bg-white flex items-center justify-center">
                <h1 className="font-montserrat text-lg font-bold" style={{ color: 'var(--color-green-teal)' }}>
                    SWStarter
                </h1>
            </header>
            <div className="container mx-auto px-4 py-8" style={{ maxWidth: '1200px' }}>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Left Panel - Search Form */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h2 className="font-montserrat text-sm font-semibold mb-5" style={{ color: 'var(--color-dark-grey)' }}>
                            What are you searching for?
                        </h2>

                        <form onSubmit={handleSubmit}>
                            <SearchTypeRadioButtons
                                type={type}
                                onChange={setType}
                                disabled={isLoading}
                            />

                            <SearchInput
                                value={query}
                                onChange={setQuery}
                                placeholder={placeholderText}
                                error={errors.query}
                                disabled={isLoading}
                            />

                            <SearchButton isLoading={isLoading} />
                        </form>
                    </div>

                    {/* Right Panel - Results */}
                    {/* Use Activity to preserve results for both search types */}
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
    );
}