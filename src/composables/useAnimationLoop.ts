// useAnimationLoop.ts

import { ref, onUnmounted } from 'vue';

// This composable takes a callback function to run on each frame.
export function useAnimationLoop(onTick: (timestamp: number) => void) {
    const rafId = ref<number | null>(null);

    const start = () => {
        if (rafId.value !== null) {
            cancelAnimationFrame(rafId.value);
        }
        
        const loop = (timestamp: number) => {
            onTick(timestamp);
            rafId.value = requestAnimationFrame(loop);
        };
        rafId.value = requestAnimationFrame(loop);
    };

    const stop = () => {
        if (rafId.value !== null) {
            cancelAnimationFrame(rafId.value);
            rafId.value = null;
        }
    };

    // Automatically stop the loop when the component is unmounted
    onUnmounted(stop);

    return { start, stop };
}