// useGlitchAnimation.ts
// Handles glitch effect logic: picking colors, applying glitch styles, and animation cycles
import { Ref } from 'vue';
import { pickWeightedColor, pickRandomIndices } from './useGlitchUtils';

export interface GlitchConfig {
  minDelay: number;
  maxDelay: number;
  minTransition: number;
  maxTransition: number;
  numGlitchMin: number;
  numGlitchMax: number;
  hideMultiplier: number;
  minRotate: number;
  maxRotate: number;
  minSkew: number;
  maxSkew: number;
  minScale: number;
  maxScale: number;
  scaleProb: number;
  colorPalette: Array<{ value: string; prob: number }>;
  revealMin: number;
  revealMax: number;
  enableAdd: boolean;
  enableRemove: boolean;
  modeChangeDelay: number;
}

export function useGlitchAnimation(
  config: GlitchConfig,
  hidingModeRef: Ref<boolean>,
  glitchPhaseRef: Ref<string>
) {
  // Apply random glitch styles to a letter element
  function applyGlitchStyle(el: HTMLElement) {
    const color = pickWeightedColor(config, hidingModeRef.value, glitchPhaseRef.value);
    const transitionDuration = (config.minTransition + Math.random() * (config.maxTransition - config.minTransition)).toFixed(2) + 's';
    const rotX = (config.minRotate + Math.random() * (config.maxRotate - config.minRotate)).toFixed(2) + 'deg';
    const rotY = (config.minRotate + Math.random() * (config.maxRotate - config.minRotate)).toFixed(2) + 'deg';
    const rotZ = (config.minRotate + Math.random() * (config.maxRotate - config.minRotate)).toFixed(2) + 'deg';
    const skewX = (config.minSkew + Math.random() * (config.maxSkew - config.minSkew)).toFixed(2) + 'deg';
    const skewY = (config.minSkew + Math.random() * (config.maxSkew - config.minSkew)).toFixed(2) + 'deg';
    let scale = 1;
    if (Math.random() < config.scaleProb) {
      scale = parseFloat((config.minScale + Math.random() * (config.maxScale - config.minScale)).toFixed(2));
    }
    const transform = `rotateX(${rotX}) rotateY(${rotY}) rotateZ(${rotZ}) skewX(${skewX}) skewY(${skewY}) scale(${scale})`;
    el.style.setProperty('--glitch-transform', transform);
    el.style.setProperty('--glitch-color', color);
    el.style.setProperty('--glitch-transition-duration', transitionDuration);
    const shadow1 = `0 0 16px ${color}`;
    const shadow2 = `0 0 8px ${color}`;
    const shadow3 = `0 0 2px ${color}`;
    el.style.setProperty('--glitch-text-shadow', `${shadow1}, ${shadow2}, ${shadow3}`);
    el.classList.add('glitch');
    setTimeout(() => {
      el.classList.remove('glitch');
    }, parseFloat(transitionDuration) * 1000);
  }

  // Apply glitch styles to a random subset of candidate letters
  function applyGlitchesToCandidates(candidates: HTMLElement[], count: number) {
    if (candidates.length > 0 && count > 0) {
      const indices = pickRandomIndices(candidates.length, count);
      for (let i = 0; i < indices.length; i++) {
        applyGlitchStyle(candidates[indices[i]]);
      }
    }
  }

  return {
    applyGlitchStyle,
    applyGlitchesToCandidates
  };
}
