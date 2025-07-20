<template>
<!-- TypingText.vue: Animated glitch text effect with per-letter typing, continuous glitching, and letter removal. -->
  <h1 class="w-full">
    <span v-for="(word, wIdx) in words" :key="wIdx" class="word-block">
      <span v-for="(char, cIdx) in word.split('')" :key="charIndex(wIdx, cIdx)"
        class="letter letter-upper"
        :class="{
          removed: !letterVisible[charIndex(wIdx, cIdx)],
          visible: letterVisible[charIndex(wIdx, cIdx)],
          glitch: charIndex(wIdx, cIdx) < displayedText.length && glitchMap[charIndex(wIdx, cIdx)] === 'glitch'
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
// === CONSTANTS ===
const TYPING_SPEED_DEFAULT = 40; // Default typing speed in milliseconds
const GLITCH_MIN_DELAY = 300; // Minimum delay between glitch cycles in milliseconds
const GLITCH_MAX_DELAY = 800; // Maximum delay between glitch cycles in milliseconds
const GLITCH_MIN_SCALE = 1; // Minimum scale for glitch effect
const GLITCH_MAX_SCALE = 2; // Maximum scale for glitch effect
const GLITCH_SCALE_PROB = 0.3; // Probability of applying scale glitch
const GLITCH_MIN_ROTATE = 0; // Minimum rotation for glitch effect
const GLITCH_MAX_ROTATE = 360;  // Maximum rotation for glitch effect
const GLITCH_MIN_SKEW = 0;  // Minimum skew for glitch effect
const GLITCH_MAX_SKEW = 20; // Maximum skew for glitch effect
const GLITCH_MIN_TRANSITION = 0.2; // Minimum transition duration for glitch effect
const GLITCH_MAX_TRANSITION = 1; // Maximum transition duration for glitch effect
const GLITCH_NUM_GLITCH_MIN = 1; // Minimum number of letters to glitch at once
const GLITCH_NUM_GLITCH_MAX = 1; // Maximum number of letters to glitch at once
const REMOVE_MIN_INTERVAL = 100; // Minimum interval for removing letters in milliseconds
const REMOVE_MAX_INTERVAL = 500; // Maximum interval for removing letters in milliseconds
const REMOVE_PROBABILITY = 0.5; // Probability of removing a letter on each interval
const INITIAL_REMOVE_DELAY = 4000; // Initial delay before starting removing (ms)
const GLITCH_STUCK_PROBABILITY = 0.3; // Probability to keep glitch class stuck

const props = defineProps({
  text: String,
  speed: { type: Number, default: TYPING_SPEED_DEFAULT },
});

const mutableText = ref(props.text);
const displayedText = ref('');
const words = computed(() => mutableText.value.split(' '));
const glitchMap = ref([]);
const letterVisible = ref([]); // Track visibility of each letter
const showGoDigital = ref(false);
const emit = defineEmits(['update:showGoDigital']);

let removedCount = 0;
let glitchInterval = null;
let removeInterval = null;
let currentRemoveMinInterval = REMOVE_MIN_INTERVAL;
let restartTimeout = null;

const nonSpaceLetterCount = computed(() => Array.from(mutableText.value).filter(c => c !== ' ').length);

function charIndex(wIdx, cIdx) {
  let idx = 0;
  for (let i = 0; i < wIdx; i++) {
    idx += words.value[i].length + 1;
  }
  return idx + cIdx;
}

function glitchStyle(wIdx, cIdx) {
  let color = '#B6B6B6';
  if (Math.random() < 0.1) {
    color = '#FFE412';
  } else if (Math.random() < 0.5) {
    color = '#4B0082';
  }
  const transitionDuration = (GLITCH_MIN_TRANSITION + Math.random() * (GLITCH_MAX_TRANSITION - GLITCH_MIN_TRANSITION)).toFixed(2) + 's';
  const rotXVal = Math.floor(Math.random() * (GLITCH_MAX_ROTATE - GLITCH_MIN_ROTATE + 1)) + GLITCH_MIN_ROTATE;
  const rotYVal = Math.floor(Math.random() * (GLITCH_MAX_ROTATE - GLITCH_MIN_ROTATE + 1)) + GLITCH_MIN_ROTATE;
  const rotZVal = Math.floor(Math.random() * (GLITCH_MAX_ROTATE - GLITCH_MIN_ROTATE + 1)) + GLITCH_MIN_ROTATE;
  const rotX = rotXVal + 'deg';
  const rotY = rotYVal + 'deg';
  const rotZ = rotZVal + 'deg';
  // Do NOT mutate glitchSumTotal here; only compute style.
  const skewX = (GLITCH_MIN_SKEW + Math.random() * (GLITCH_MAX_SKEW - GLITCH_MIN_SKEW)).toFixed(2) + 'deg';
  const skewY = (GLITCH_MIN_SKEW + Math.random() * (GLITCH_MAX_SKEW - GLITCH_MIN_SKEW)).toFixed(2) + 'deg';
  const scale = Math.random() < GLITCH_SCALE_PROB ? (GLITCH_MIN_SCALE + Math.random() * (GLITCH_MAX_SCALE - GLITCH_MIN_SCALE)).toFixed(2) : '1';
  const transform = `rotateX(${rotX}) rotateY(${rotY}) rotateZ(${rotZ}) skewX(${skewX}) skewY(${skewY}) scale(${scale})`;
  const textShadow = `0 0 16px ${color}, 0 0 8px ${color}, 0 0 2px ${color}`;
  return {
    '--glitch-transform': transform,
    '--glitch-color': color,
    '--glitch-transition-duration': transitionDuration,
    '--glitch-text-shadow': textShadow,
  };
}

