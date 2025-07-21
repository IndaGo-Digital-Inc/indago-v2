// useGlitchLifecycle.ts
// Manages glitch animation lifecycle: targets, intervals, and reset logic
import { Ref, ref, onUnmounted } from "vue";

export interface GlitchConfig {
    minDelay: number;
    maxDelay: number;
    minTransition: number;
    maxTransition: number;
    numGlitchMin: number;
    numGlitchMax: number;
    hideMultiplier: number;
    minRotate: number;
    maxRotate: number;
    minSkew: number;
    maxSkew: number;
    minScale: number;
    maxScale: number;
    scaleProb: number;
    colorPalette: Array<{ value: string; prob: number }>;
    revealMin: number;
    revealMax: number;
    enableAdd: boolean;
    enableRemove: boolean;
    modeChangeDelay: number;
}

export function useGlitchLifecycle(config: GlitchConfig) {
    const glitchInterval = ref<number | null>(null);
    const targets = ref<HTMLElement[]>([]);

    function setTargets(
        refsOrSelector: string | Array<HTMLElement | { value: HTMLElement }>
    ) {
        if (typeof refsOrSelector === "string") {
            targets.value = Array.from(
                document.querySelectorAll(refsOrSelector)
            );
        } else if (Array.isArray(refsOrSelector)) {
            targets.value = refsOrSelector.map((r) =>
                r instanceof HTMLElement ? r : r.value
            );
        }
    }

    onUnmounted(() => {
        if (glitchInterval.value) {
            clearTimeout(glitchInterval.value);
            glitchInterval.value = null;
        }
    });

    return {
        glitchInterval,
        targets,
        setTargets,
    };
}
