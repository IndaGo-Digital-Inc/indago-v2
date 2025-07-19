<template>
  <h1 class="w-full">
    <span v-if="displayedText.length < props.text.length">
      <span v-for="(word, wIdx) in displayedWords" :key="wIdx" class="word-block">
        <span v-for="(char, cIdx) in word" :key="cIdx"
          class="letter"
          :class="[
            { visible: charIndex(wIdx, cIdx) < displayedText.length },
            glitchClass(wIdx, cIdx, charIndex(wIdx, cIdx) < displayedText.length)
          ]"
          :style="glitchStyle(wIdx, cIdx)"
        >
          <template v-if="char === ' '">
            &nbsp;
          </template>
          <template v-else>{{ char }}</template>
        </span>
        <br />
      </span>
    </span>
    <span v-else>
      <span v-for="(word, wIdx) in displayedWords" :key="wIdx" class="word-block">
        <span v-for="(char, cIdx) in word" :key="cIdx"
          class="letter visible"
          :style="glitchStyle(wIdx, cIdx)"
        >
          <template v-if="char === ' '">
            &nbsp;
          </template>
          <template v-else>{{ char }}</template>
        </span>
        <br />
      </span>
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
    default: 0.4,
  },
  glitchProbX: {
    type: Number,
    default: 0.1,
  },
  glitchProbXNeg: {
    type: Number,
    default: 0.1,
  },
  glitchProbY: {
    type: Number,
    default: 0.1,
  },
  glitchProbYNeg: {
    type: Number,
    default: 0.1,
  },
  glitchProbBoth: {
    type: Number,
    default: 0.1,
  },
  glitchProbBothNeg: {
    type: Number,
    default: 0.1,
  },
  glitchRotateCoeff: {
    type: Number,
    default: 2,
  },
});

const displayedText = ref('');
const words = computed(() => props.text.split(' '));
const displayedWords = computed(() => words.value.map(word => word.split('')));

const glitchMap = ref([]);
const glitchRotateCoeffs = ref([]);
const { getGlitchClass } = useGlitchEffect({
  glitchProbNone: props.glitchProbNone,
  glitchProbX: props.glitchProbX,
  glitchProbXNeg: props.glitchProbXNeg,
  glitchProbY: props.glitchProbY,
  glitchProbYNeg: props.glitchProbYNeg,
  glitchProbBoth: props.glitchProbBoth,
  glitchProbBothNeg: props.glitchProbBothNeg,
});

const glitchRootStyle = computed(() => {
  // Default base is 180deg, multiplied by coeff
  const base = 180 * props.glitchRotateCoeff;
  return { '--glitch-rotate': `${base}deg` };
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
  return !isVisible ? glitchMap.value[idx] : '';
}

function glitchStyle(wIdx, cIdx) {
  const idx = charIndex(wIdx, cIdx);
  return { '--glitch-rotate': `${180 * (glitchRotateCoeffs.value[idx] || 1)}deg` };
}

function startTyping() {
  let i = 0;
  glitchMap.value = Array(props.text.length).fill('').map(() => getGlitchClass(true));
  glitchRotateCoeffs.value = Array(props.text.length).fill(0).map(() => (Math.random() * 1.5 + 0.5));
  function type() {
    if (i <= props.text.length) {
      displayedText.value = props.text.slice(0, i);
      i++;
      setTimeout(type, props.speed);
    }
  }
  type();
}

onMounted(startTyping);

watch(() => [props.text, props.speed, props.glitchRotateCoeff], () => {
  displayedText.value = '';
  startTyping();
});

onUnmounted(() => {
  // No cleanup needed
});
</script>

<style scoped>
.letter {
  opacity: 0;
  transition: opacity 0.3s, transform 0.3s cubic-bezier(.68, -0.55, .27, 1.55);
  display: inline-block;
  transform: none;
}

.letter.visible {
  opacity: 1;
  transform: none;
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

.word-block {
  display: block;
}
</style>
