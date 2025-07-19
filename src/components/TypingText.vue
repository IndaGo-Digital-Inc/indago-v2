<template>
  <h1 class="w-full">
    <span v-for="(word, wIdx) in words" :key="wIdx" class="word-block">
      <span v-for="(char, cIdx) in word.split('')" :key="cIdx"
        class="letter letter-upper"
        :class="{
        visible: charIndex(wIdx, cIdx) < displayedText.length,
        removed: removedIndices.has(charIndex(wIdx, cIdx)),
        [glitchClass(wIdx, cIdx, charIndex(wIdx, cIdx) < displayedText.length)]: true
      }"
        :style="glitchStyle(wIdx, cIdx)"
      >
        {{ char === ' ' ? '\u00A0' : char }}
      </span>
      <br />
    </span>
  </h1>
</template>

<script setup>
import { ref, onMounted, watch, computed, onUnmounted } from 'vue';
import { useGlitchEffect } from '../composables/useGlitchEffect';

// Props for text, typing speed, and glitch probabilities
const props = defineProps({
  text: String,
  speed: { type: Number, default: 50 },
  glitchProbNone: { type: Number, default: 0.5 },
  glitchProbX: { type: Number, default: 0.05 },
  glitchProbXNeg: { type: Number, default: 0.05 },
  glitchProbY: { type: Number, default: 0.05 },
  glitchProbYNeg: { type: Number, default: 0.05 },
  glitchProbZ: { type: Number, default: 0.05 },
  glitchProbZNeg: { type: Number, default: 0.05 },
  glitchProbBoth: { type: Number, default: 0.05 },
  glitchProbBothNeg: { type: Number, default: 0.05 },
  glitchProbXYZ: { type: Number, default: 0.05 },
  glitchProbXYZNeg: { type: Number, default: 0.05 },
});

const displayedText = ref('');
const words = computed(() => props.text.split(' '));
const glitchMap = ref([]);
let glitchInterval = null;
const removedIndices = ref(new Set());

// Get glitch class based on probabilities
const { getGlitchClass } = useGlitchEffect({
  glitchProbNone: props.glitchProbNone,
  glitchProbX: props.glitchProbX,
  glitchProbXNeg: props.glitchProbXNeg,
  glitchProbY: props.glitchProbY,
  glitchProbYNeg: props.glitchProbYNeg,
  glitchProbZ: props.glitchProbZ,
  glitchProbZNeg: props.glitchProbZNeg,
  glitchProbBoth: props.glitchProbBoth,
  glitchProbBothNeg: props.glitchProbBothNeg,
  glitchProbXYZ: props.glitchProbXYZ,
  glitchProbXYZNeg: props.glitchProbXYZNeg,
});

// Get the flat index of a character in the text
function charIndex(wIdx, cIdx) {
  let idx = 0;
  for (let i = 0; i < wIdx; i++) {
    idx += words.value[i].length + 1;
  }
  return idx + cIdx;
}

// Get the glitch class for a character if visible
function glitchClass(wIdx, cIdx, isVisible) {
  const idx = charIndex(wIdx, cIdx);
  // If letter is removed, never show glitch class
  if (removedIndices.value.has(idx)) return '';
  return isVisible ? glitchMap.value[idx] : '';
}

// Style for glitch rotation
function glitchStyle() {
  const deg = Math.random() < 0.5 ? '90deg' : '180deg';
  const skewX = (Math.random() * 30).toFixed(2) + 'deg';
  const skewY = (Math.random() * 30).toFixed(2) + 'deg';
  // Instead of fontWeight, use a much stronger textShadow with the same color as the letter
  const colorRand = Math.random();
  let color = '#ffffff'; // fallback color
  if (colorRand < 0.5) color = '#a259ff';
  // Only use visible colors for glitch effect
  const transitionDuration = (0.2 + Math.random() * 0.6).toFixed(2) + 's';
  // Randomize transform axis
  const axes = ['x', 'y', 'z', 'xy', 'xz', 'yz', 'xyz'];
  const axis = axes[Math.floor(Math.random() * axes.length)];
  let transform = '';
  if (axis === 'x') transform = `rotateX(${deg}) skewX(${skewX}) skewY(${skewY})`;
  else if (axis === 'y') transform = `rotateY(${deg}) skewX(${skewX}) skewY(${skewY})`;
  else if (axis === 'z') transform = `rotateZ(${deg}) skewX(${skewX}) skewY(${skewY})`;
  else if (axis === 'xy') transform = `rotateX(${deg}) rotateY(${deg}) skewX(${skewX}) skewY(${skewY})`;
  else if (axis === 'xz') transform = `rotateX(${deg}) rotateZ(${deg}) skewX(${skewX}) skewY(${skewY})`;
  else if (axis === 'yz') transform = `rotateY(${deg}) rotateZ(${deg}) skewX(${skewX}) skewY(${skewY})`;
  else if (axis === 'xyz') transform = `rotateX(${deg}) rotateY(${deg}) rotateZ(${deg}) skewX(${skewX}) skewY(${skewY})`;
  // Very low probability for border
  const borderRand = Math.random();
  let border = 'none';
  if (borderRand < 0.03) border = `1px solid ${color}`;
  // Much stronger text shadow with the same color as the letter
  const textShadow = `0 0 16px ${color}, 0 0 8px ${color}, 0 0 2px ${color}`;
  return {
    '--glitch-transform': transform,
    '--glitch-color': color,
    '--glitch-transition-duration': transitionDuration,
    '--glitch-border': border,
    '--glitch-text-shadow': textShadow,
  };
}

