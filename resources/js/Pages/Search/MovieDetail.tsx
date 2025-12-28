import { memo } from 'react';
import { Link } from '@inertiajs/react';
import ErrorDisplay from '@/Components/ErrorDisplay';
import Header from '@/Components/Header';

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
            <Header />
            <div className="container mx-auto px-4 py-8" style={{ maxWidth: '1200px' }}>
                <div className="bg-white rounded-lg shadow p-8 max-w-4xl mx-auto">
                    <h2 className="text-xl font-semibold text-gray-900 mb-6">
                        {movie.title}
                    </h2>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {/* Left Column - Details */}
                        <div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-[10px] block">
                                Opening Crawl
                            </h3>
                            <div className="w-full h-px mt-2 mb-4" style={{ backgroundColor: 'var(--color-pinkish-grey)' }} />
                            <div>
                                <div className="text-base text-black">
                                    <p className="mt-2 whitespace-pre-line text-base">
                                        {movie.opening_crawl}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Right Column - Characters */}
                        <div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-[10px] block">
                                Characters
                            </h3>
                            <div className="w-full h-px mt-2 mb-4" style={{ backgroundColor: 'var(--color-pinkish-grey)' }} />
                            {movie.characters && movie.characters.length > 0 ? (
                                <div className="text-base">
                                    {movie.characters.map((character, index) => (
                                        <span key={character.id}>
                                            <Link
                                                href={`/characters/${character.id}`}
                                                className="text-blue-500 hover:text-blue-800"
                                            >
                                                {character.name}
                                            </Link>
                                            {index < (movie.characters?.length ?? 0) - 1 && ', '}
                                        </span>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-xs text-black">No characters found</p>
                            )}
                        </div>
                    </div>
                              
                    <div className="mt-8">
                        <Link
                            href="/"
                            className="inline-block bg-green-600 hover:bg-green-700 text-white font-bold font-montserrat py-2 px-6 rounded-md transition-colors uppercase"
                            style={{ backgroundColor: 'var(--color-green-teal)' }}
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