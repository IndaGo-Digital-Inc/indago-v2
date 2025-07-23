<!-- GlitchingText.vue -->

<template>
    <!-- GlitchingText: Animated headline with per-letter glitch/reveal/hide effects -->
    <h1 class="w-full">
        <template v-for="(word, wIdx) in words" :key="wIdx">
            <span class="word-block">
                <span v-for="(char, cIdx) in word" :key="cIdx" class="letter letter-upper">
                    {{ char }}
                </span>
            </span>
            <span v-if="wIdx < words.length - 1" aria-hidden="true">&nbsp;</span>
        </template>
    </h1>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useGlitchLifecycle } from '../composables/useGlitchLifecycle.ts';
import { useGlitchAnimation } from '../composables/useGlitchAnimation.ts';
import { useLetterVisibility } from '../composables/useLetterVisibility.ts';
import { useHeadlines } from '../composables/useHeadlines.ts';

const { headlines, loading, error, fetchHeadlines } = useHeadlines();
const currentHeadlineIndex = ref(0);
const words = ref([]);

async function setWordsFromHeadline(idx) {
    const ts = new Date().toISOString();
    if (headlines.value.length > 0) {
        // console.log(`[${ts}] [setWordsFromHeadline] Switching to headline index:`, idx, 'headline:', headlines.value[idx]);
        words.value = headlines.value[idx].words.map(word => Array.isArray(word) ? [...word] : word);
    } else {
        words.value = [];
    }
    await nextTick();
    await nextTick();
}

onMounted(async () => {
    await fetchHeadlines();
    if (headlines.value.length > 0) {
        await startHeadlineAnimation();
    }
});

const config = {
    minDelay: 50,
    maxDelay: 400,
    minTransition: 0.05,
    maxTransition: 0.5,
    numGlitchMin: 1,
    numGlitchMax: 6,
    hideMultiplier: 1,
    minRotate: -360,
    maxRotate: 360,
    minSkew: -45,
    maxSkew: 45,
    minScale: 0.5,
    maxScale: 1.5,
    scaleProb: 0.8,
    colorPalette: [
        { value: '#B6B6B6', prob: 0.6 },
        { value: '#4B0082', prob: 0.3 },
        { value: '#FFE412', prob: 0.1 }
    ],
    revealMin: 1,
    revealMax: 4,
    enableAdd: true,
    enableRemove: true,
    modeChangeDelay: 3000,
};

const {
    glitchInterval,
    targets,
    setTargets,
} = useGlitchLifecycle(config);

const animationStates = {
    REVEALING: 'REVEALING',
    REVEAL_PAUSE: 'REVEAL_PAUSE',
    HIDING: 'HIDING',
};
const animationState = ref(animationStates.REVEALING);

const { applyGlitchStyle, applyGlitchesToCandidates } = useGlitchAnimation(config, animationState);
const { handleRevealLogic, handleHideLogic } = useLetterVisibility(config, targets, animationState, onHeadlineAnimationCompleteLocal);

let rafId = null;
let lastFrameTime = 0;
let nextDelay = config.minDelay;

function getGlitchCandidatesAndRange() {
    let glitchCandidates, min, max;
    if (animationState.value === animationStates.REVEALING) {
        glitchCandidates = targets.value.filter(el => !el.classList.contains('visible'));
        min = Math.min(config.numGlitchMin, glitchCandidates.length);
        max = Math.min(config.numGlitchMax, glitchCandidates.length);
    } else if (animationState.value === animationStates.HIDING) {
        glitchCandidates = targets.value.filter(el => el.classList.contains('visible'));
        min = Math.min(config.numGlitchMin * config.hideMultiplier, glitchCandidates.length);
        max = Math.min(config.numGlitchMax * config.hideMultiplier, glitchCandidates.length);
    } else if (animationState.value === animationStates.REVEAL_PAUSE) {
        glitchCandidates = targets.value.filter(el => el.classList.contains('visible'));
        min = Math.min(config.numGlitchMin * 0.1, glitchCandidates.length);
        max = Math.min(config.numGlitchMax * 0.1, glitchCandidates.length);
    } else {
        glitchCandidates = [];
        min = 0;
        max = 0;
    }
    return { glitchCandidates, min, max };
}

