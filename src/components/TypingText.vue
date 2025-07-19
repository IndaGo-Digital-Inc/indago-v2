<template>
  <h1 class="w-full">
    <span v-for="(word, wIdx) in words" :key="wIdx" class="word-block">
      <span v-for="(char, cIdx) in word.split('')" :key="cIdx"
        class="letter"
        :class="[
          { visible: charIndex(wIdx, cIdx) < displayedText.length },
          glitchClass(wIdx, cIdx, charIndex(wIdx, cIdx) < displayedText.length)
        ]"
        :style="glitchStyle(wIdx, cIdx)"
      >
        <span v-if="char === ' '">&nbsp;</span>
        <span v-else :class="[
          glitchClass(wIdx, cIdx, charIndex(wIdx, cIdx) < displayedText.length) === 'glitch-lowercase' ? 'letter-lower' : ''
        ]">
          {{ char.toUpperCase() }}
        </span>
      </span>
      <br />
    </span>
  </h1>
</template>

<script setup>
import { ref, onMounted, watch, computed, onUnmounted } from 'vue';
import { useGlitchEffect } from '../composables/useGlitchEffect';

const props = defineProps({
  text: {
    type: String,
    required: true,
  },
  speed: {
    type: Number,
    default: 50,
  },
  glitchProbNone: {
    type: Number,
    default: 0.5,
  },
  glitchProbX: {
    type: Number,
    default: 0.05,
  },
  glitchProbXNeg: {
    type: Number,
    default: 0.05,
  },
  glitchProbY: {
    type: Number,
    default: 0.05,
  },
  glitchProbYNeg: {
    type: Number,
    default: 0.05,
  },
  glitchProbZ: {
    type: Number,
    default: 0.05,
  },
  glitchProbZNeg: {
    type: Number,
    default: 0.05,
  },
  glitchProbBoth: {
    type: Number,
    default: 0.05,
  },
  glitchProbBothNeg: {
    type: Number,
    default: 0.05,
  },
  glitchProbXYZ: {
    type: Number,
    default: 0.05,
  },
  glitchProbXYZNeg: {
    type: Number,
    default: 0.05,
  },
});

const displayedText = ref('');
const words = computed(() => props.text.split(' '));
// const displayedWords = computed(() => words.value.map(word => word.split('')));

// Flat array of all letters for continuous glitching
// const glitchLetters = computed(() => props.text.split(''));
const glitchMap = ref([]);
let glitchInterval = null;
// ...existing code...
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


function charIndex(wIdx, cIdx) {
  let idx = 0;
  for (let i = 0; i < wIdx; i++) {
    idx += words.value[i].length + 1; // +1 for space
  }
  return idx + cIdx;
}

function glitchClass(wIdx, cIdx, isVisible) {
  const idx = charIndex(wIdx, cIdx);
  // Always show glitch for visible letters
  return isVisible ? glitchMap.value[idx] : '';
}

function glitchStyle(wIdx, cIdx) {
  // Always use 180deg for all letters
  return { '--glitch-rotate': '180deg' };
}

function startTyping() {
  let i = 0;
  glitchMap.value = Array(props.text.length).fill('').map(() => getGlitchClass(true));
  function type() {
    if (i <= props.text.length) {
      displayedText.value = props.text.slice(0, i);
      // When a new letter appears, apply glitch immediately (no timeout)
      if (i > 0) {
        glitchMap.value[i - 1] = getGlitchClass(true);
      }
      i++;
      setTimeout(type, props.speed);
    }
  }
  type();
}

function startGlitching() {
  if (glitchInterval) clearInterval(glitchInterval);
  const lowercaseTimeouts = Array(props.text.length).fill(null);
  glitchInterval = setInterval(() => {
    // Pick one random index to glitch
    const len = props.text.length;
    const randomIdx = Math.floor(Math.random() * len);
    for (let idx = 0; idx < len; idx++) {
      // If currently showing lowercase glitch, keep it for at least 400ms
      if (glitchMap.value[idx] === 'glitch-lowercase') continue;
      glitchMap.value[idx] = idx === randomIdx ? getGlitchClass(true) : '';
      // If just set to lowercase, set a timeout to clear it after 400ms
      if (glitchMap.value[idx] === 'glitch-lowercase') {
        if (lowercaseTimeouts[idx]) clearTimeout(lowercaseTimeouts[idx]);
        lowercaseTimeouts[idx] = setTimeout(() => {
          // Only clear if still lowercase
          if (glitchMap.value[idx] === 'glitch-lowercase') {
            glitchMap.value[idx] = '';
          }
        }, 400);
      }
    }
  }, 200);
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
  if (glitchInterval) clearInterval(glitchInterval);
});
</script>

<style scoped>
.letter {
  opacity: 0;
  transition: opacity 0.3s, transform 0.2s cubic-bezier(.68, -0.55, .27, 1.55);
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
.letter-lower {
  text-transform: lowercase;
}

.letter.glitch-x {
  transform: rotateY(var(--glitch-rotate));
}

.letter.glitch-x-neg {
  transform: rotateY(calc(-1 * var(--glitch-rotate)));
}

.letter.glitch-y {
  transform: rotateX(var(--glitch-rotate));
}

.letter.glitch-y-neg {
  transform: rotateX(calc(-1 * var(--glitch-rotate)));
}

.letter.glitch-xy {
  transform: rotateY(var(--glitch-rotate)) rotateX(var(--glitch-rotate));
}

.letter.glitch-xy-neg {
  transform: rotateY(calc(-1 * var(--glitch-rotate))) rotateX(calc(-1 * var(--glitch-rotate)));
}

.letter.glitch-z {
  transform: rotateZ(var(--glitch-rotate));
}

.letter.glitch-z-neg {
  transform: rotateZ(calc(-1 * var(--glitch-rotate)));
}

.letter.glitch-xyz {
  transform: rotateX(var(--glitch-rotate)) rotateY(var(--glitch-rotate)) rotateZ(var(--glitch-rotate));
}

.letter.glitch-xyz-neg {
  transform: rotateX(calc(-1 * var(--glitch-rotate))) rotateY(calc(-1 * var(--glitch-rotate))) rotateZ(calc(-1 * var(--glitch-rotate)));
}

.word-block {
  display: block;
}
</style>
