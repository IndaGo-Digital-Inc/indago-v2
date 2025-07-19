import { ref } from 'vue';

/**
 * useLivingGlitch composable
 * Handles the post-typing living glitch animation for text components.
 * @param {object} options - { getVisibleCount, glitchMap, randomGlitch }
 * @returns {object} { start, stop }
 */
export function useLivingGlitch({ getVisibleCount, glitchMap, randomGlitch, speed = { min: 200, max: 1500 } }) {
    let glitchTimeout = null;
    let stopped = false;

    function glitchCycle() {
        if (stopped) return;
        const visibleCount = getVisibleCount();
        if (visibleCount > 0) {
            const idx = Math.floor(Math.random() * visibleCount);
            // Allow only 0 (no glitch), 1 (X), or 2 (Y)
            const g = Math.floor(Math.random() * 3); // 0, 1, or 2
            glitchMap.value[idx] = g;
            setTimeout(() => {
                glitchMap.value[idx] = 0;
            }, 200);
        }
        // Randomize next glitch interval using speed.min and speed.max
        const nextDelay = speed.min + Math.random() * (speed.max - speed.min);
        glitchTimeout = setTimeout(glitchCycle, nextDelay);
    }

    function start() {
        stopped = false;
        glitchCycle();
    }

    function stop() {
        stopped = true;
        if (glitchTimeout) clearTimeout(glitchTimeout);
    }

    return { start, stop };
}