// Typing effect: reveals letters one by one
function startTyping() {
  let i = 0;
  glitchMap.value = Array(props.text.length).fill('').map(() => getGlitchClass(true));
  removedIndices.value.clear();
  function type() {
    if (i <= props.text.length) {
      displayedText.value = props.text.slice(0, i);
      if (i > 0) {
        glitchMap.value[i - 1] = getGlitchClass(true);
      }
      i++;
      setTimeout(type, props.speed);
    }
  }
  type();
}

// Glitch effect: randomly glitches one letter at a time
function startGlitching() {
  if (glitchInterval) clearInterval(glitchInterval);
  function glitchCycle() {
    const len = props.text.length;
    // Only consider non-removed indices
    const availableIndices = Array.from({ length: len }, (_, i) => i).filter(i => !removedIndices.value.has(i));
    if (availableIndices.length === 0) return;
    const numGlitch = Math.max(1, Math.floor(Math.random() * Math.min(4, availableIndices.length)) + 1);
    // Pick unique random indices from available
    const indices = [];
    while (indices.length < numGlitch) {
      const idx = availableIndices[Math.floor(Math.random() * availableIndices.length)];
      if (!indices.includes(idx)) indices.push(idx);
    }
    for (let idx = 0; idx < len; idx++) {
      glitchMap.value[idx] = indices.includes(idx) ? getGlitchClass(true) : '';
    }
    const nextDelay = 50 + Math.random() * 600; // random between 100 and 800 ms
    glitchInterval = setTimeout(glitchCycle, nextDelay);
  }
  glitchCycle();
}

// After typing completes, start removing letters after 20s
let removeTimer = null;
function startRemovingLetters() {
  if (removeTimer) clearTimeout(removeTimer);
  removeTimer = setTimeout(() => {
    function removeCycle() {
      // Only consider non-removed and visible indices
      const len = props.text.length;
      const availableIndices = Array.from({ length: len }, (_, i) => i)
        .filter(i => !removedIndices.value.has(i) && i < displayedText.value.length);
      if (availableIndices.length === 0) {
        // All letters removed, restart typing animation
        displayedText.value = '';
        startTyping();
        startGlitching();
        startRemovingLetters();
        return;
      }
      // Randomly pick one to remove
      const idxToRemove = availableIndices[Math.floor(Math.random() * availableIndices.length)];
      removedIndices.value.add(idxToRemove);
      // Continue removing every 400-1200ms
      setTimeout(removeCycle, 400 + Math.random() * 800);
    }
    removeCycle();
  }, 10000);
}

onMounted(() => {
  startTyping();
  startGlitching();
  startRemovingLetters();
});

watch(() => [props.text, props.speed], () => {
  displayedText.value = '';
  startTyping();
  startGlitching();
  startRemovingLetters();
});

onUnmounted(() => {
  if (glitchInterval) clearTimeout(glitchInterval);
  if (removeTimer) clearTimeout(removeTimer);
});
</script>

<style scoped>
.letter {
  opacity: 0;
  transition: color 0.2s ease, opacity 0.5s, transform var(--glitch-transition-duration, 0.3s) cubic-bezier(.68, -0.55, .27, 1.55);
  display: inline-block;
  transform: none;
  will-change: transform;
  transform-style: preserve-3d;
  backface-visibility: hidden;
  perspective: 800px;
}

.letter.visible {
  opacity: 1;
  transform: none;
}
.letter.removed {
  opacity: 0 !important;
  pointer-events: none;
}
.letter-upper {
  text-transform: uppercase;
}

/* Use only the --glitch-transform variable for all glitch classes */
.letter.glitch-x,
.letter.glitch-x-neg,
.letter.glitch-y,
.letter.glitch-y-neg,
.letter.glitch-xy,
.letter.glitch-xy-neg,
.letter.glitch-z,
.letter.glitch-z-neg,
.letter.glitch-xyz,
.letter.glitch-xyz-neg {
  transform: var(--glitch-transform);
  color: var(--glitch-color);
  border: var(--glitch-border);
  text-shadow: var(--glitch-text-shadow);
  border-radius: 100px;
}

.word-block {
  display: block;
}
</style>
