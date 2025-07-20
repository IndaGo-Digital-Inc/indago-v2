<template>
  <h1 class="w-full">
    <span v-for="(word, wIdx) in words" :key="wIdx" class="word-block">
      <span v-for="(char, cIdx) in word" :key="cIdx" class="letter letter-upper">{{ char === ' ' ? '\u00A0' : char }}</span>
    </span>
  </h1>
</template>

<script setup>
import { onMounted, nextTick } from 'vue';
import { useGlitchEffect } from '../composables/useGlitchEffect.js';

const props = defineProps({
  text: String
});
const words = props.text.split(/\s+/).map(word => word.split(''));

onMounted(async () => {
  await nextTick();
  const letters = Array.from(document.querySelectorAll('.letter'));
  const glitch = useGlitchEffect();
  glitch.setTargets(letters);
  glitch.startGlitching();
});
</script>

<style scoped>
.word-block {
  display: inline-block;
  white-space: nowrap;
}
.letter {
  display: inline-block;
  text-transform: uppercase;
  transition: color var(--glitch-transition-duration, 0.3s) ease, opacity var(--glitch-transition-duration, 0.3s), transform var(--glitch-transition-duration, 0.3s) cubic-bezier(.68, -0.55, .27, 1.55);
}
.letter.glitch, .glitch {
  opacity: 1;
  transform: var(--glitch-transform);
  color: var(--glitch-color);
  text-shadow: var(--glitch-text-shadow), 0 0 20px #4B0082, 0 0 40px #ffe412;
  z-index: 10;
}
</style>
