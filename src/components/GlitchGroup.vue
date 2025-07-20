<template>
  <span ref="glitchRoot">
    <slot />
  </span>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { useGlitchEffect } from '../composables/useGlitchEffect.js';

const props = defineProps({
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

const glitchRoot = ref(null);
const glitchEls = ref([]);
const { startGlitching, stopGlitching, setConfig, setTargets } = useGlitchEffect({
  minDelay: props.minDelay,
  maxDelay: props.maxDelay,
  minScale: props.minScale,
  maxScale: props.maxScale,
  scaleProb: props.scaleProb,
  minRotate: props.minRotate,
  maxRotate: props.maxRotate,
  minSkew: props.minSkew,
  maxSkew: props.maxSkew,
  minTransition: props.minTransition,
  maxTransition: props.maxTransition,
  numGlitchMin: props.numGlitchMin,
  numGlitchMax: props.numGlitchMax,
  stuckProbMin: props.stuckProbMin,
  stuckProbability: props.stuckProbability,
  colorPalette: props.colorPalette,
});

function updateTargets() {
  if (glitchRoot.value) {
    // Target all .letter elements inside glitchRoot
    glitchEls.value = Array.from(glitchRoot.value.querySelectorAll('.letter'));
    setTargets(glitchEls.value);
  }
}

onMounted(() => {
  nextTick(() => {
    updateTargets();
    setConfig({
      minDelay: props.minDelay,
      maxDelay: props.maxDelay,
      minScale: props.minScale,
      maxScale: props.maxScale,
      scaleProb: props.scaleProb,
      minRotate: props.minRotate,
      maxRotate: props.maxRotate,
      minSkew: props.minSkew,
      maxSkew: props.maxSkew,
      minTransition: props.minTransition,
      maxTransition: props.maxTransition,
      numGlitchMin: props.numGlitchMin,
      numGlitchMax: props.numGlitchMax,
      stuckProbMin: props.stuckProbMin,
      stuckProbability: props.stuckProbability,
      colorPalette: props.colorPalette,
    });
    startGlitching();
  });

  // ...existing code...
  const observer = new MutationObserver(() => {
    updateTargets();
  });
  if (glitchRoot.value) {
    observer.observe(glitchRoot.value, { childList: true, subtree: true });
  }
  onUnmounted(() => {
    observer.disconnect();
  });
});

onUnmounted(() => {
  stopGlitching();
});

watch(
  () => [
    props.minDelay, props.maxDelay, props.minScale, props.maxScale, props.scaleProb,
    props.minRotate, props.maxRotate, props.minSkew, props.maxSkew, props.minTransition,
    props.maxTransition, props.numGlitchMin, props.numGlitchMax, props.stuckProbMin,
    props.stuckProbability, props.colorPalette
  ],
  () => {
    setConfig({
      minDelay: props.minDelay,
      maxDelay: props.maxDelay,
      minScale: props.minScale,
      maxScale: props.maxScale,
      scaleProb: props.scaleProb,
      minRotate: props.minRotate,
      maxRotate: props.maxRotate,
      minSkew: props.minSkew,
      maxSkew: props.maxSkew,
      minTransition: props.minTransition,
      maxTransition: props.maxTransition,
      numGlitchMin: props.numGlitchMin,
      numGlitchMax: props.numGlitchMax,
      stuckProbMin: props.stuckProbMin,
      stuckProbability: props.stuckProbability,
      colorPalette: props.colorPalette,
    });
  }
);
</script>

<style>
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
