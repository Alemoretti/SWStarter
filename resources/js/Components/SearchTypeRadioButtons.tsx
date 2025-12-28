import { memo, useCallback } from 'react';

interface SearchTypeRadioButtonsProps {
    type: 'people' | 'movies';
    onChange: (type: 'people' | 'movies') => void;
    disabled?: boolean;
}

/**
 * Radio button group for selecting search type (People or Movies)
 * Memoized to prevent unnecessary re-renders
 */
function SearchTypeRadioButtons({ type, onChange, disabled = false }: SearchTypeRadioButtonsProps) {
    const handleChange = useCallback(
        (e: React.ChangeEvent<HTMLInputElement>) => {
            if (!disabled) {
                onChange(e.target.value as 'people' | 'movies');
            }
        },
        [onChange, disabled]
    );

    return (
        <div className="mb-4">
            <div className="flex gap-4">
                <label className="flex items-center font-bold">
                    <input
                        type="radio"
                        name="type"
                        value="people"
                        checked={type === 'people'}
                        onChange={handleChange}
                        className="mr-2"
                        disabled={disabled}
                    />
                    People
                </label>
                <label className="flex items-center font-bold">
                    <input
                        type="radio"
                        name="type"
                        value="movies"
                        checked={type === 'movies'}
                        onChange={handleChange}
                        className="mr-2"
                        disabled={disabled}
                    />
                    Movies
                </label>
            </div>
        </div>
    );
}

export default memo(SearchTypeRadioButtons);

