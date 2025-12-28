import { memo, useCallback } from 'react';

interface SearchInputProps {
    value: string;
    onChange: (value: string) => void;
    placeholder: string;
    error?: string;
    disabled?: boolean;
}

/**
 * Search input field component
 * Memoized to prevent unnecessary re-renders
 */
function SearchInput({ value, onChange, placeholder, error, disabled = false }: SearchInputProps) {
    const handleChange = useCallback(
        (e: React.ChangeEvent<HTMLInputElement>) => {
            onChange(e.target.value);
        },
        [onChange]
    );

    return (
        <div className="mb-4">
            <input
                type="text"
                id="query"
                value={value}
                onChange={handleChange}
                className={`w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-bold shadow-[inset_0_1px_0_0_rgba(0,0,0,0.1)] ${
                    error ? 'border-red-500' : 'border-gray-300'
                }`}
                placeholder={placeholder}
                disabled={disabled}
            />
            {error && (
                <p className="mt-1 text-sm text-red-600">{error}</p>
            )}
        </div>
    );
}

export default memo(SearchInput);

