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
            className={`w-full py-2 px-4 rounded-md font-medium ${
                isLoading || disabled
                    ? 'bg-gray-400 cursor-not-allowed'
                    : 'bg-green-600 hover:bg-green-700'
            } text-white transition-colors uppercase`}
        >
            {isLoading ? 'SEARCHING...' : 'SEARCH'}
        </button>
    );
}

export default memo(SearchButton);

