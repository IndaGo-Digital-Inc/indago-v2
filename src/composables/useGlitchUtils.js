// useGlitchUtils.js
// Utility functions for glitch animation and visibility

// Pick a color from the palette using weighted probabilities
export function pickWeightedColor(config, hidingMode, glitchPhase) {
  if (hidingMode && glitchPhase === 'normal') return '#4B0082';
  if (glitchPhase === 'preHideDelay') return '#FFE412';
  const r = Math.random();
  let acc = 0;
  for (const c of config.colorPalette) {
    acc += c.prob;
    if (r < acc) return c.value;
  }
  return config.colorPalette[config.colorPalette.length - 1].value;
}

// Pick 'count' unique random indices from a list of given length
export function pickRandomIndices(length, count) {
  const indices = [];
  while (indices.length < count) {
    const idx = Math.floor(Math.random() * length);
    if (!indices.includes(idx)) indices.push(idx);
  }
  return indices;
}

// Reveal or hide a random subset of candidate letters
export function updateLetterVisibility({ candidates, min, max, action }) {
  if (!candidates.length) return;
  const count = Math.min(candidates.length, Math.floor(Math.random() * (max - min + 1)) + min);
  const indices = [];
  while (indices.length < count && candidates.length > 0) {
    const idx = Math.floor(Math.random() * candidates.length);
    if (!indices.includes(idx)) indices.push(idx);
  }
  indices.forEach(idx => {
    if (action === 'reveal') {
      candidates[idx].classList.add('visible');
    } else if (action === 'hide') {
      candidates[idx].classList.remove('visible');
    }
  });
}
