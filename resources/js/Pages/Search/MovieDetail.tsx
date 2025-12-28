import { memo } from 'react';
import { Link } from '@inertiajs/react';
import ErrorDisplay from '@/Components/ErrorDisplay';

interface Character {
    id: number;
    name: string;
}

interface Movie {
    id: number;
    title: string;
    opening_crawl: string;
    characters?: Character[];
}

interface Props {
    movie?: Movie;
    error?: string;
}

function MovieDetail({ movie, error }: Props) {
    if (error || !movie) {
        return <ErrorDisplay error={error || 'Movie not found'} />;
    }

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="container mx-auto px-4 py-8">
                <h1 className="text-3xl font-bold text-green-600 mb-8 text-center">
                    SWStarter
                </h1>

                <div className="bg-white rounded-lg shadow p-6 max-w-4xl mx-auto">
                    <h2 className="text-2xl font-semibold text-gray-900 mb-6">
                        {movie.title}
                    </h2>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {/* Left Column - Details */}
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                Details
                            </h3>
                            <div className="space-y-3">
                                <div>
                                    <span className="font-medium text-gray-700">Opening Crawl:</span>
                                    <p className="text-gray-900 mt-2 whitespace-pre-line">
                                        {movie.opening_crawl}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Right Column - Characters */}
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                Characters
                            </h3>
                            {movie.characters && movie.characters.length > 0 ? (
                                <div className="space-y-2">
                                    {movie.characters.map((character) => (
                                        <div key={character.id}>
                                            <Link
                                                href={`/characters/${character.id}`}
                                                className="text-blue-600 hover:text-blue-800 underline"
                                            >
                                                {character.name}
                                            </Link>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-gray-500">No characters found</p>
                            )}
                        </div>
                    </div>
                              
                    <div className="mt-8 text-center">
                        <Link
                            href="/"
                            className="inline-block bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-md transition-colors"
                        >
                            BACK TO SEARCH
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}

// Memoize component to prevent unnecessary re-renders
export default memo(MovieDetail);