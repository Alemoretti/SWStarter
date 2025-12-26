import { Link } from '@inertiajs/react';

interface Movie {
    id: number;
    title: string;
}

interface Character {
    id: number;
    name: string;
    birth_year: string | null;
    gender: string;
    eye_color: string;
    hair_color: string;
    height: number | null;
    mass: number | null;
    films: string[];
    movies?: Movie[];
}

interface Props {
    character?: Character;
    error?: string;
}

export default function CharacterDetail({ character, error }: Props) {
    if (error || !character) {
        return (
            <div className="min-h-screen bg-gray-100">
                <div className="container mx-auto px-4 py-8">
                    <div className="bg-white rounded-lg shadow p-8 text-center">
                        <h1 className="text-2xl font-bold text-gray-900 mb-4">Error</h1>
                        <p className="text-gray-600 mb-4">{error || 'Character not found'}</p>
                        <Link
                            href="/"
                            className="inline-block text-blue-600 hover:text-blue-800"
                        >
                            ← Back to Search
                        </Link>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="container mx-auto px-4 py-8">
                <h1 className="text-3xl font-bold text-green-600 mb-8 text-center">
                    SWStarter
                </h1>

                <div className="bg-white rounded-lg shadow p-6 max-w-4xl mx-auto">
                    <h2 className="text-2xl font-semibold text-gray-900 mb-6">
                        {character.name}
                    </h2>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {/* Left Column - Details */}
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                Details
                            </h3>
                            <div className="space-y-3">
                                <div>
                                    <span className="font-medium text-gray-700">Birth Year:</span>{' '}
                                    <span className="text-gray-900">{character.birth_year || 'Unknown'}</span>
                                </div>
                                <div>
                                    <span className="font-medium text-gray-700">Gender:</span>{' '}
                                    <span className="text-gray-900">{character.gender}</span>
                                </div>
                                <div>
                                    <span className="font-medium text-gray-700">Eye Color:</span>{' '}
                                    <span className="text-gray-900">{character.eye_color}</span>
                                </div>
                                <div>
                                    <span className="font-medium text-gray-700">Hair Color:</span>{' '}
                                    <span className="text-gray-900">{character.hair_color}</span>
                                </div>
                                {character.height !== null && (
                                    <div>
                                        <span className="font-medium text-gray-700">Height:</span>{' '}
                                        <span className="text-gray-900">{character.height}</span>
                                    </div>
                                )}
                                {character.mass !== null && (
                                    <div>
                                        <span className="font-medium text-gray-700">Mass:</span>{' '}
                                        <span className="text-gray-900">{character.mass}</span>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Right Column - Movies */}
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                Movies
                            </h3>
                            {character.movies && character.movies.length > 0 ? (
                                <div className="space-y-2">
                                    {character.movies.map((movie) => (
                                        <div key={movie.id}>
                                            <Link
                                                href={`/movies/${movie.id}`}
                                                className="text-blue-600 hover:text-blue-800 underline"
                                            >
                                                {movie.title}
                                            </Link>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-gray-500">No movies found</p>
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