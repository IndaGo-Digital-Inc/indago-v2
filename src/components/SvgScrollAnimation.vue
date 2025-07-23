<template>
  <div ref="svgContainer" class="w-full svg-perspective svg-container relative">
    <div ref="rotatingPrism" class="w-full m-x-auto h-full relative" style="transform-style: preserve-3d;">
      <div v-for="(svg, index) in svgs" :key="index" class="absolute top-0 left-0 w-[full] h-full svg-face"
        :style="getFaceStyle(index)">
        <component :is="svg.component" class="h-full block" :class="svg.color" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue';

const props = defineProps({
  svgs: {
    type: Array,
    required: true,
  },
  // --- NEW ---
  // Added a prop to control the rotation axis.
  direction: {
    type: String,
    default: 'vertical', // 'vertical' (rotateX) or 'horizontal' (rotateY)
    validator: (value) => ['vertical', 'horizontal'].includes(value),
  }
});

const rotationMultiplier = computed(() => (props.svgs.length > 0 ? 3 / props.svgs.length : 1));

const svgContainer = ref(null);
const rotatingPrism = ref(null);
const translateZ = ref(0);

const numSvgs = computed(() => props.svgs.length);
const anglePerFace = computed(() => 360 / numSvgs.value);

// --- MODIFIED ---
// This function now applies either rotateX or rotateY based on the `direction` prop.
function getFaceStyle(index) {
  if (!translateZ.value) return {};
  const rotation = index * anglePerFace.value;
  const rotateProperty = props.direction === 'vertical' ? 'rotateX' : 'rotateY';
  return {
    transform: `${rotateProperty}(${rotation}deg) translateZ(${translateZ.value}px)`,
  };
}

// --- MODIFIED ---
// The main scroll handler now also uses the `direction` prop to set the correct rotation.
function handleScroll() {
  if (!svgContainer.value || !rotatingPrism.value) return;

  const rect = svgContainer.value.getBoundingClientRect();
  const viewportHeight = window.innerHeight;

  const scrollPadding = viewportHeight * 0.10;
  const scrollDistance = viewportHeight + rect.height + (2 * scrollPadding);
  const animationStartPoint = viewportHeight + scrollPadding;
  const currentScroll = animationStartPoint - rect.top;
  const progress = Math.max(0, Math.min(1, currentScroll / scrollDistance));

  const totalRotation = numSvgs.value * 360 * rotationMultiplier.value;
  const currentRotation = progress * totalRotation;

  const rotateProperty = props.direction === 'vertical' ? 'rotateX' : 'rotateY';
  rotatingPrism.value.style.transform = `${rotateProperty}(-${currentRotation}deg)`;
}

onMounted(() => {
  nextTick(() => {
    if (svgContainer.value && numSvgs.value > 0) {
      const height = svgContainer.value.offsetHeight;
      // This calculation works for both X and Y rotation without changes.
      translateZ.value = (height / 2) / Math.tan(Math.PI / numSvgs.value);
      handleScroll();
    }
  });

  window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
/* No changes are needed here */
.svg-container {
  width: 100%;
  aspect-ratio: 16 / 9;
  padding: 0 40px;
  /* object-fit: contain; */
}

.svg-container svg {
  width: 100%;
  /* max-width: 80%; */
  margin: 0 auto;
  /* padding: 0 20px; */
  ;
}

.svg-perspective {
  perspective: 1000px;
}

.svg-face {
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
}
</style>