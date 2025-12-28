import { Link } from '@inertiajs/react';

interface ErrorDisplayProps {
    error: string;
    title?: string;
    showBackLink?: boolean;
}

export default function ErrorDisplay({ 
    error, 
    title = 'Error',
    showBackLink = true 
}: ErrorDisplayProps) {
    return (
        <div className="min-h-screen bg-gray-100">
            <div className="container mx-auto px-4 py-8">
                <div className="bg-white rounded-lg shadow p-8 text-center">
                    <h1 className="text-2xl font-bold text-gray-900 mb-4">{title}</h1>
                    <p className="text-gray-600 mb-4">{error}</p>
                    {showBackLink && (
                        <Link
                            href="/"
                            className="inline-block text-blue-600 hover:text-blue-800"
                        >
                            ← Back to Search
                        </Link>
                    )}
                </div>
            </div>
        </div>
    );
}

