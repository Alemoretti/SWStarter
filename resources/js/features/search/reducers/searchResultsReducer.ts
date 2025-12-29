import { SearchResultsState, SearchResult, PaginationData } from '@/types/api';

export type SearchResultsAction =
    | { type: 'SET_PEOPLE_RESULTS'; payload: { results: SearchResult[]; count: number; pagination: PaginationData | undefined } }
    | { type: 'SET_MOVIES_RESULTS'; payload: { results: SearchResult[]; count: number; pagination: PaginationData | undefined } }
    | { type: 'CLEAR_RESULTS' };

export const initialSearchResultsState: SearchResultsState = {
    people: {
        results: undefined,
        count: undefined,
        pagination: undefined,
    },
    movies: {
        results: undefined,
        count: undefined,
        pagination: undefined,
    },
};

export function searchResultsReducer(
    state: SearchResultsState,
    action: SearchResultsAction
): SearchResultsState {
    switch (action.type) {
        case 'SET_PEOPLE_RESULTS':
            return {
                ...state,
                people: {
                    results: action.payload.results,
                    count: action.payload.count,
                    pagination: action.payload.pagination,
                },
            };
        case 'SET_MOVIES_RESULTS':
            return {
                ...state,
                movies: {
                    results: action.payload.results,
                    count: action.payload.count,
                    pagination: action.payload.pagination,
                },
            };
        case 'CLEAR_RESULTS':
            return initialSearchResultsState;
        default:
            return state;
    }
}

