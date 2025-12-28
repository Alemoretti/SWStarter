import { memo } from 'react';
import PaginationButton from './PaginationButton';
import PaginationNavButton from './PaginationNavButton';
import PaginationEllipsis from './PaginationEllipsis';

interface PaginationProps {
    currentPage: number;
    totalPages: number;
    onPageChange: (page: number) => void;
}

function Pagination({ currentPage, totalPages, onPageChange }: PaginationProps) {
    // Ensure currentPage is a number
    const currentPageNum = Number(currentPage);
    const totalPagesNum = Number(totalPages);
    
    if (totalPagesNum <= 1) {
        return null;
    }

    const getPageNumbers = () => {
        const pages: (number | string)[] = [];
        const maxVisible = 5;

        if (totalPagesNum <= maxVisible) {
            // Show all pages if total is less than max visible
            for (let i = 1; i <= totalPagesNum; i++) {
                pages.push(i);
            }
        } else {
            // Always show first page
            pages.push(1);

            // Calculate the range of pages to show around current page
            const start = Math.max(2, currentPageNum - 1);
            const end = Math.min(totalPagesNum - 1, currentPageNum + 1);

            // Show ellipsis before the range if there's a gap
            if (start > 2) {
                pages.push('...');
            }

            // Show pages around current page
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            // Show ellipsis after the range if there's a gap before the last page
            if (end < totalPagesNum - 1) {
                pages.push('...');
            }

            // Always show last page
            pages.push(totalPagesNum);
        }

        return pages;
    };

    const pageNumbers = getPageNumbers();

    return (
        <div className="flex items-center justify-center gap-3 mt-6">
            <PaginationNavButton
                label="Previous"
                onClick={() => onPageChange(currentPageNum - 1)}
                disabled={currentPageNum === 1}
                ariaLabel="Previous page"
            />

            <div className="flex items-center gap-2">
                {pageNumbers.map((page, index) => {
                    if (page === '...') {
                        return <PaginationEllipsis key={`ellipsis-${index}`} />;
                    }

                    const pageNum = page as number;
                    const isActive = Number(pageNum) === currentPageNum;

                    return (
                        <PaginationButton
                            key={pageNum}
                            pageNum={pageNum}
                            isActive={isActive}
                            onClick={() => onPageChange(pageNum)}
                        />
                    );
                })}
            </div>

            <PaginationNavButton
                label="Next"
                onClick={() => onPageChange(currentPageNum + 1)}
                disabled={currentPageNum === totalPagesNum}
                ariaLabel="Next page"
            />
        </div>
    );
}

export default memo(Pagination);

