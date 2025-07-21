<template>
    <h1 class="w-full">
        <span v-for="(word, wIdx) in words" :key="wIdx" class="word-block">
            <span v-for="(char, cIdx) in word" :key="cIdx" class="letter letter-upper">{{ char === ' ' ? '\u00A0' : char
            }}</span>
        </span>
    </h1>
</template>

<script setup>
// Vue 3 GlitchingText Component
// Animated text with glitch/reveal/hide cycles and tunable config


const props = defineProps({
    text: String
});
// Split text into words and then into characters
const words = props.text.split(/\s+/).map(word => word.split(''));

// Animation and glitch effect configuration
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
let glitchInterval = null; // Stores the current glitch cycle timeout
let targets = [];          // Array of letter elements
let hidingMode = false;    // Whether currently in hiding mode
let glitchPhase = 'normal'; // 'normal', 'preHideDelay', 'preRevealDelay'

// Select a color from the palette using weighted probabilities
function pickWeightedColor() {
    const r = Math.random();
    let acc = 0;
    for (const c of config.colorPalette) {
        acc += c.prob;
        if (r < acc) return c.value;
    }
    return config.colorPalette[config.colorPalette.length - 1].value;
}

// Apply glitch styles to a letter element
function applyGlitchStyle(el) {
    const color = pickWeightedColor();
    const transitionDuration = (config.minTransition + Math.random() * (config.maxTransition - config.minTransition)).toFixed(2) + 's';
    const rotX = (config.minRotate + Math.random() * (config.maxRotate - config.minRotate)).toFixed(2) + 'deg';
    const rotY = (config.minRotate + Math.random() * (config.maxRotate - config.minRotate)).toFixed(2) + 'deg';
    const rotZ = (config.minRotate + Math.random() * (config.maxRotate - config.minRotate)).toFixed(2) + 'deg';
    const skewX = (config.minSkew + Math.random() * (config.maxSkew - config.minSkew)).toFixed(2) + 'deg';
    const skewY = (config.minSkew + Math.random() * (config.maxSkew - config.minSkew)).toFixed(2) + 'deg';
    let scale = 1;
    if (Math.random() < config.scaleProb) {
        scale = parseFloat((config.minScale + Math.random() * (config.maxScale - config.minScale)).toFixed(2));
    }

    const transform = `rotateX(${rotX}) rotateY(${rotY}) rotateZ(${rotZ}) skewX(${skewX}) skewY(${skewY}) scale(${scale})`;
    el.style.setProperty('--glitch-transform', transform);
    el.style.setProperty('--glitch-color', color);
    el.style.setProperty('--glitch-transition-duration', transitionDuration);
    // Use the same color for all text shadows
    const shadow1 = `0 0 16px ${color}`;
    const shadow2 = `0 0 8px ${color}`;
    const shadow3 = `0 0 2px ${color}`;
    el.style.setProperty('--glitch-text-shadow', `${shadow1}, ${shadow2}, ${shadow3}`);
    el.classList.add('glitch');
    setTimeout(() => {
        el.classList.remove('glitch');
    }, parseFloat(transitionDuration) * 1000);
}

// Reveal or hide a random subset of candidate letters
function updateLetterVisibility({ candidates, min, max, action }) {
    if (!candidates.length) return;
    const count = Math.min(candidates.length, Math.floor(Math.random() * (max - min + 1)) + min);
    const indices = [];
    while (indices.length < count && candidates.length > 0) {
        const idx = Math.floor(Math.random() * candidates.length);
        if (!indices.includes(idx)) indices.push(idx);
    }
    indices.forEach(idx => {
        if (action === 'reveal') {
            candidates[idx].classList.add('visible');
        } else if (action === 'hide') {
            candidates[idx].classList.remove('visible');
        }
    });
}

