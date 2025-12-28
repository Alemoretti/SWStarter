import { memo } from 'react';

interface PaginationNavButtonProps {
    label: string;
    onClick: () => void;
    disabled: boolean;
    ariaLabel: string;
}

function PaginationNavButton({ label, onClick, disabled, ariaLabel }: PaginationNavButtonProps) {
    return (
        <button
            onClick={onClick}
            disabled={disabled}
            className="px-3 py-1 rounded-full font-bold font-montserrat text-white disabled:cursor-not-allowed cursor-pointer transition-colors"
            style={
                disabled
                    ? { backgroundColor: 'var(--color-pinkish-grey)' }
                    : { backgroundColor: 'var(--color-green-teal)' }
            }
            aria-label={ariaLabel}
        >
            {label}
        </button>
    );
}

export default memo(PaginationNavButton);

