<template>
  <h1 class="w-full">
    <span v-for="(word, wIdx) in words" :key="wIdx" class="word-block">
      <span v-for="(char, cIdx) in word.split('')" :key="cIdx"
        class="letter letter-upper"
        :class="{ visible: charIndex(wIdx, cIdx) < displayedText.length, [glitchClass(wIdx, cIdx, charIndex(wIdx, cIdx) < displayedText.length)]: true }"
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
  return isVisible ? glitchMap.value[idx] : '';
}

// Style for glitch rotation
function glitchStyle() {
  // Randomly choose either 90deg or 180deg for each letter
  const deg = Math.random() < 0.5 ? '90deg' : '180deg';
  // Randomize skewX and skewY between 0 and 30deg
  const skewX = (Math.random() * 30).toFixed(2) + 'deg';
  const skewY = (Math.random() * 30).toFixed(2) + 'deg';
  const fontWeight = Math.random() < 0.5 ? 'bold' : 'normal';
  // Randomly choose purple, dark-grey, or inherit
  const colorRand = Math.random();
  let color = 'inherit';
  if (colorRand < 0.33) color = '#a259ff'; // purple
  else if (colorRand < 0.66) color = '#232323'; // dark-grey
  const transitionDuration = (0.2 + Math.random() * 0.6).toFixed(2) + 's'; // 0.2s to 0.8s
  return {
    '--glitch-rotate': deg,
    '--glitch-skew-x': skewX,
    '--glitch-skew-y': skewY,
    '--glitch-font-weight': fontWeight,
    '--glitch-color': color,
    '--glitch-transition-duration': transitionDuration,
  };
}

// Typing effect: reveals letters one by one
function startTyping() {
  let i = 0;
  glitchMap.value = Array(props.text.length).fill('').map(() => getGlitchClass(true));
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
    // Randomly choose how many letters to glitch (between 1 and 4, or up to len)
    const numGlitch = Math.max(1, Math.floor(Math.random() * Math.min(4, len)) + 1);
    // Pick unique random indices
    const indices = [];
    while (indices.length < numGlitch) {
      const idx = Math.floor(Math.random() * len);
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

onMounted(() => {
  startTyping();
  startGlitching();
});

watch(() => [props.text, props.speed], () => {
  displayedText.value = '';
  startTyping();
  startGlitching();
});

onUnmounted(() => {
  if (glitchInterval) clearTimeout(glitchInterval);
});
</script>

<style scoped>
.letter {
  opacity: 0;
  transition: opacity 0.5s, transform var(--glitch-transition-duration, 0.3s) cubic-bezier(.68, -0.55, .27, 1.55);
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

/* Skew effect for glitched letters */
.letter.glitch-x,


.letter.glitch-x {
  transform: rotateY(var(--glitch-rotate)) skewX(var(--glitch-skew-x)) skewY(var(--glitch-skew-y));
  font-weight: var(--glitch-font-weight);
  color: var(--glitch-color);
}

.letter.glitch-x-neg {
  transform: rotateY(calc(-1 * var(--glitch-rotate))) skewX(var(--glitch-skew-x)) skewY(var(--glitch-skew-y));
  font-weight: var(--glitch-font-weight);
  color: var(--glitch-color);
}

.letter.glitch-y {
  transform: rotateX(var(--glitch-rotate)) skewX(var(--glitch-skew-x)) skewY(var(--glitch-skew-y));
  font-weight: var(--glitch-font-weight);
  color: var(--glitch-color);
}

.letter.glitch-y-neg {
  transform: rotateX(calc(-1 * var(--glitch-rotate))) skewX(var(--glitch-skew-x)) skewY(var(--glitch-skew-y));
  font-weight: var(--glitch-font-weight);
  color: var(--glitch-color);
}

.letter.glitch-xy {
  transform: rotateY(var(--glitch-rotate)) rotateX(var(--glitch-rotate)) skewX(var(--glitch-skew-x)) skewY(var(--glitch-skew-y));
  font-weight: var(--glitch-font-weight);
  color: var(--glitch-color);
}

.letter.glitch-xy-neg {
  transform: rotateY(calc(-1 * var(--glitch-rotate))) rotateX(calc(-1 * var(--glitch-rotate))) skewX(var(--glitch-skew-x)) skewY(var(--glitch-skew-y));
  font-weight: var(--glitch-font-weight);
  color: var(--glitch-color);
}

.letter.glitch-z {
  transform: rotateZ(var(--glitch-rotate)) skewX(var(--glitch-skew-x)) skewY(var(--glitch-skew-y));
  font-weight: var(--glitch-font-weight);
  color: var(--glitch-color);
}

.letter.glitch-z-neg {
  transform: rotateZ(calc(-1 * var(--glitch-rotate))) skewX(var(--glitch-skew-x)) skewY(var(--glitch-skew-y));
  font-weight: var(--glitch-font-weight);
  color: var(--glitch-color);
}

.letter.glitch-xyz {
  transform: rotateX(var(--glitch-rotate)) rotateY(var(--glitch-rotate)) rotateZ(var(--glitch-rotate)) skewX(var(--glitch-skew-x)) skewY(var(--glitch-skew-y));
  font-weight: var(--glitch-font-weight);
  color: var(--glitch-color);
}

.letter.glitch-xyz-neg {
  transform: rotateX(calc(-1 * var(--glitch-rotate))) rotateY(calc(-1 * var(--glitch-rotate))) rotateZ(calc(-1 * var(--glitch-rotate))) skewX(var(--glitch-skew-x)) skewY(var(--glitch-skew-y));
  font-weight: var(--glitch-font-weight);
  color: var(--glitch-color);
}

.word-block {
  display: block;
}
</style>
