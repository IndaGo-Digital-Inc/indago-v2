<template>
    <!-- GlitchingText: Animated headline with per-letter glitch/reveal/hide effects -->
    <h1 class="w-full">
        <!-- Render each word as a block for layout control -->
        <span v-for="(word, wIdx) in words" :key="wIdx" class="word-block">
            <!-- Render each character as a span for individual glitching -->
            <span v-for="(char, cIdx) in word" :key="cIdx" class="letter letter-upper">
                {{ char === ' ' ? '\u00A0' : char }}
            </span>
        </span>
    </h1>
</template>

<script setup>
// Import Vue lifecycle and custom composables for glitch animation
import { onMounted, nextTick } from 'vue';
import { useGlitchLifecycle } from '../composables/useGlitchLifecycle';
import { useGlitchAnimation } from '../composables/useGlitchAnimation';
import { useLetterVisibility } from '../composables/useLetterVisibility';

// Props: text to animate
const props = defineProps({ text: String });
// Split text into words and then into characters for per-letter animation
const words = props.text.split(/\s+/).map(word => word.split(''));

// Glitch animation configuration (timings, colors, effects)
const config = {
    minDelay: 300, // Minimum delay between glitch cycles (ms)
    maxDelay: 800, // Maximum delay between glitch cycles (ms)
    minTransition: 0.1, // Minimum transition duration (s)
    maxTransition: 1,   // Maximum transition duration (s)
    numGlitchMin: 1,    // Minimum number of letters to glitch per cycle
    numGlitchMax: 4,    // Maximum number of letters to glitch per cycle
    hideMultiplier: 5,  // Multiplier for glitch count in hiding mode
    minRotate: 0,       // Minimum rotation (deg)
    maxRotate: 360,     // Maximum rotation (deg)
    minSkew: -45,       // Minimum skew (deg)
    maxSkew: 45,        // Maximum skew (deg)
    minScale: 0.5,      // Minimum scale
    maxScale: 1.5,      // Maximum scale
    scaleProb: 0.8,     // Probability to apply scale
    colorPalette: [     // Weighted color palette for glitch effect
        { value: '#B6B6B6', prob: 0.6 },
        { value: '#4B0082', prob: 0.3 },
        { value: '#FFE412', prob: 0.1 }
    ],
    revealMin: 1,       // Minimum letters to reveal per cycle
    revealMax: 4,       // Maximum letters to reveal per cycle
    enableAdd: true,    // Toggle revealing letters
    enableRemove: true, // Toggle hiding letters
    modeChangeDelay: 5000, // Delay before switching between reveal/hide modes (ms)
};

// Setup glitch lifecycle state and methods
const {
    glitchInterval,
    targets,
    hidingMode,
    glitchPhase,
    setTargets,
    resetAnimation: lifecycleResetAnimation
} = useGlitchLifecycle(config);

// Setup glitch animation and visibility logic
const { applyGlitchStyle, applyGlitchesToCandidates } = useGlitchAnimation(config, hidingMode, glitchPhase);
const { handleRevealLogic, handleHideLogic } = useLetterVisibility(config, targets, hidingMode, glitchPhase);

// Determine which letters are candidates for glitching and how many to glitch
function getGlitchCandidatesAndRange() {
    let glitchCandidates, min, max;
    if (glitchPhase.value === 'preHideDelay') {
        glitchCandidates = targets.value.filter(el => el.classList.contains('visible'));
        min = Math.min(config.numGlitchMin, glitchCandidates.length);
        max = min;
    } else if (glitchPhase.value === 'preRevealDelay') {
        glitchCandidates = targets.value.filter(el => !el.classList.contains('visible'));
        min = Math.min(config.numGlitchMax, glitchCandidates.length);
        max = Math.min(config.numGlitchMax, glitchCandidates.length);
    } else if (!hidingMode.value) {
        glitchCandidates = targets.value.filter(el => !el.classList.contains('visible'));
        min = Math.min(config.numGlitchMin, glitchCandidates.length);
        max = Math.min(config.numGlitchMax, glitchCandidates.length);
    } else {
        glitchCandidates = targets.value.filter(el => el.classList.contains('visible'));
        min = Math.min(config.numGlitchMin * config.hideMultiplier, glitchCandidates.length);
        max = Math.min(config.numGlitchMax * config.hideMultiplier, glitchCandidates.length);
    }
    return { glitchCandidates, min, max };
}

// Main animation loop: applies glitches, handles reveal/hide logic, and schedules next cycle
function glitchCycle() {
    if (!targets.value.length) return;
    // Count revealed and total letters for reveal/hide logic
    const revealedCount = targets.value.reduce((acc, el) => acc + (el.classList.contains('visible') ? 1 : 0), 0);
    const totalLetters = targets.value.filter(el => el.textContent && el.textContent.trim() !== '' && el.textContent.trim() !== ' ').length;

    // Pick glitch candidates and apply glitch effect
    const { glitchCandidates, min, max } = getGlitchCandidatesAndRange();
    const count = Math.max(min, Math.floor(Math.random() * (max - min + 1)) + min);
    applyGlitchesToCandidates(glitchCandidates, count);

    // Reveal/hide logic for animated text
    handleRevealLogic(totalLetters);
    handleHideLogic();

    // Schedule next glitch cycle with random delay
    const nextDelay = config.minDelay + Math.random() * (config.maxDelay - config.minDelay);
    glitchInterval.value = setTimeout(glitchCycle, nextDelay);
}

// Start the glitch animation loop, clearing any previous interval
function startGlitching() {
    if (glitchInterval.value) clearTimeout(glitchInterval.value);
    glitchCycle();
}

// Initialize targets and start animation on mount
onMounted(async () => {
    await nextTick();
    // Select all letter elements for glitching
    const letters = Array.from(document.querySelectorAll('.letter'));
    setTargets(letters);
    setTimeout(() => {
        startGlitching();
    }, 100);
});

// Top-level function to reset the glitch animation to initial state
function resetAnimation() {
    lifecycleResetAnimation();
    if (targets.value && targets.value.length) {
        targets.value.forEach(el => el.classList.remove('visible'));
    }
    startGlitching();
}

// Expose resetAnimation for parent components to trigger a reset
defineExpose({ resetAnimation });
</script>

<style scoped>
/* Layout for each word block in the headline */
.word-block {
    display: inline-block;
    white-space: nowrap;
}

/* Base style for each letter, including transition for glitch effect */
.letter {
    opacity: 0;
    display: inline-block;
    text-transform: uppercase;
    transition: color var(--glitch-transition-duration, 0.3s) ease, opacity var(--glitch-transition-duration, 0.3s), transform var(--glitch-transition-duration, 0.3s) cubic-bezier(.68, -0.55, .27, 1.55);
}

/* Letters become visible when revealed */
.visible {
    opacity: 1;
}

/* Glitch effect styles for animated letters */
.letter.glitch,
.glitch {
    opacity: 1;
    transform: var(--glitch-transform);
    color: var(--glitch-color);
    text-shadow: var(--glitch-text-shadow);
    z-index: 10;
}
</style>
