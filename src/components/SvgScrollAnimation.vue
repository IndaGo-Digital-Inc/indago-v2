<template>
  <div ref="svgContainer">
    <component
      :is="svgs[activeIndex].component"
      :key="activeIndex"
      ref="svgRef"
      class="w-full block"
      :class="svgs[activeIndex].color"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';

const props = defineProps({
  svgs: {
    type: Array,
    required: true,
  },
  deadSpace: {
    type: Number,
    default: 0.2,
  },
});


// Animation config for easy tuning
const config = {
  minDuration: 0.005,      // minimum transition duration (seconds)
  maxDuration: 0.03,       // maximum transition duration (seconds)
};

const activeIndex = ref(0);
const svgContainer = ref(null);
const svgRef = ref(null);
// ...existing code...
// ...existing code...


function randomAxis() {
  return config.axisOptions[Math.floor(Math.random() * config.axisOptions.length)];
}

onMounted(() => {
  let observer = null;
  let scrollActive = false;

  function getScrollProgress(rect, vh) {
    const start = vh - 100;
    const end = 100 - rect.height;
    return Math.max(0, Math.min((rect.top - start) / (end - start), 1));
  }

  function getNextIndex(progress, svgsLength) {
    const index = Math.floor(progress * svgsLength);
    return Math.min(index, svgsLength - 1);
  }

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

  function startTransition(nextIndex) {
    activeIndex.value = nextIndex;
  }

  // ...existing code...

  function enableScroll() {
    if (!scrollActive) {
      window.addEventListener('scroll', handleScroll);
      window.addEventListener('resize', handleScroll);
      handleScroll();
      scrollActive = true;
    }
  }
  function disableScroll() {
    if (scrollActive) {
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('resize', handleScroll);
      scrollActive = false;
    }
  }

  observer = new window.IntersectionObserver((entries) => {
    if (!svgContainer.value) return;
    // Enable scroll if ANY part of the component is visible
    if (entries[0].intersectionRatio > 0) {
      enableScroll();
    } else {
      disableScroll();
    }
  }, {
    threshold: [0], // triggers as soon as any part is visible
  });
  if (svgContainer.value) {
    observer.observe(svgContainer.value);
  }

  // ...existing code...

  onUnmounted(() => {
    if (observer && svgContainer.value) observer.unobserve(svgContainer.value);
    observer = null;
    disableScroll();
  });
});
</script>

<style scoped>
/* No SVG transition styles; SVGs switch instantly */
</style>