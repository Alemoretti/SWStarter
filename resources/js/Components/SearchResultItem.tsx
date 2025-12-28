import { memo } from 'react';
import { Link } from '@inertiajs/react';

interface SearchResultItemProps {
    id: number;
    name?: string;
    title?: string;
    type: 'people' | 'movies';
    showSeparator?: boolean;
}

/**
 * Individual search result item component
 * Memoized to prevent unnecessary re-renders when parent re-renders
 */
function SearchResultItem({ id, name, title, type, showSeparator = false }: SearchResultItemProps) {
    const displayName = name || title || 'Unknown';
    const detailUrl = `/${type === 'people' ? 'characters' : 'movies'}/${id}`;

    return (
        <>
            <div className="py-[10px]">
                <div className="flex items-center justify-between w-full">
                    <h3 className="font-montserrat text-sm font-bold text-black flex-shrink-0">
                        {displayName}
                    </h3>
                    <Link
                        href={detailUrl}
                        className="min-w-[134px] h-[34px] px-5 py-2 rounded-[17px] font-montserrat text-sm font-bold text-white uppercase border-none cursor-pointer transition-colors duration-200 inline-flex items-center justify-center no-underline whitespace-nowrap"
                        style={{ 
                            backgroundColor: 'var(--color-green-teal)',
                        }}
                        onMouseEnter={(e) => {
                            e.currentTarget.style.backgroundColor = 'var(--color-green-teal-hover)';
                        }}
                        onMouseLeave={(e) => {
                            e.currentTarget.style.backgroundColor = 'var(--color-green-teal)';
                        }}
                    >
                        SEE DETAILS
                    </Link>
                </div>
            </div>
            {showSeparator && (
                <div className="w-full h-px mt-2 mb-0" style={{ backgroundColor: 'var(--color-pinkish-grey)' }} />
            )}
        </>
    );
}

export default memo(SearchResultItem);

