/**
 * Frontend Tests for Character Detail Page Component
 */

import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen } from '@testing-library/react';
import CharacterDetail from '@/Pages/Search/CharacterDetail';

// Mock Inertia.js Link component
vi.mock('@inertiajs/react', () => ({
    Link: ({ href, children, ...props }: { href: string; children: React.ReactNode }) => (
        <a href={href} {...props}>
            {children}
        </a>
    ),
}));

describe('CharacterDetail', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders character name and title', () => {
        const character = {
            id: 1,
            name: 'Luke Skywalker',
            birth_year: '19BBY',
            gender: 'male',
            eye_color: 'blue',
            hair_color: 'blond',
            height: 172,
            mass: 77,
            films: [],
            movies: [],
        };

        render(<CharacterDetail character={character} />);

        expect(screen.getByText('SWStarter')).toBeInTheDocument();
        expect(screen.getByText('Luke Skywalker')).toBeInTheDocument();
    });

    it('displays character details section', () => {
        const character = {
            id: 1,
            name: 'Luke Skywalker',
            birth_year: '19BBY',
            gender: 'male',
            eye_color: 'blue',
            hair_color: 'blond',
            height: 172,
            mass: 77,
            films: [],
            movies: [],
        };

        render(<CharacterDetail character={character} />);

        expect(screen.getByText('Details')).toBeInTheDocument();
        expect(screen.getByText(/Birth Year:/)).toBeInTheDocument();
        expect(screen.getByText('19BBY')).toBeInTheDocument();
        expect(screen.getByText(/Gender:/)).toBeInTheDocument();
        expect(screen.getByText('male')).toBeInTheDocument();
        expect(screen.getByText(/Eye Color:/)).toBeInTheDocument();
        expect(screen.getByText('blue')).toBeInTheDocument();
        expect(screen.getByText(/Hair Color:/)).toBeInTheDocument();
        expect(screen.getByText('blond')).toBeInTheDocument();
    });

    it('displays movies section with clickable links', () => {
        const character = {
            id: 1,
            name: 'Luke Skywalker',
            birth_year: '19BBY',
            gender: 'male',
            eye_color: 'blue',
            hair_color: 'blond',
            height: 172,
            mass: 77,
            films: [],
            movies: [
                { id: 1, title: 'A New Hope' },
                { id: 2, title: 'The Empire Strikes Back' },
            ],
        };

        render(<CharacterDetail character={character} />);

        expect(screen.getByText('Movies')).toBeInTheDocument();
        expect(screen.getByText('A New Hope')).toBeInTheDocument();
        expect(screen.getByText('The Empire Strikes Back')).toBeInTheDocument();
        
        const movieLinks = screen.getAllByRole('link');
        expect(movieLinks.some(link => link.getAttribute('href') === '/movies/1')).toBe(true);
        expect(movieLinks.some(link => link.getAttribute('href') === '/movies/2')).toBe(true);
    });

    it('displays "No movies found" when character has no movies', () => {
        const character = {
            id: 1,
            name: 'Luke Skywalker',
            birth_year: '19BBY',
            gender: 'male',
            eye_color: 'blue',
            hair_color: 'blond',
            height: 172,
            mass: 77,
            films: [],
            movies: [],
        };

        render(<CharacterDetail character={character} />);

        expect(screen.getByText('No movies found')).toBeInTheDocument();
    });

    it('displays back to search button', () => {
        const character = {
            id: 1,
            name: 'Luke Skywalker',
            birth_year: '19BBY',
            gender: 'male',
            eye_color: 'blue',
            hair_color: 'blond',
            height: 172,
            mass: 77,
            films: [],
            movies: [],
        };

        render(<CharacterDetail character={character} />);

        const backButton = screen.getByText('BACK TO SEARCH');
        expect(backButton).toBeInTheDocument();
        expect(backButton.closest('a')).toHaveAttribute('href', '/');
    });

    it('displays error message when character is not found', () => {
        render(<CharacterDetail error="Character not found" />);

        expect(screen.getByText('Error')).toBeInTheDocument();
        expect(screen.getByText('Character not found')).toBeInTheDocument();
    });

    it('displays error message when character is null', () => {
        render(<CharacterDetail />);

        expect(screen.getByText('Error')).toBeInTheDocument();
        expect(screen.getByText('Character not found')).toBeInTheDocument();
    });
});

