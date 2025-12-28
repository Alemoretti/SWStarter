import { memo } from 'react';

interface SearchButtonProps {
    isLoading: boolean;
    disabled?: boolean;
}

/**
 * Search submit button component
 * Memoized to prevent unnecessary re-renders
 */
function SearchButton({ isLoading, disabled = false }: SearchButtonProps) {
    return (
        <button
            type="submit"
            disabled={disabled || isLoading}
            className={`w-full h-[34px] px-4 text-sm rounded-full font-bold font-montserrat uppercase text-white flex items-center justify-center ${
                isLoading || disabled
                    ? 'cursor-not-allowed'
                    : 'transition-colors'
            }`}
            style={
                !isLoading && !disabled
                    ? { backgroundColor: 'var(--color-green-teal)' }
                    : { backgroundColor: 'var(--color-pinkish-grey)' }
            }
        >
            {isLoading ? 'SEARCHING...' : 'SEARCH'}
        </button>
    );
}

export default memo(SearchButton);

