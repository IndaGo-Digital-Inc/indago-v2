<template>
  <div class="glitch-demo">
    <span v-for="(char, idx) in letters" :key="idx"
      :class="['letter', glitchClasses[idx]]"
    >
      <template v-if="char === ' '">&nbsp;</template>
      <template v-else>{{ char }}</template>
    </span>
  </div>
</template>

<script setup lang="ts">
import { useGlobalGlitchEffect } from '../composables/useGlobalGlitchEffect';

const text = 'Global glitch effect demo!';
const { letters, glitchClasses } = useGlobalGlitchEffect(text, {
  minInterval: 100,
  maxInterval: 600,
  glitchProbNone: 0.4,
  glitchProbX: 0.2,
  glitchProbY: 0.2,
  glitchProbBoth: 0.2,
});
</script>

<style scoped>
.letter {
  display: inline-block;
  transition: transform 0.3s cubic-bezier(.68, -0.55, .27, 1.55);
  font-size: 2rem;
  margin: 0 2px;
}
.glitch-x {
  transform: rotateY(90deg);
}
.glitch-y {
  transform: rotateX(90deg);
}
.glitch-xy {
  transform: rotateY(90deg) rotateX(90deg);
}
</style>