import { onMounted, onUnmounted, nextTick } from 'vue';
function glitchCycle() {
    if (!targets.length) return;
    const revealedCount = targets.reduce((acc, el) => acc + (el.classList.contains('visible') ? 1 : 0), 0);
    const totalLetters = targets.filter(el => el.textContent && el.textContent.trim() !== '' && el.textContent.trim() !== ' ').length;

    // Select glitch candidates and glitch count based on current phase
    let glitchCandidates;
    let min, max;
    if (glitchPhase === 'preHideDelay') {
        glitchCandidates = targets.filter(el => el.classList.contains('visible'));
        min = Math.min(config.numGlitchMin, glitchCandidates.length);
        max = min; // numGlitchMax = numGlitchMin during preHideDelay
    } else if (glitchPhase === 'preRevealDelay') {
        glitchCandidates = targets.filter(el => !el.classList.contains('visible'));
        min = Math.min(config.numGlitchMax, glitchCandidates.length); // numGlitchMin = numGlitchMax during preRevealDelay
        max = Math.min(config.numGlitchMax, glitchCandidates.length);
    } else if (!hidingMode) {
        glitchCandidates = targets.filter(el => !el.classList.contains('visible'));
        min = Math.min(config.numGlitchMin, glitchCandidates.length);
        max = Math.min(config.numGlitchMax, glitchCandidates.length);
    } else {
        glitchCandidates = targets.filter(el => el.classList.contains('visible'));
        min = Math.min(config.numGlitchMin * config.hideMultiplier, glitchCandidates.length);
        max = Math.min(config.numGlitchMax * config.hideMultiplier, glitchCandidates.length);
    }
    const count = Math.max(min, Math.floor(Math.random() * (max - min + 1)) + min);
    if (glitchCandidates.length > 0 && count > 0) {
        const indices = [];
        while (indices.length < count) {
            const idx = Math.floor(Math.random() * glitchCandidates.length);
            if (!indices.includes(idx)) indices.push(idx);
        }
        for (let i = 0; i < indices.length; i++) {
            applyGlitchStyle(glitchCandidates[indices[i]]);
        }
    }

    // Reveal logic: reveal hidden letters if allowed
    if (!hidingMode && config.enableAdd) {
        const revealCandidates = targets.filter(el => !el.classList.contains('visible') && el.classList.contains('glitch'));
        updateLetterVisibility({
            candidates: revealCandidates,
            min: config.revealMin,
            max: config.revealMax,
            action: 'reveal'
        });
        const revealedCountNow = targets.reduce((acc, el) => acc + (el.classList.contains('visible') ? 1 : 0), 0);
        if (revealedCountNow === totalLetters) {
            window.dispatchEvent(new CustomEvent('showGoDigital'));
            glitchPhase = 'preHideDelay'; // Enter pre-hide delay phase
            setTimeout(() => {
                hidingMode = true;
                glitchPhase = 'normal';
            }, config.modeChangeDelay);
        }
    }

    // Hide logic: hide visible letters if allowed
    if (hidingMode && config.enableRemove) {
        const hideCandidates = targets.filter(el => el.classList.contains('visible') && el.classList.contains('glitch'));
        updateLetterVisibility({
            candidates: hideCandidates,
            min: config.revealMin,
            max: config.revealMax,
            action: 'hide'
        });
        const stillVisible = targets.reduce((acc, el) => acc + (el.classList.contains('visible') ? 1 : 0), 0);
        if (stillVisible === 0) {
            glitchPhase = 'preRevealDelay'; // Enter pre-reveal delay phase
            setTimeout(() => {
                hidingMode = false;
                glitchPhase = 'normal';
            }, config.modeChangeDelay);
        }
    }

    // Schedule next cycle
    const nextDelay = config.minDelay + Math.random() * (config.maxDelay - config.minDelay);
    glitchInterval = setTimeout(glitchCycle, nextDelay);
}

// Set targets to all letter elements
function setTargets(refsOrSelector) {
    if (typeof refsOrSelector === 'string') {
        targets = Array.from(document.querySelectorAll(refsOrSelector));
    } else if (Array.isArray(refsOrSelector)) {
        targets = refsOrSelector.map(r => r instanceof HTMLElement ? r : r.value);
    }
}

// Start the glitch animation loop
function startGlitching() {
    if (glitchInterval) clearTimeout(glitchInterval);
    glitchCycle();
}

// Initialize targets and start animation on mount
onMounted(async () => {
    await nextTick();
    const letters = Array.from(document.querySelectorAll('.letter'));
    setTargets(letters);
    setTimeout(() => {
        startGlitching();
    }, 100);
});

// Clean up any running intervals/timeouts on unmount
onUnmounted(() => {
    if (glitchInterval) {
        clearTimeout(glitchInterval);
        glitchInterval = null;
    }
});

// Top-level function for reset
function resetAnimation() {
    if (glitchInterval) {
        clearTimeout(glitchInterval);
        glitchInterval = null;
    }
    hidingMode = false;
    glitchPhase = 'normal';
    if (targets && targets.length) {
        targets.forEach(el => el.classList.remove('visible'));
    }
    startGlitching();
}

defineExpose({ resetAnimation });
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
