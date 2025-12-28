import { memo } from 'react';

interface PaginationButtonProps {
    pageNum: number;
    isActive: boolean;
    onClick: () => void;
}

function PaginationButton({ pageNum, isActive, onClick }: PaginationButtonProps) {
    return (
        <button
            onClick={onClick}
            className="px-3 py-1 rounded-full font-bold font-montserrat text-white cursor-pointer transition-all"
            style={
                isActive
                    ? { 
                        backgroundColor: '#089a52',
                        transform: 'scale(1.15)',
                        border: '3px solid white',
                        boxShadow: '0 0 0 2px rgba(10, 180, 99, 0.5)',
                    }
                    : { 
                        backgroundColor: 'var(--color-green-teal)', 
                        opacity: 0.7 
                    }
            }
            aria-label={`Page ${pageNum}`}
            aria-current={isActive ? 'page' : undefined}
        >
            {pageNum}
        </button>
    );
}

export default memo(PaginationButton);

