interface LoadingStateProps {
    message?: string;
}

export default function LoadingState({ message = 'Loading...' }: LoadingStateProps) {
    return (
        <div className="text-center text-gray-400 py-8">
            {message}
        </div>
    );
}

