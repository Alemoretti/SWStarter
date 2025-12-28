import { describe, it, expect, vi } from 'vitest';
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import Pagination from '@/components/pagination';

describe('Pagination', () => {
    it('renders pagination with page numbers', () => {
        const onPageChange = vi.fn();
        render(<Pagination currentPage={1} totalPages={5} onPageChange={onPageChange} />);

        expect(screen.getByText('1')).toBeInTheDocument();
        expect(screen.getByText('2')).toBeInTheDocument();
        expect(screen.getByText('5')).toBeInTheDocument();
    });

    it('does not render when totalPages is 1', () => {
        const onPageChange = vi.fn();
        const { container } = render(<Pagination currentPage={1} totalPages={1} onPageChange={onPageChange} />);

        expect(container.firstChild).toBeNull();
    });

    it('renders Previous and Next buttons', () => {
        const onPageChange = vi.fn();
        render(<Pagination currentPage={2} totalPages={5} onPageChange={onPageChange} />);

        expect(screen.getByText('Previous')).toBeInTheDocument();
        expect(screen.getByText('Next')).toBeInTheDocument();
    });

    it('disables Previous button on first page', () => {
        const onPageChange = vi.fn();
        render(<Pagination currentPage={1} totalPages={5} onPageChange={onPageChange} />);

        const previousButton = screen.getByText('Previous');
        expect(previousButton).toBeDisabled();
    });

    it('disables Next button on last page', () => {
        const onPageChange = vi.fn();
        render(<Pagination currentPage={5} totalPages={5} onPageChange={onPageChange} />);

        const nextButton = screen.getByText('Next');
        expect(nextButton).toBeDisabled();
    });

    it('calls onPageChange when clicking a page number', async () => {
        const user = userEvent.setup();
        const onPageChange = vi.fn();
        render(<Pagination currentPage={1} totalPages={5} onPageChange={onPageChange} />);

        const page2Button = screen.getByText('2');
        await user.click(page2Button);

        expect(onPageChange).toHaveBeenCalledWith(2);
    });

    it('calls onPageChange when clicking Next button', async () => {
        const user = userEvent.setup();
        const onPageChange = vi.fn();
        render(<Pagination currentPage={2} totalPages={5} onPageChange={onPageChange} />);

        const nextButton = screen.getByText('Next');
        await user.click(nextButton);

        expect(onPageChange).toHaveBeenCalledWith(3);
    });

    it('calls onPageChange when clicking Previous button', async () => {
        const user = userEvent.setup();
        const onPageChange = vi.fn();
        render(<Pagination currentPage={3} totalPages={5} onPageChange={onPageChange} />);

        const previousButton = screen.getByText('Previous');
        await user.click(previousButton);

        expect(onPageChange).toHaveBeenCalledWith(2);
    });

    it('shows ellipsis when there are many pages', () => {
        const onPageChange = vi.fn();
        render(<Pagination currentPage={5} totalPages={10} onPageChange={onPageChange} />);

        const ellipsis = screen.getAllByText('...');
        expect(ellipsis.length).toBeGreaterThan(0);
    });

    it('highlights the active page', () => {
        const onPageChange = vi.fn();
        render(<Pagination currentPage={3} totalPages={5} onPageChange={onPageChange} />);

        const page3Button = screen.getByText('3');
        expect(page3Button).toHaveAttribute('aria-current', 'page');
    });
});

