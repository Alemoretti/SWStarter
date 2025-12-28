import { memo } from 'react';
import { Link } from '@inertiajs/react';

/**
 * Application header component
 * Memoized to prevent unnecessary re-renders
 */
function Header() {
    return (
        <header className="w-full h-[50px] mb-[30px] py-[14px] shadow-[0_1px_0_0_var(--color-light-grey-shadow)] bg-white flex items-center justify-center">
            <Link
                href="/"
                className="font-montserrat text-lg font-bold cursor-pointer transition-opacity hover:opacity-80 no-underline"
                style={{ color: 'var(--color-green-teal)' }}
            >
                SWStarter
            </Link>
        </header>
    );
}

export default memo(Header);

