import { Link } from '@inertiajs/react';

interface Result {
    id: number;
    name?: string;
    title?: string;
    [key: string]: unknown;
}

interface Props {
    query: string;
    type: 'people' | 'movies';
    results: Result[];
    resultsCount: number;
}

export default function SearchResults({ query, type, results, resultsCount }: Props) {
    return (
        <div className="min-h-screen bg-gray-100">
            <div className="container mx-auto px-4 py-8">
                <h1 className="text-3xl font-bold text-gray-900 mb-4">
                    Search Results
                </h1>

                <p className="text-gray-600 mb-6">
                    Found {resultsCount} result{resultsCount !== 1 ? 's' : ''} for &quot;{query}&quot;
                </p>

                {resultsCount === 0 ? (
                    <div className="bg-white rounded-lg shadow p-8 text-center">
                        <p className="text-gray-600 text-lg">
                            There are zero matches.
                        </p>
                        <Link
                            href="/"
                            className="mt-4 inline-block text-blue-600 hover:text-blue-800"
                        >
                            ← Back to Search
                        </Link>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {results.map((result) => (
                            <div
                                key={result.id}
                                className="bg-white rounded-lg shadow p-6"
                            >
                                <h2 className="text-xl font-semibold text-gray-900 mb-2">
                                    {result.name || result.title}
                                </h2>
                                <Link
                                    href={`/${type === 'people' ? 'characters' : 'movies'}/${result.id}`}
                                    className="text-blue-600 hover:text-blue-800"
                                >
                                    SEE DETAILS →
                                </Link>
                            </div>
                        ))}
                        <Link
                            href="/"
                            className="inline-block mt-4 text-blue-600 hover:text-blue-800"
                        >
                            ← Back to Search
                        </Link>
                    </div>
                )}
            </div>
        </div>
    );
}