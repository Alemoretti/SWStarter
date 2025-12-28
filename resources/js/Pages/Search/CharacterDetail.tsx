import { useMemo, memo } from 'react';
import { Link } from '@inertiajs/react';
import ErrorDisplay from '@/Components/ErrorDisplay';

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

function CharacterDetail({ character, error }: Props) {
    // Memoize character details to avoid recalculation
    const characterDetails = useMemo(
        () => {
            if (!character) return [];
            return [
                { label: 'Birth Year', value: character.birth_year || 'Unknown' },
                { label: 'Gender', value: character.gender },
                { label: 'Eye Color', value: character.eye_color },
                { label: 'Hair Color', value: character.hair_color },
                ...(character.height !== null ? [{ label: 'Height', value: character.height }] : []),
                ...(character.mass !== null ? [{ label: 'Mass', value: character.mass }] : []),
            ];
        },
        [character]
    );

    if (error || !character) {
        return <ErrorDisplay error={error || 'Character not found'} />;
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
                                {characterDetails.map((detail) => (
                                    <div key={detail.label}>
                                        <span className="font-medium text-gray-700">{detail.label}:</span>{' '}
                                        <span className="text-gray-900">{detail.value}</span>
                                    </div>
                                ))}
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

// Memoize component to prevent unnecessary re-renders
export default memo(CharacterDetail);