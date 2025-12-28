import { memo } from 'react';
import SearchTypeRadioButtons from './SearchTypeRadioButtons';
import SearchInput from './SearchInput';
import SearchButton from './SearchButton';

interface SearchPanelProps {
    query: string;
    type: 'people' | 'movies';
    placeholder: string;
    errors: { query?: string };
    isLoading: boolean;
    onQueryChange: (value: string) => void;
    onTypeChange: (type: 'people' | 'movies') => void;
    onSubmit: (e: React.FormEvent<HTMLFormElement>) => void;
}

/**
 * Search form panel component
 * Memoized to prevent unnecessary re-renders
 */
function SearchPanel({
    query,
    type,
    placeholder,
    errors,
    isLoading,
    onQueryChange,
    onTypeChange,
    onSubmit,
}: SearchPanelProps) {
    return (
        <div 
            className="bg-white rounded-sm p-8 lg:col-span-1 lg:self-start"
            style={{ boxShadow: '0 2px 4px var(--color-warm-grey-shadow)' }}
        >
            <h2 className="font-montserrat text-sm font-semibold mb-5" style={{ color: 'var(--color-dark-grey)' }}>
                What are you searching for?
            </h2>

            <form onSubmit={onSubmit}>
                <SearchTypeRadioButtons
                    type={type}
                    onChange={onTypeChange}
                    disabled={isLoading}
                />

                <SearchInput
                    value={query}
                    onChange={onQueryChange}
                    placeholder={placeholder}
                    error={errors.query}
                    disabled={isLoading}
                />

                <SearchButton isLoading={isLoading} disabled={!query.trim()} />
            </form>
        </div>
    );
}

export default memo(SearchPanel);

