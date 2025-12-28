import { memo } from 'react';

function PaginationEllipsis() {
    return (
        <span className="px-2 text-gray-600">
            ...
        </span>
    );
}

export default memo(PaginationEllipsis);

