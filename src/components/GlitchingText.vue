<template>
    <!-- GlitchingText: Animated headline with per-letter glitch/reveal/hide effects -->
    <h1 class="w-full">
        <!-- Render each word as a block for layout control -->
        <template v-for="(word, wIdx) in words" :key="wIdx">
            <span class="word-block">
                <!-- Render each character as a span for individual glitching -->
                <span v-for="(char, cIdx) in word" :key="cIdx" class="letter letter-upper">
                    {{ char }}
                </span>
            </span>
            <span v-if="wIdx < words.length - 1" aria-hidden="true">&nbsp;</span>
        </template>
    </h1>
</template>

<script setup>
// Import Vue lifecycle and custom composables for glitch animation
import { ref, watch, onMounted, nextTick, onBeforeUnmount } from 'vue';
import { useGlitchLifecycle } from '../composables/useGlitchLifecycle.ts';
import { useGlitchAnimation } from '../composables/useGlitchAnimation.ts';
import { useLetterVisibility } from '../composables/useLetterVisibility.ts';
import { useHeadlines } from '../composables/useHeadlines.ts';

// Headlines composable: fetch and process all headlines from WP
const { headlines, loading, error, fetchHeadlines } = useHeadlines();

// Index of the current headline being animated
const currentHeadlineIndex = ref(0);

// Reactive words array for the current headline
const words = ref([]);

// Helper to update words from the current headline
async function setWordsFromHeadline(idx) {
    const ts = new Date().toISOString();
    if (headlines.value.length > 0) {
        console.log(`[${ts}] [setWordsFromHeadline] Switching to headline index:`, idx, 'headline:', headlines.value[idx]);
        words.value = headlines.value[idx].words.map(word => Array.isArray(word) ? [...word] : word);
    } else {
        words.value = [];
    }
    await nextTick();
    await nextTick();
}

// Fetch headlines and start animation on mount
onMounted(async () => {
    await fetchHeadlines();
    if (headlines.value.length > 0) {
        await startHeadlineAnimation();
    }
});

// Glitch animation configuration (timings, colors, effects)
const config = {
    minDelay: 100, // Minimum delay between glitch cycles (ms)
    maxDelay: 600, // Maximum delay between glitch cycles (ms)
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
} = useGlitchLifecycle(config);

// Setup glitch animation and visibility logic
const { applyGlitchStyle, applyGlitchesToCandidates } = useGlitchAnimation(config, hidingMode, glitchPhase);
const { handleRevealLogic, handleHideLogic } = useLetterVisibility(config, targets, hidingMode, glitchPhase, onHeadlineAnimationCompleteLocal);

// Determine which letters are candidates for glitching and how many to glitch
function getGlitchCandidatesAndRange() {
    let glitchCandidates, min, max;
    if (glitchPhase.value === 'preHideDelay') {
        glitchCandidates = targets.value.filter(el => el.classList.contains('visible'));
        min = Math.min(config.numGlitchMin, glitchCandidates.length);
        max = min;
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
    const ts = new Date().toISOString();
    if (!targets.value.length) {
        console.log(`[${ts}] [glitchCycle] No targets, skipping.`);
        return;
    }
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

// Guard to prevent animation-complete callback from firing too soon after reset
let animationResetGuard = false;
function resetAnimation() {
    const ts = new Date().toISOString();
    console.log(`[${ts}] [resetAnimation] Resetting animation`);
    if (glitchInterval.value) {
        clearTimeout(glitchInterval.value);
        glitchInterval.value = null;
    }
    if (targets.value && targets.value.length) {
        targets.value.forEach(el => el.classList.remove('visible'));
    }
    // Always start in reveal mode after switching headlines
    if (typeof hidingMode !== 'undefined' && typeof glitchPhase !== 'undefined') {
        hidingMode.value = false;
        glitchPhase.value = 'normal';
    }
    // Block the animation-complete callback for 300ms after reset
    animationResetGuard = true;
    setTimeout(() => {
        animationResetGuard = false;
    }, 300);
}


// Animation lifecycle orchestration
async function startHeadlineAnimation() {
    await setWordsFromHeadline(currentHeadlineIndex.value);
    // Set targets after DOM update
    const letters = Array.from(document.querySelectorAll('.letter'));
    setTargets(letters);
    resetAnimation();
    // Start glitching after a short delay to ensure DOM is ready
    setTimeout(() => {
        const ts = new Date().toISOString();
        startGlitching();
        console.log(`[${ts}] [startHeadlineAnimation] Animation started.`);
    }, 100);
}

// Called by glitch composable when animation is complete
async function onHeadlineAnimationCompleteLocal() {
    if (animationResetGuard) {
        const ts = new Date().toISOString();
        console.log(`[${ts}] [onHeadlineAnimationCompleteLocal] Animation complete callback ignored due to reset guard.`);
        return;
    }
    const ts = new Date().toISOString();
    console.log(`[${ts}] [onHeadlineAnimationCompleteLocal] Animation complete, switching headline.`);
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