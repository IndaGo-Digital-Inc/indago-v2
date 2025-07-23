<template>
  <!-- Container for SVG scroll animation -->
  <div ref="svgContainer" class="w-full">
    <!-- Dynamically render the active SVG component based on scroll position -->
    <component :is="svgs[activeIndex].component" :key="activeIndex" ref="svgRef" class="w-full block"
      :class="svgs[activeIndex].color" />
  </div>
</template>

<script setup>
// Import Vue composition API and custom composables for scroll logic
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { getScrollProgress, getNextIndex } from '../composables/useScrollProgress.ts';
import { useScrollObserver } from '../composables/useScrollObserver.ts';

// Props: array of SVGs to animate
const props = defineProps({
  svgs: {
    type: Array,
    required: true,
  },
});

// Animation config (currently unused, but available for future tuning)
const config = {
  minDuration: 0.005,
  maxDuration: 0.03,
};

// State: active SVG index, container ref, and SVG ref
const activeIndex = ref(0);
const svgContainer = ref(null);
const svgRef = ref(null);

// Update the active SVG index when scroll position changes
function startTransition(nextIndex) {
  activeIndex.value = nextIndex;
}

// Handle scroll event: calculate progress and update SVG index
function handleScroll() {
  if (!svgContainer.value) return;
  const rect = svgContainer.value.getBoundingClientRect();
  const vh = window.innerHeight;
  const progress = getScrollProgress(rect, vh);
  const nextIndex = getNextIndex(progress, props.svgs.length);
  if (nextIndex !== activeIndex.value) {
    startTransition(nextIndex);
  }
}

// Setup intersection observer and scroll event listeners
const { mountObserver } = useScrollObserver(svgContainer, handleScroll);

// Mount observer on component mount
onMounted(() => {
  mountObserver();
});
</script>

<style scoped>
/* SVGs switch instantly; no transition styles applied */
</style>