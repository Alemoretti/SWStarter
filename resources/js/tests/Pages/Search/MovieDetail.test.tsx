/**
 * Frontend Tests for Movie Detail Page Component
 */

import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen } from '@testing-library/react';
import MovieDetail from '@/Pages/Search/MovieDetail';

// Mock Inertia.js Link component
vi.mock('@inertiajs/react', () => ({
    Link: ({ href, children, ...props }: { href: string; children: React.ReactNode }) => (
        <a href={href} {...props}>
            {children}
        </a>
    ),
}));

describe('MovieDetail', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders movie title and SWStarter title', () => {
        const movie = {
            id: 1,
            title: 'A New Hope',
            opening_crawl: 'It is a period of civil war...',
            characters: [],
        };

        render(<MovieDetail movie={movie} />);

        expect(screen.getByText('SWStarter')).toBeInTheDocument();
        expect(screen.getByText('A New Hope')).toBeInTheDocument();
    });

    it('displays opening crawl in details section', () => {
        const movie = {
            id: 1,
            title: 'A New Hope',
            opening_crawl: 'It is a period of civil war.\n\nRebel spaceships...',
            characters: [],
        };

        render(<MovieDetail movie={movie} />);

        expect(screen.getByText('Details')).toBeInTheDocument();
        expect(screen.getByText(/Opening Crawl:/)).toBeInTheDocument();
        expect(screen.getByText(/It is a period of civil war/)).toBeInTheDocument();
    });

    it('displays characters section with clickable links', () => {
        const movie = {
            id: 1,
            title: 'A New Hope',
            opening_crawl: 'It is a period of civil war...',
            characters: [
                { id: 1, name: 'Luke Skywalker' },
                { id: 2, name: 'Princess Leia' },
            ],
        };

        render(<MovieDetail movie={movie} />);

        expect(screen.getByText('Characters')).toBeInTheDocument();
        expect(screen.getByText('Luke Skywalker')).toBeInTheDocument();
        expect(screen.getByText('Princess Leia')).toBeInTheDocument();
        
        const characterLinks = screen.getAllByRole('link');
        expect(characterLinks.some(link => link.getAttribute('href') === '/characters/1')).toBe(true);
        expect(characterLinks.some(link => link.getAttribute('href') === '/characters/2')).toBe(true);
    });

    it('displays "No characters found" when movie has no characters', () => {
        const movie = {
            id: 1,
            title: 'A New Hope',
            opening_crawl: 'It is a period of civil war...',
            characters: [],
        };

        render(<MovieDetail movie={movie} />);

        expect(screen.getByText('No characters found')).toBeInTheDocument();
    });

    it('displays "No characters found" when characters is undefined', () => {
        const movie = {
            id: 1,
            title: 'A New Hope',
            opening_crawl: 'It is a period of civil war...',
        };

        render(<MovieDetail movie={movie} />);

        expect(screen.getByText('No characters found')).toBeInTheDocument();
    });

    it('displays back to search button', () => {
        const movie = {
            id: 1,
            title: 'A New Hope',
            opening_crawl: 'It is a period of civil war...',
            characters: [],
        };

        render(<MovieDetail movie={movie} />);

        const backButton = screen.getByText('BACK TO SEARCH');
        expect(backButton).toBeInTheDocument();
        expect(backButton.closest('a')).toHaveAttribute('href', '/');
    });

    it('displays error message when movie is not found', () => {
        render(<MovieDetail error="Movie not found" />);

        expect(screen.getByText('Error')).toBeInTheDocument();
        expect(screen.getByText('Movie not found')).toBeInTheDocument();
    });

    it('displays error message when movie is null', () => {
        render(<MovieDetail />);

        expect(screen.getByText('Error')).toBeInTheDocument();
        expect(screen.getByText('Movie not found')).toBeInTheDocument();
    });
});

