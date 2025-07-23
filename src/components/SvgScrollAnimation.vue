<template>
  <div ref="svgContainer" class="w-full svg-perspective svg-container relative">
    <div ref="rotatingPrism" class="w-full max-w-[60vw] m-x-auto h-full relative" style="transform-style: preserve-3d;">
      <div v-for="(svg, index) in svgs" :key="index" class="absolute top-0 left-0 w-[full] h-full svg-face"
        :style="getFaceStyle(index)">
        <component :is="svg.component" class="w-full h-full block" :class="svg.color" />
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
});

// Using the multiplier from our previous conversation.
// Set to 1 / numSvgs.value for a single 360-degree rotation.
const rotationMultiplier = computed(() => (props.svgs.length > 0 ? 3 / props.svgs.length : 1));

const svgContainer = ref(null);
const rotatingPrism = ref(null);
const translateZ = ref(0);

const numSvgs = computed(() => props.svgs.length);
const anglePerFace = computed(() => 360 / numSvgs.value);

function getFaceStyle(index) {
  if (!translateZ.value) return {};
  const rotation = index * anglePerFace.value;
  return {
    transform: `rotateX(${rotation}deg) translateZ(${translateZ.value}px)`,
  };
}

function handleScroll() {
  if (!svgContainer.value || !rotatingPrism.value) return;

  const rect = svgContainer.value.getBoundingClientRect();
  const viewportHeight = window.innerHeight;

  // --- MODIFIED LOGIC ---
  // 1. Define the padding in pixels (10vh = 10% of viewport height).
  const scrollPadding = viewportHeight * 0.10;

  // 2. The total animation distance now includes the padding at the top and bottom.
  const scrollDistance = viewportHeight + rect.height + (2 * scrollPadding);

  // 3. The animation's starting point is now `20vh` below the bottom of the viewport.
  const animationStartPoint = viewportHeight + scrollPadding;

  // 4. Calculate the current scroll position relative to the new start point.
  const currentScroll = animationStartPoint - rect.top;

  // 5. Calculate progress (0 to 1) over the new, larger distance.
  const progress = Math.max(0, Math.min(1, currentScroll / scrollDistance));

  const totalRotation = numSvgs.value * 360 * rotationMultiplier.value;
  const currentRotation = progress * totalRotation;

  rotatingPrism.value.style.transform = `rotateX(-${currentRotation}deg)`;
}

onMounted(() => {
  nextTick(() => {
    if (svgContainer.value && numSvgs.value > 0) {
      const height = svgContainer.value.offsetHeight;
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
/*
  The container needs a defined height for the geometry calculation (translateZ) to work correctly.
  You can adjust this value as needed for your layout.
*/
.svg-container {
  width: 100%;
  aspect-ratio: 16 / 9;
  object-fit: contain;
}

/* This creates the 3D space for the rotation effect. */
.svg-perspective {
  perspective: 1000px;
}

/* Hiding the back of the faces prevents flickering and visual artifacts 
  during the 3D rotation. 
*/
.svg-face {
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
}
</style>