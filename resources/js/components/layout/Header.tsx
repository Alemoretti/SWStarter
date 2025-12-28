import { memo } from 'react';

/**
 * Application header component
 * Memoized to prevent unnecessary re-renders
 */
function Header() {
    return (
        <header className="w-full h-[50px] mb-[30px] py-[14px] shadow-[0_1px_0_0_var(--color-light-grey-shadow)] bg-white flex items-center justify-center">
            <h1 className="font-montserrat text-lg font-bold" style={{ color: 'var(--color-green-teal)' }}>
                SWStarter
            </h1>
        </header>
    );
}

export default memo(Header);

