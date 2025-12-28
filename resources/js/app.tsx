import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ErrorBoundary } from './components/ui/ErrorBoundary';

createInertiaApp({
    title: (title) => `${title} - SWStarter`,
    resolve: (name) => {
        // Handle both old format (Search/Index) and new format (Index)
        // Extract just the filename if path includes slashes
        const pageName = name.includes('/') ? name.split('/').pop() || name : name;
        const pages = import.meta.glob('./features/**/pages/**/*.tsx');
        return resolvePageComponent(`./features/search/pages/${pageName}.tsx`, pages);
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <ErrorBoundary>
                <App {...props} />
            </ErrorBoundary>
        );
    },
    progress: {
        color: '#4B5563',
    },
});