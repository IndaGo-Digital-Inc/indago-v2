<template>
  <h1 class="w-full">
    <GlitchGroup
      :minDelay="minDelay"
      :maxDelay="maxDelay"
      :minScale="minScale"
      :maxScale="maxScale"
      :scaleProb="scaleProb"
      :minRotate="minRotate"
      :maxRotate="maxRotate"
      :minSkew="minSkew"
      :maxSkew="maxSkew"
      :minTransition="minTransition"
      :maxTransition="maxTransition"
      :numGlitchMin="numGlitchMin"
      :numGlitchMax="numGlitchMax"
      :stuckProbMin="stuckProbMin"
      :stuckProbability="stuckProbability"
      :colorPalette="colorPalette"
    >
      <span v-for="char in text" :key="char" class="letter letter-upper">{{ char === ' ' ? '\u00A0' : char }}</span>
    </GlitchGroup>
  </h1>
</template>

<script setup>
import GlitchGroup from './GlitchGroup.vue';
const props = defineProps({
  text: String,
  minDelay: { type: Number, default: 300 },
  maxDelay: { type: Number, default: 800 },
  minScale: { type: Number, default: 1 },
  maxScale: { type: Number, default: 2 },
  scaleProb: { type: Number, default: 0.3 },
  minRotate: { type: Number, default: 0 },
  maxRotate: { type: Number, default: 360 },
  minSkew: { type: Number, default: 0 },
  maxSkew: { type: Number, default: 20 },
  minTransition: { type: Number, default: 0.2 },
  maxTransition: { type: Number, default: 1 },
  numGlitchMin: { type: Number, default: 1 },
  numGlitchMax: { type: Number, default: 1 },
  stuckProbMin: { type: Number, default: 0 },
  stuckProbability: { type: Number, default: 0 },
  colorPalette: { type: Array, default: () => ['#B6B6B6', '#FFE412', '#4B0082'] },
});
</script>

<style scoped>
.letter {
  display: inline-block;
  text-transform: uppercase;
}
</style>
