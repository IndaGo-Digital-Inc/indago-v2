// useLetterVisibility.ts
// Handles reveal/hide logic and state transitions for animated text
import { Ref } from "vue";
import { updateLetterVisibility } from "./useGlitchUtils";
import { GlitchConfig } from "./useGlitchLifecycle";

/**
 * useLetterVisibility composable
 *
 * Handles the per-letter reveal and hide logic for animated glitch text.
 * Manages state transitions between revealing and hiding modes, and uses configurable delays to control animation pacing.
 *
 * @param config - GlitchConfig object containing animation parameters, including modeChangeDelay
 * @param targetsRef - Ref to array of letter DOM elements
 * @param hidingModeRef - Ref to boolean indicating if currently in hiding mode
 * @param glitchPhaseRef - Ref to string representing the current phase (e.g., 'normal', 'preHideDelay')
 */
export function useLetterVisibility(
    config: GlitchConfig,
    targetsRef: Ref<HTMLElement[]>,
    hidingModeRef: Ref<boolean>,
    glitchPhaseRef: Ref<string>,
    onComplete?: () => void
) {
    /**
     * Reveals hidden letters one-by-one (or in batches), if revealing is enabled.
     * When all letters are revealed, triggers a transition to hiding mode after a configurable delay.
     *
     * @param totalLetters - The total number of letters to reveal
     */
    function handleRevealLogic(totalLetters: number) {
        // Only reveal if not currently hiding and revealing is enabled
        if (!hidingModeRef.value && config.enableAdd) {
            // Find all hidden letters that are eligible for glitch/reveal
            const revealCandidates = targetsRef.value.filter(
                (el) =>
                    !el.classList.contains("visible") &&
                    el.classList.contains("glitch")
            );
            // Reveal a random batch of candidates (min/max controlled by config)
            updateLetterVisibility({
                candidates: revealCandidates,
                min: config.revealMin,
                max: config.revealMax,
                action: "reveal",
            });
            // Count how many letters are now visible
            const revealedCountNow = targetsRef.value.reduce(
                (acc, el) => acc + (el.classList.contains("visible") ? 1 : 0),
                0
            );
            // If all letters are revealed, start the transition to hiding mode
            if (revealedCountNow === totalLetters) {
                // Enter a temporary phase before hiding, to allow a pause before letters start hiding
                glitchPhaseRef.value = "preHideDelay";
                // After modeChangeDelay ms, switch to hiding mode and resume normal glitching
                setTimeout(() => {
                    hidingModeRef.value = true; // Enter hiding mode
                    glitchPhaseRef.value = "normal"; // Resume normal glitch animation
                }, config.modeChangeDelay);
            }
        }
    }

    /**
     * Hides visible letters one-by-one (or in batches), if hiding is enabled.
     * When all letters are hidden, triggers a transition back to revealing mode after a configurable delay.
     */
    function handleHideLogic() {
        // Only hide if currently in hiding mode and hiding is enabled
        if (hidingModeRef.value && config.enableRemove) {
            // Find all visible letters that are eligible for glitch/hide
            const hideCandidates = targetsRef.value.filter(
                (el) =>
                    el.classList.contains("visible") &&
                    el.classList.contains("glitch")
            );
            // Hide a random batch of candidates (min/max controlled by config)
            updateLetterVisibility({
                candidates: hideCandidates,
                min: config.revealMin,
                max: config.revealMax,
                action: "hide",
            });
            // Count how many letters are still visible
            const stillVisible = targetsRef.value.reduce(
                (acc, el) => acc + (el.classList.contains("visible") ? 1 : 0),
                0
            );
            // If all letters are hidden, start the transition back to revealing mode
            if (stillVisible === 0) {
                // All letters are now hidden. Call the provided callback to trigger a headline change/reset in the parent/component.
                if (onComplete) onComplete();
                // Optionally, you can still handle the phase logic here if needed for fallback/looping
                // glitchPhaseRef.value = "hidingComplete";
                // setTimeout(() => {
                //     hidingModeRef.value = false;
                //     glitchPhaseRef.value = "normal";
                // }, config.modeChangeDelay);
            }
        }
    }

    // Expose the logic handlers for use in the animation loop/component
    return {
        handleRevealLogic,
        handleHideLogic,
    };
}
