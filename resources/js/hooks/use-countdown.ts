import { useState, useEffect } from 'react';

export const useCountdown = (initialSeconds: number, onEnd: () => void) => {
    const [secondsLeft, setSecondsLeft] = useState(initialSeconds);

    // Update internal state when initialSeconds prop changes
    useEffect(() => {
        setSecondsLeft(initialSeconds);
    }, [initialSeconds]);

    useEffect(() => {
        if (secondsLeft <= 0) {
            onEnd();
            return;
        }

        const interval = setInterval(() => {
            setSecondsLeft((prev) => prev - 1);
        }, 1000);

        return () => clearInterval(interval);
    }, [secondsLeft, onEnd]);

    const formatTime = (totalSeconds: number) => {
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    };

    return { secondsLeft, formattedTime: formatTime(secondsLeft) };
};