// Main animation loop using requestAnimationFrame
function glitchCycle(now = 0) {
    if (!targets.value.length) return;
    if (!lastFrameTime) lastFrameTime = now;
    const elapsed = now - lastFrameTime;

    if (animationState.value === animationStates.REVEALING || animationState.value === animationStates.HIDING) {
        if (elapsed >= nextDelay) {
            const totalLetters = targets.value.filter(el => el.textContent && el.textContent.trim() !== '' && el.textContent.trim() !== ' ').length;
            const { glitchCandidates, min, max } = getGlitchCandidatesAndRange();
            const count = Math.max(min, Math.floor(Math.random() * (max - min + 1)) + min);
            applyGlitchesToCandidates(glitchCandidates, count);
            if (animationState.value === animationStates.REVEALING) {
                handleRevealLogic(totalLetters);
            } else {
                // Fire event when entering HIDING state for the first time in this cycle
                if (!glitchCycle._hidingEventFired) {
                    window.dispatchEvent(new CustomEvent('showGoDigital'));
                    glitchCycle._hidingEventFired = true;
                }
                handleHideLogic();
            }
            nextDelay = config.minDelay + Math.random() * (config.maxDelay - config.minDelay);
            lastFrameTime = now;
        }
        // Reset the event flag if not in HIDING state
        if (animationState.value !== animationStates.HIDING && glitchCycle._hidingEventFired) {
            glitchCycle._hidingEventFired = false;
        }
        rafId = requestAnimationFrame(glitchCycle);
    } else if (animationState.value === animationStates.REVEAL_PAUSE) {
        if (!glitchCycle._pauseStart) {
            glitchCycle._pauseStart = now;
        }
        const pauseElapsed = now - glitchCycle._pauseStart;
        if (elapsed >= nextDelay) {
            const { glitchCandidates, min, max } = getGlitchCandidatesAndRange();
            const count = Math.max(min, Math.floor(Math.random() * (max - min + 1)) + min);
            applyGlitchesToCandidates(glitchCandidates, count);
            nextDelay = config.minDelay + Math.random() * (config.maxDelay - config.minDelay);
            lastFrameTime = now;
        }
        if (pauseElapsed >= config.modeChangeDelay) {
            animationState.value = animationStates.HIDING;
            lastFrameTime = 0;
            glitchCycle._pauseStart = undefined;
            rafId = requestAnimationFrame(glitchCycle);
        } else {
            rafId = requestAnimationFrame(glitchCycle);
        }
    }
}

function startGlitching() {
    if (rafId) cancelAnimationFrame(rafId);
    lastFrameTime = 0;
    nextDelay = config.minDelay;
    rafId = requestAnimationFrame(glitchCycle);
}

function resetAnimation() {
    // const ts = new Date().toISOString();
    if (rafId) {
        cancelAnimationFrame(rafId);
        rafId = null;
    }
    if (glitchInterval.value) {
        clearTimeout(glitchInterval.value);
        glitchInterval.value = null;
    }
    if (targets.value && targets.value.length) {
        targets.value.forEach(el => el.classList.remove('visible'));
    }
    animationState.value = animationStates.REVEALING;
}

async function startHeadlineAnimation() {
    await setWordsFromHeadline(currentHeadlineIndex.value);
    const letters = Array.from(document.querySelectorAll('.letter'));
    setTargets(letters);
    resetAnimation();
    // setTimeout(() => {
    // const ts = new Date().toISOString();
    startGlitching();
    // console.log(`[${ts}] [startHeadlineAnimation] Animation started.`);
    // }, 100);
}

async function onHeadlineAnimationCompleteLocal() {
    // const ts = new Date().toISOString();
    if (headlines.value.length > 0) {
        currentHeadlineIndex.value = (currentHeadlineIndex.value + 1) % headlines.value.length;
        await startHeadlineAnimation();
    }
}
</script>

<style scoped>
.word-block {
    display: inline-block;
    white-space: nowrap;
}

/* h1 {
    font-size: 36px;
} */

.letter {
    opacity: 0;
    display: inline-block;
    text-transform: uppercase;
    transition: color var(--glitch-transition-duration, 0.3s) ease, opacity var(--glitch-transition-duration, 0.3s), transform var(--glitch-transition-duration, 0.3s) cubic-bezier(.68, -0.55, .27, 1.55);
}

.visible {
    opacity: 1;
}

.letter.glitch,
.glitch {
    opacity: 1;
    transform: var(--glitch-transform);
    color: var(--glitch-color);
    text-shadow: var(--glitch-text-shadow);
    z-index: 10;
}
</style>