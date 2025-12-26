import { useState } from 'react';
import { router, Link } from '@inertiajs/react';
import { searchSchema } from '@/schemas/searchSchema';

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
    const [query, setQuery] = useState(initialQuery);
    const [type, setType] = useState<'people' | 'movies'>(initialType);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
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

        // Submit search via Inertia - will return to same page with results
        router.post('/api/v1/search', result.data, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setIsLoading(false),
        });
    };

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="container mx-auto px-4 py-8">
                <h1 className="text-3xl font-bold text-gray-900 mb-8 text-center">
                    SWStarter
                </h1>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Left Panel - Search Form */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h2 className="text-xl font-semibold text-gray-900 mb-4">
                            What are you searching for?
                        </h2>

                        <form onSubmit={handleSubmit}>
                            {/* Radio buttons */}
                            <div className="mb-4">
                                <div className="flex gap-4">
                                    <label className="flex items-center">
                                        <input
                                            type="radio"
                                            name="type"
                                            value="people"
                                            checked={type === 'people'}
                                            onChange={(e) => setType(e.target.value as 'people' | 'movies')}
                                            className="mr-2"
                                            disabled={isLoading}
                                        />
                                        People
                                    </label>
                                    <label className="flex items-center">
                                        <input
                                            type="radio"
                                            name="type"
                                            value="movies"
                                            checked={type === 'movies'}
                                            onChange={(e) => setType(e.target.value as 'people' | 'movies')}
                                            className="mr-2"
                                            disabled={isLoading}
                                        />
                                        Movies
                                    </label>
                                </div>
                            </div>

                            {/* Search input */}
                            <div className="mb-4">
                                <input
                                    type="text"
                                    id="query"
                                    value={query}
                                    onChange={(e) => setQuery(e.target.value)}
                                    className={`w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                        errors.query ? 'border-red-500' : 'border-gray-300'
                                    }`}
                                    placeholder={type === 'people' ? 'e.g. Chewbacca, Yoda, Boba Fett' : 'e.g. A New Hope, Empire Strikes Back'}
                                    disabled={isLoading}
                                />
                                {errors.query && (
                                    <p className="mt-1 text-sm text-red-600">{errors.query}</p>
                                )}
                            </div>

                            {/* Submit button */}
                            <button
                                type="submit"
                                disabled={isLoading}
                                className={`w-full py-2 px-4 rounded-md font-medium ${
                                    isLoading
                                        ? 'bg-gray-400 cursor-not-allowed'
                                        : 'bg-green-600 hover:bg-green-700'
                                } text-white transition-colors uppercase`}
                            >
                                {isLoading ? 'SEARCHING...' : 'SEARCH'}
                            </button>
                        </form>
                    </div>

                    {/* Right Panel - Results */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h2 className="text-xl font-semibold text-gray-900 mb-4">
                            Results
                        </h2>
                        <div className="border-t border-gray-200 pt-4">
                            {isLoading ? (
                                <div className="text-center text-gray-400 py-8">
                                    Searching...
                                </div>
                            ) : results && results.length > 0 ? (
                                <div className="space-y-4">
                                    {results.map((result) => (
                                        <div
                                            key={result.id}
                                            className="border-b border-gray-200 pb-4 last:border-b-0"
                                        >
                                            <h3 className="text-lg font-medium text-gray-900 mb-2">
                                                {result.name || result.title}
                                            </h3>
                                            <Link
                                                href={`/${type === 'people' ? 'characters' : 'movies'}/${result.id}`}
                                                className="text-blue-600 hover:text-blue-800"
                                            >
                                                SEE DETAILS →
                                            </Link>
                                        </div>
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
                </div>
            </div>
        </div>
    );
}