function startTyping() {
  if (removeInterval) clearTimeout(removeInterval);
  let i = 0;
  glitchMap.value = Array(mutableText.value.length).fill('');
  letterVisible.value = Array(mutableText.value.length).fill(false);
  removedCount = 0;
  function type() {
    if (i <= mutableText.value.length) {
      displayedText.value = mutableText.value.slice(0, i);
      if (i > 0) {
        glitchMap.value[i - 1] = '';
        letterVisible.value[i - 1] = true;
      }
      i++;
      setTimeout(type, props.speed);
    }
    if (i > props.text.length) {
      setTimeout(() => {
        startRemovingLetters();
      }, INITIAL_REMOVE_DELAY);
    }
  }
  type();
}

function startRemovingLetters() {
  if (removeInterval) clearTimeout(removeInterval);
  if (restartTimeout) clearTimeout(restartTimeout);
  currentRemoveMinInterval = REMOVE_MIN_INTERVAL;
  function scheduleNext() {
    const len = displayedText.value.length;
    const candidates = [];
    words.value.forEach((word, wIdx) => {
      word.split('').forEach((char, cIdx) => {
        const idx = charIndex(wIdx, cIdx);
        // Only consider non-space characters for removal
        if (idx < len && letterVisible.value[idx] && mutableText.value[idx] !== ' ') {
          candidates.push(idx);
        }
      });
    });
    if (candidates.length === 0) {
      // All letters removed, schedule restart in 1 minute
      restartTimeout = setTimeout(() => {
        mutableText.value = props.text;
        displayedText.value = '';
        glitchMap.value = Array(mutableText.value.length).fill('');
        removedCount = 0;
        currentRemoveMinInterval = REMOVE_MIN_INTERVAL;
        letterVisible.value = Array(mutableText.value.length).fill(false);
        showGoDigital.value = false;
        startTyping();
      }, 60000);
      return;
    }
    const idx = candidates[Math.floor(Math.random() * candidates.length)];
    let removed = false;
    if (Math.random() < REMOVE_PROBABILITY) {
      letterVisible.value[idx] = false;
      removedCount++;
      removed = true;
    }
    // Check if all letters are removed
    if (removedCount === nonSpaceLetterCount.value && !showGoDigital.value) {
      showGoDigital.value = true;
      emit('update:showGoDigital', true);
    }
    if (removed) {
      currentRemoveMinInterval += 0.1;
    }
    const nextInterval = currentRemoveMinInterval + Math.random() * (REMOVE_MAX_INTERVAL - currentRemoveMinInterval);
    removeInterval = setTimeout(scheduleNext, nextInterval);
  }
  scheduleNext();
}

function startGlitching() {
  if (glitchInterval) clearInterval(glitchInterval);
  const minDelay = GLITCH_MIN_DELAY, maxDelay = GLITCH_MAX_DELAY;
  function glitchCycle() {
    // Only non-space letters are candidates for glitch
    const totalLength = mutableText.value.length;
    let availableIndices = Array.from({ length: totalLength }, (_, i) => i)
      .filter(i => glitchMap.value[i] !== 'glitch' && mutableText.value[i] !== ' ');
    if (availableIndices.length === 0) return;
    const numGlitch = Math.floor(Math.random() * (GLITCH_NUM_GLITCH_MAX - GLITCH_NUM_GLITCH_MIN + 1)) + GLITCH_NUM_GLITCH_MIN;
    const indices = [];
    while (indices.length < numGlitch) {
      const idx = availableIndices[Math.floor(Math.random() * availableIndices.length)];
      if (!indices.includes(idx)) indices.push(idx);
    }
    indices.forEach(idx => {
      glitchMap.value[idx] = 'glitch';
      // Remove glitch class after transition duration, but sometimes skip removal
      const el = document.querySelectorAll('.letter')[idx];
      let duration = GLITCH_MIN_TRANSITION;
      if (el) {
        const style = window.getComputedStyle(el);
        const durStr = style.getPropertyValue('--glitch-transition-duration') || style.transitionDuration;
        duration = parseFloat(durStr);
      }
      setTimeout(() => {
        if (Math.random() > GLITCH_STUCK_PROBABILITY) {
          glitchMap.value[idx] = '';
        }
        // else: leave glitch class stuck
      }, duration * 1000);
    });
    const nextDelay = minDelay + Math.random() * (maxDelay - minDelay);
    glitchInterval = setTimeout(glitchCycle, nextDelay);
  }
  glitchCycle();
}



onMounted(() => {
  startTyping();
  startGlitching();
});

watch(() => [props.text, props.speed], () => {
  mutableText.value = props.text;
  displayedText.value = '';
  startTyping();
  // startGlitching();
});

onUnmounted(() => {
  if (glitchInterval) clearTimeout(glitchInterval);
  if (removeInterval) clearTimeout(removeInterval);
  if (restartTimeout) clearTimeout(restartTimeout);
});
</script>

<style scoped>
.letter {
  opacity: 0;
  transition: color var(--glitch-transition-duration, 0.3s) ease, opacity var(--glitch-transition-duration, 0.3s), transform var(--glitch-transition-duration, 0.3s) cubic-bezier(.68, -0.55, .27, 1.55);
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
.letter-upper {
  text-transform: uppercase;
}

/* Use only the --glitch-transform variable for all glitch classes */
.letter.glitch {
  transform: var(--glitch-transform);
  color: var(--glitch-color);
  border: var(--glitch-border);
  text-shadow: var(--glitch-text-shadow);
  border-radius: 100px;
  opacity: 1;
}

.word-block {
  display: block;
}
</style>
