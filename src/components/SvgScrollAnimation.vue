<template>
  <div ref="svgContainer" class="w-full svg-perspective svg-container">
    <Transition name="rotate-x" mode="out-in">
      <component :is="svgs[activeIndex].component" :key="activeIndex" ref="svgRef" class="w-full block"
        :class="svgs[activeIndex].color" />
    </Transition>
  </div>
</template>

<script setup>
// The <script> block does not need any changes.
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { getScrollProgress, getNextIndex } from '../composables/useScrollProgress.ts';
import { useScrollObserver } from '../composables/useScrollObserver.ts';

const props = defineProps({
  svgs: {
    type: Array,
    required: true,
  },
});

const config = {
  minDuration: 0.005,
  maxDuration: 0.03,
};

const activeIndex = ref(0);
const svgContainer = ref(null);
const svgRef = ref(null);

function startTransition(nextIndex) {
  activeIndex.value = nextIndex;
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

const { mountObserver } = useScrollObserver(svgContainer, handleScroll);

onMounted(() => {
  mountObserver();
});
</script>

<style scoped>
/* 3. Added a perspective property to the container */
.svg-perspective {
  perspective: 1000px;
}

/* .svg-container {
  aspect-ratio: 16 / 9;
  overflow: hidden;
} */

/* 4. Updated the transition classes to perform the rotation */
.rotate-x-enter-active,
.rotate-x-leave-active {
  transition: all 0.1s ease-in-out;
}

.rotate-x-enter-from {
  transform: rotateX(-64deg);
  /* opacity: 0; */
}

.rotate-x-leave-to {
  transform: rotateX(64deg);
  /* opacity: 0; */
}
</style>