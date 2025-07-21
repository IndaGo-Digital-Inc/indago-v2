import { ref, onMounted, onUnmounted } from 'vue';

/**
 * useHeroUI composable
 * Handles scroll detection, GO DIGITAL button visibility, and event listeners for hero section.
 */
export function useHeroUI(resetGlitchText, cycleGlitchText) {
  const userScrolled = ref(false);
  const showGoDigital = ref(false);

  // Setup listeners on mount
  onMounted(() => {
    // window.addEventListener('reset-glitch', resetGlitchText);
    window.addEventListener('glitch-reveal-mode', cycleGlitchText);
    window.addEventListener('glitch-hiding-mode', () => {
      showGoDigital.value = true;
    });
  });

  // Cleanup listeners on unmount
  onUnmounted(() => {
    window.removeEventListener('reset-glitch', resetGlitchText);
    window.removeEventListener('glitch-reveal-mode', cycleGlitchText);
    window.removeEventListener('glitch-hiding-mode', () => {
      showGoDigital.value = true;
    });
  });

  return {
    userScrolled,
    showGoDigital,
  };
}
