import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import Test from '../../Pages/Test';

describe('Test Page', () => {
    it('renders the test page', () => {
        render(<Test />);
        
        expect(screen.getByText('Frontend Setup Complete!')).toBeInTheDocument();
        expect(screen.getByText(/React \+ Inertia \+ Tailwind CSS 4\.x is working/)).toBeInTheDocument();
    });
});