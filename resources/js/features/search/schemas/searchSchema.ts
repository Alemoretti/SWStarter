import { z } from 'zod';

export const searchSchema = z.object({
    query: z.string().min(1, 'Query is required').max(255, 'Query must be less than 255 characters'),
    type: z.enum(['people', 'movies'], {
        message: 'Type must be either "people" or "movies"',
    }),
});

export type SearchFormData = z.infer<typeof searchSchema>;