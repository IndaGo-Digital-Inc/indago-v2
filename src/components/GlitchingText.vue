<template>
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
import { ref, watch, nextTick } from 'vue';
// Import our new, cleaner pieces
import { config } from '../composables/glitch.config.ts';
import { useAnimationLoop } from '../composables/useAnimationLoop.ts';
// Import existing composables
import { useGlitchLifecycle } from '../composables/useGlitchLifecycle.ts';
import { useGlitchAnimation } from '../composables/useGlitchAnimation.ts';
import { useLetterVisibility } from '../composables/useLetterVisibility.ts';

// --- PROPS AND EMITS ---
const props = defineProps({
    headline: { type: String, required: true, default: '' },
});
const emit = defineEmits(['animationComplete', 'hiding']);

const words = ref([]);

// --- COMPOSABLE SETUP ---
const { glitchInterval, targets, setTargets } = useGlitchLifecycle(config);
const animationStates = { REVEALING: 'REVEALING', REVEAL_PAUSE: 'REVEAL_PAUSE', HIDING: 'HIDING' };
const animationState = ref(animationStates.REVEALING);
const { applyGlitchesToCandidates } = useGlitchAnimation(config, animationState);
const { handleRevealLogic, handleHideLogic } = useLetterVisibility(config, targets, animationState, onAnimationCompleteInternal);

// --- ANIMATION TIMING ---
let lastFrameTime = 0;
let nextDelay = config.minDelay;

// The logic from the old `glitchCycle` is now the callback for our loop
const onTick = (now = 0) => {
    if (!targets.value.length) return;
    if (!lastFrameTime) lastFrameTime = now;
    const elapsed = now - lastFrameTime;

    // Paused state logic
    if (animationState.value === animationStates.REVEAL_PAUSE) {
        if (!onTick._pauseStart) onTick._pauseStart = now;
        if (now - onTick._pauseStart >= config.modeChangeDelay) {
            animationState.value = animationStates.HIDING;
            lastFrameTime = 0;
            onTick._pauseStart = undefined;
        }
        return; // Don't run main logic during pause
    }

    // Main animation logic for Revealing/Hiding
    if (elapsed >= nextDelay) {
        const totalLetters = targets.value.filter(el => el.textContent?.trim()).length;
        const { glitchCandidates, min, max } = getGlitchCandidatesAndRange();
        const count = Math.max(min, Math.floor(Math.random() * (max - min + 1)) + min);

        applyGlitchesToCandidates(glitchCandidates, count);

        if (animationState.value === animationStates.REVEALING) {
            handleRevealLogic(totalLetters);
        } else { // HIDING
            if (!onTick._hidingEventFired) {
                emit('hiding');
                onTick._hidingEventFired = true;
            }
            handleHideLogic();
        }

        nextDelay = config.minDelay + Math.random() * (config.maxDelay - config.minDelay);
        lastFrameTime = now;
    }
    if (animationState.value !== animationStates.HIDING && onTick._hidingEventFired) {
        onTick._hidingEventFired = false;
    }
};

// Instantiate the animation loop with our tick logic
const loop = useAnimationLoop(onTick);

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
    } else {
        glitchCandidates = []; min = 0; max = 0;
    }
    return { glitchCandidates, min, max };
}

function resetAnimationState() {
    loop.stop();
    if (glitchInterval.value) clearTimeout(glitchInterval.value);
    targets.value.forEach(el => el.classList.remove('visible'));
    animationState.value = animationStates.REVEALING;
    lastFrameTime = 0;
    nextDelay = config.minDelay;
}

// --- ORCHESTRATION ---
async function startHeadlineAnimation(headlineText) {
    if (!headlineText) {
        words.value = [];
        return;
    }
    words.value = headlineText.split(' ').map(word => word.split(''));
    await nextTick();
    await nextTick();
    const letters = Array.from(document.querySelectorAll('.letter'));
    setTargets(letters);
    resetAnimationState();
    loop.start();
}

watch(() => props.headline, (newHeadline) => {
    if (newHeadline) {
        startHeadlineAnimation(newHeadline);
    }
}, { immediate: true });

function onAnimationCompleteInternal() {
    setTimeout(() => {
        emit('animationComplete');
    }, config.resetDelay);
}
</script>

<style scoped>
/* Styles remain the same */
.word-block {
    display: inline-block;
    white-space: nowrap;
}

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