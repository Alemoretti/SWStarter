export interface Character {
    id: number;
    name: string;
    birth_year: string | null;
    gender: string;
    eye_color: string;
    hair_color: string;
    height: number | null;
    mass: number | null;
    films: string[];
    movies?: Movie[];
}

export interface Movie {
    id: number;
    title: string;
    opening_crawl: string;
    characters?: Character[];
}

export interface PaginationData {
    current_page: number;
    per_page: number;
    total: number;
    total_pages: number;
}

export type SearchType = 'people' | 'movies';

export interface SearchResult {
    id: number;
    name?: string;
    title?: string;
}

export interface SearchResultsState {
    people: {
        results: SearchResult[] | undefined;
        count: number | undefined;
        pagination: PaginationData | undefined;
    };
    movies: {
        results: SearchResult[] | undefined;
        count: number | undefined;
        pagination: PaginationData | undefined;
    };
}

