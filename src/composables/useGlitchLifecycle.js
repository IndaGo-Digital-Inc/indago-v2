// useGlitchLifecycle.js
// Manages glitch animation lifecycle: targets, intervals, and reset logic
import { ref, onMounted, onUnmounted, nextTick } from 'vue';

export function useGlitchLifecycle(config) {
  const glitchInterval = ref(null);
  const targets = ref([]);
  const hidingMode = ref(false);

  function setTargets(refsOrSelector) {
    if (typeof refsOrSelector === 'string') {
      targets.value = Array.from(document.querySelectorAll(refsOrSelector));
    } else if (Array.isArray(refsOrSelector)) {
      targets.value = refsOrSelector.map(r => r instanceof HTMLElement ? r : r.value);
    }
  }

  function resetAnimation() {
    if (glitchInterval.value) {
      clearTimeout(glitchInterval.value);
      glitchInterval.value = null;
    }
    hidingMode.value = false;
    if (targets.value && targets.value.length) {
      targets.value.forEach(el => el.classList.remove('visible'));
    }
  }

  onUnmounted(() => {
    if (glitchInterval.value) {
      clearTimeout(glitchInterval.value);
      glitchInterval.value = null;
    }
  });

  return {
    glitchInterval,
    targets,
    hidingMode,
    setTargets,
    resetAnimation
  };
}
