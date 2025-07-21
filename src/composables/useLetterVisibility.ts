// useLetterVisibility.ts
// Handles reveal/hide logic and state transitions for animated text
import { Ref } from 'vue';
import { updateLetterVisibility } from './useGlitchUtils';
import { GlitchConfig } from './useGlitchLifecycle';

export function useLetterVisibility(
  config: GlitchConfig,
  targetsRef: Ref<HTMLElement[]>,
  hidingModeRef: Ref<boolean>,
  glitchPhaseRef: Ref<string>
) {
  // Reveal hidden letters if allowed, and handle transition to hiding mode when all are revealed
  function handleRevealLogic(totalLetters: number) {
    if (!hidingModeRef.value && config.enableAdd) {
      const revealCandidates = targetsRef.value.filter(el => !el.classList.contains('visible') && el.classList.contains('glitch'));
      updateLetterVisibility({
        candidates: revealCandidates,
        min: config.revealMin,
        max: config.revealMax,
        action: 'reveal'
      });
      const revealedCountNow = targetsRef.value.reduce((acc, el) => acc + (el.classList.contains('visible') ? 1 : 0), 0);
      if (revealedCountNow === totalLetters) {
        window.dispatchEvent(new CustomEvent('showGoDigital'));
        glitchPhaseRef.value = 'preHideDelay';
        setTimeout(() => {
          hidingModeRef.value = true;
          glitchPhaseRef.value = 'normal';
        }, config.modeChangeDelay);
      }
    }
  }

  // Hide visible letters if allowed, and handle transition to reveal mode when all are hidden
  function handleHideLogic() {
    if (hidingModeRef.value && config.enableRemove) {
      const hideCandidates = targetsRef.value.filter(el => el.classList.contains('visible') && el.classList.contains('glitch'));
      updateLetterVisibility({
        candidates: hideCandidates,
        min: config.revealMin,
        max: config.revealMax,
        action: 'hide'
      });
      const stillVisible = targetsRef.value.reduce((acc, el) => acc + (el.classList.contains('visible') ? 1 : 0), 0);
      if (stillVisible === 0) {
        glitchPhaseRef.value = 'preRevealDelay';
        setTimeout(() => {
          hidingModeRef.value = false;
          glitchPhaseRef.value = 'normal';
        }, config.modeChangeDelay);
      }
    }
  }

  return {
    handleRevealLogic,
    handleHideLogic
  };
}
