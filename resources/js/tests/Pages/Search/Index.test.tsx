/**
 * Frontend Tests for Search Page Component
 */

import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import SearchIndex from '@/Pages/Search/Index';

// Mock Inertia.js router and Link components to avoid actual navigation
vi.mock('@inertiajs/react', () => ({
    router: {
        post: vi.fn(),
    },
    Link: ({ href, children, ...props }: { href: string; children: React.ReactNode }) => (
        <a href={href} {...props}>
            {children}
        </a>
    ),
}));

describe('SearchIndex', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders the search page with title and form', () => {
        render(<SearchIndex />);

        expect(screen.getByText('SWStarter')).toBeInTheDocument();
        expect(screen.getByText('What are you searching for?')).toBeInTheDocument();
        expect(screen.getByText('Results')).toBeInTheDocument();
        expect(screen.getByRole('textbox')).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /search/i })).toBeInTheDocument();
    });

    it('shows radio buttons for People and Movies', () => {
        render(<SearchIndex />);

        const peopleRadio = screen.getByLabelText('People');
        const moviesRadio = screen.getByLabelText('Movies');

        expect(peopleRadio).toBeInTheDocument();
        expect(moviesRadio).toBeInTheDocument();
        expect(peopleRadio).toBeChecked(); // People is default
    });

    it('allows user to type in search input', () => {
        render(<SearchIndex />);

        const input = screen.getByRole('textbox') as HTMLInputElement;
        fireEvent.change(input, { target: { value: 'luke' } });

        expect(input.value).toBe('luke');
    });

    it('allows user to switch between People and Movies', () => {
        render(<SearchIndex />);

        const moviesRadio = screen.getByLabelText('Movies');
        fireEvent.click(moviesRadio);

        expect(moviesRadio).toBeChecked();
    });

    it('displays "No search performed yet" when no results', () => {
        render(<SearchIndex />);

        expect(screen.getByText('No search performed yet.')).toBeInTheDocument();
    });

    it('displays search results when provided', () => {
        const results = [
            { id: 1, name: 'Luke Skywalker' },
            { id: 2, name: 'Darth Vader' },
        ];

        render(<SearchIndex results={results} resultsCount={2} />);

        expect(screen.getByText('Luke Skywalker')).toBeInTheDocument();
        expect(screen.getByText('Darth Vader')).toBeInTheDocument();
        expect(screen.getAllByText('SEE DETAILS →')).toHaveLength(2);
    });

    it('displays "There are zero matches" when search returns no results', () => {
        render(<SearchIndex results={[]} resultsCount={0} query="nonexistent" />);

        expect(screen.getByText('There are zero matches.')).toBeInTheDocument();
    });

    it('displays movie results correctly', () => {
        const results = [
            { id: 1, title: 'A New Hope' },
            { id: 2, title: 'The Empire Strikes Back' },
        ];

        render(<SearchIndex results={results} resultsCount={2} type="movies" />);

        expect(screen.getByText('A New Hope')).toBeInTheDocument();
        expect(screen.getByText('The Empire Strikes Back')).toBeInTheDocument();
    });

    it('shows validation error when submitting empty query', async () => {
        render(<SearchIndex />);

        const submitButton = screen.getByRole('button', { name: /search/i });
        fireEvent.click(submitButton);

        await waitFor(() => {
            const input = screen.getByRole('textbox');
            expect(input).toHaveClass('border-red-500');
        });
    });

    it('shows loading state when form is submitted', async () => {
        const { router } = await import('@inertiajs/react');
        const mockPost = vi.mocked(router.post);

        render(<SearchIndex />);

        const input = screen.getByRole('textbox') as HTMLInputElement;
        const submitButton = screen.getByRole('button', { name: /search/i });

        // Type a valid query
        fireEvent.change(input, { target: { value: 'luke' } });

        // Submit the form
        fireEvent.click(submitButton);

        // The button text should change to "SEARCHING..."
        await waitFor(() => {
            expect(screen.getByText('SEARCHING...')).toBeInTheDocument();
        });

        // Verify router.post was called
        expect(mockPost).toHaveBeenCalledWith(
            '/api/v1/search',
            { query: 'luke', type: 'people' },
            expect.any(Object)
        );
    });
});

