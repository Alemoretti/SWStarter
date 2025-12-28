import { useMemo, memo } from 'react';
import { Link } from '@inertiajs/react';
import ErrorDisplay from '@/Components/ErrorDisplay';
import Header from '@/Components/Header';

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
            <Header />
            <div className="container mx-auto px-4 py-8" style={{ maxWidth: '1200px' }}>
                <div className="bg-white rounded-lg shadow p-8 max-w-4xl mx-auto">
                    <h2 className="text-xl font-semibold text-gray-900 mb-6">
                        {character.name}
                    </h2>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {/* Left Column - Details */}
                        <div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-[10px] block">
                                Details
                            </h3>
                            <div className="w-full h-px mt-2 mb-4" style={{ backgroundColor: 'var(--color-pinkish-grey)' }} />
                            <div>
                                {characterDetails.map((detail) => (
                                    <div key={detail.label} className="text-sm text-black">
                                        <span>{detail.label}:</span>{' '}
                                        <span>{detail.value}</span>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Right Column - Movies */}
                        <div>
                            <h3 className="text-xl font-semibold text-gray-900 mb-[10px] block">
                                Movies
                            </h3>
                            <div className="w-full h-px mt-2 mb-4" style={{ backgroundColor: 'var(--color-pinkish-grey)' }} />
                            {character.movies && character.movies.length > 0 ? (
                                <div className="text-base">
                                    {character.movies.map((movie, index) => (
                                        <span key={movie.id}>
                                            <Link
                                                href={`/movies/${movie.id}`}
                                                className="text-blue-500 hover:text-blue-800"
                                            >
                                                {movie.title}
                                            </Link>
                                            {index < (character.movies?.length ?? 0) - 1 && ', '}
                                        </span>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-xs text-black">No movies found</p>
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
export default memo(CharacterDetail);