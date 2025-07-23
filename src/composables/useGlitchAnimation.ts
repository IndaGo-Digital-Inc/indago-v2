// src/composables/useGlitchAnimation.ts
import { ref, onUnmounted, computed, type Ref, nextTick } from 'vue';
import { useAnimationLoop } from './useAnimationLoop';

export interface GlitchConfig {
    minDelay: number;
    maxDelay: number;
    minTransition: number;
    maxTransition: number;
    numGlitchMin: number;
    numGlitchMax: number;
    minRotate: number;
    maxRotate: number;
    minSkew: number;
    maxSkew: number;
    minScale: number;
    maxScale: number;
    scaleProb: number;
    colorPalette: Array<{ value: string; prob: number }>;
    enableAdd: boolean;
    enableRemove: boolean;
    modeChangeDelay: number;
    resetDelay: number;
}

interface LetterState {
    char: string;
    isVisible: boolean;
    isGlitching: boolean;
    glitchStyles: object;
}

export function useGlitchAnimation(config: Ref<GlitchConfig>, onComplete?: () => void) {
    const words = ref<LetterState[][]>([]);
    const allLetters = computed(() => words.value.flat());
    
    const animationState = ref('IDLE');
    const currentGlitchCount = ref(0);

    let lastFrameTime = 0;
    let nextDelay = 0;
    let pauseTimeout: number | null = null;
    
    const loop = useAnimationLoop(onTick);
    onUnmounted(() => {
      loop.stop();
      if (pauseTimeout) clearTimeout(pauseTimeout);
    });

    // --- Private Helpers (Unchanged) ---
    function pickRandomIndices(total: number, count: number) {
        const indices = new Set<number>();
        while (indices.size < count && indices.size < total) {
            indices.add(Math.floor(Math.random() * total));
        }
        return Array.from(indices);
    }

    function generateGlitchStyles() {
        const { minTransition, maxTransition, minRotate, maxRotate, minSkew, maxSkew, minScale, maxScale, scaleProb, colorPalette } = config.value;
        let cumulativeProb = 0;
        const rand = Math.random();
        const color = colorPalette.find(c => (cumulativeProb += c.prob) >= rand)?.value || colorPalette[0]?.value || '#FFF';
        const transitionDuration = `${(minTransition + Math.random() * (maxTransition - minTransition)).toFixed(2)}s`;
        const rot = () => `${(minRotate + Math.random() * (maxRotate - minRotate)).toFixed(2)}deg`;
        const skew = () => `${(minSkew + Math.random() * (maxSkew - minSkew)).toFixed(2)}deg`;
        const scale = Math.random() < scaleProb ? (minScale + Math.random() * (maxScale - minScale)).toFixed(2) : '1';
        const styles = {
            '--glitch-transform': `rotateX(${rot()}) rotateY(${rot()}) rotateZ(${rot()}) skewX(${skew()}) skewY(${skew()}) scale(${scale})`,
            '--glitch-color': color,
            '--glitch-transition-duration': transitionDuration,
        };
        return { styles, durationMs: parseFloat(transitionDuration) * 1000 };
    }

    // --- Updated Animation Logic ---
    function onTick(now: number) {
        if (allLetters.value.length === 0 || animationState.value === 'IDLE') return;
        if (!lastFrameTime) lastFrameTime = now;
        const elapsed = now - lastFrameTime;

        if (elapsed < nextDelay) return;
        
        let candidates;
        if (animationState.value === 'REVEALING') {
            candidates = allLetters.value.filter(l => !l.isVisible);
        } else {
            candidates = allLetters.value.filter(l => l.isVisible);
        }

        if (candidates.length > 0) {
            let glitchAmount = currentGlitchCount.value;
            if (animationState.value === 'PAUSE') {
                glitchAmount = 1;
            }
            const glitchIndices = pickRandomIndices(candidates.length, Math.floor(glitchAmount));
            glitchIndices.forEach(i => {
                const letter = candidates[i];
                const { styles, durationMs } = generateGlitchStyles();
                letter.isGlitching = true;
                letter.glitchStyles = styles;
                setTimeout(() => { letter.isGlitching = false; }, durationMs);
            });
        }
       
        // 3. Perform the state-specific action (reveal, hide, or transition).
        if (animationState.value === 'REVEALING' && config.value.enableAdd) {
            const revealCandidates = allLetters.value.filter(l => !l.isVisible && l.isGlitching);
            
            // NEW LOGIC: Reveal a number of letters based on currentGlitchCount
            const revealCount = Math.min(
                revealCandidates.length, 
                Math.max(1, Math.floor(currentGlitchCount.value * 0.5))
            );
            const revealIndices = pickRandomIndices(revealCandidates.length, revealCount);
            revealIndices.forEach(i => revealCandidates[i].isVisible = true);

            currentGlitchCount.value = Math.min(currentGlitchCount.value + 0.1, config.value.numGlitchMax);

            const revealedCount = allLetters.value.filter(l => l.isVisible).length;
            if (revealedCount === allLetters.value.length) {
                animationState.value = 'PAUSE';
                pauseTimeout = setTimeout(() => {
                    animationState.value = config.value.enableRemove ? 'HIDING' : 'IDLE';
                }, config.value.modeChangeDelay);
            }
        } else if (animationState.value === 'HIDING' && config.value.enableRemove) {
            const hideCandidates = allLetters.value.filter(l => l.isVisible && l.isGlitching);

            // NEW LOGIC: Hide a number of letters based on currentGlitchCount
            const hideCount = Math.min(
                hideCandidates.length,
                Math.max(1, Math.floor(currentGlitchCount.value * 0.5))
            );
            const hideIndices = pickRandomIndices(hideCandidates.length, hideCount);
            hideIndices.forEach(i => hideCandidates[i].isVisible = false);

            currentGlitchCount.value = Math.max(currentGlitchCount.value - 0.1, config.value.numGlitchMin);

            const visibleCount = allLetters.value.filter(l => l.isVisible).length;
            if (visibleCount === 0) {
                animationState.value = 'IDLE';
                if (onComplete) setTimeout(onComplete, config.value.resetDelay);
            }
        }
        
        lastFrameTime = now;
        
        let minDelay = config.value.minDelay;
        let maxDelay = config.value.maxDelay;

        if (animationState.value === 'PAUSE') {
            minDelay = 200;
            maxDelay = 500;
        }

        nextDelay = minDelay + Math.random() * (maxDelay - minDelay);
    }

    // --- Public API ---
    async function start(headline: string) {
        loop.stop();
        if (pauseTimeout) clearTimeout(pauseTimeout);
        if (!headline) {
            words.value = [];
            return;
        }

        words.value = headline.split(' ').map(word =>
            word.split('').map(char => ({
                char: char,
                isVisible: false,
                isGlitching: false,
                glitchStyles: {},
            }))
        );

        await nextTick();
        currentGlitchCount.value = config.value.numGlitchMin;
        animationState.value = 'REVEALING';
        lastFrameTime = 0;
        nextDelay = config.value.minDelay;
        loop.start();
    }
    
    return { words, start };
}