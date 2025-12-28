import React, { Component, ReactNode } from 'react';
import { Link } from '@inertiajs/react';
import Header from '@/components/layout/Header';

interface Props {
    children: ReactNode;
    fallback?: ReactNode;
}

interface State {
    hasError: boolean;
    error: Error | null;
}

/**
 * Error Boundary component.
 * Provides a fallback UI when errors occur.
 */
export class ErrorBoundary extends Component<Props, State> {
    constructor(props: Props) {
        super(props);
        this.state = { hasError: false, error: null };
    }

    static getDerivedStateFromError(error: Error): State {
        return { hasError: true, error };
    }

    componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
        // Log error to console in development
        if (import.meta.env.DEV) {
            console.error('ErrorBoundary caught an error:', error, errorInfo);
        }
    }

    render() {
        if (this.state.hasError) {
            if (this.props.fallback) {
                return this.props.fallback;
            }

            return (
                <div className="min-h-screen bg-gray-100">
                    <Header />
                    <div className="container mx-auto px-4">
                        <div className="bg-white rounded-lg shadow p-8 text-center">
                            <h1 className="text-2xl font-bold text-gray-900 mb-4">Something went wrong</h1>
                            <p className="text-gray-600 mb-4">
                                {this.state.error?.message || 'An unexpected error occurred'}
                            </p>
                            <div className="space-x-4">
                                <button
                                    onClick={() => this.setState({ hasError: false, error: null })}
                                    className="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors"
                                >
                                    Try again
                                </button>
                                <Link
                                    href="/"
                                    className="inline-block bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-md transition-colors"
                                >
                                    Back to Search
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }

        return this.props.children;
    }
}

