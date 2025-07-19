// useGlitchEffect.ts

export interface GlitchOptions {
  glitchProbNone?: number;
  glitchProbX?: number;
  glitchProbXNeg?: number;
  glitchProbY?: number;
  glitchProbYNeg?: number;
  glitchProbZ?: number;
  glitchProbZNeg?: number;
  glitchProbBoth?: number;
  glitchProbBothNeg?: number;
  glitchProbXYZ?: number;
  glitchProbXYZNeg?: number;
}

export function useGlitchEffect(options: GlitchOptions = {}) {
  const {
    glitchProbNone = 0.3,
    glitchProbX = 0.1,
    glitchProbXNeg = 0.1,
    glitchProbY = 0.1,
    glitchProbYNeg = 0.1,
    glitchProbZ = 0.1,
    glitchProbZNeg = 0.1,
    glitchProbBoth = 0.05,
    glitchProbBothNeg = 0.05,
    glitchProbXYZ = 0.05,
    glitchProbXYZNeg = 0.05,
  } = options;

  // Array of glitch types and their probabilities
  const glitchTypes = [
    { prob: glitchProbNone, class: '' },
    { prob: glitchProbX, class: 'glitch-x' },
    { prob: glitchProbXNeg, class: 'glitch-x-neg' },
    { prob: glitchProbY, class: 'glitch-y' },
    { prob: glitchProbYNeg, class: 'glitch-y-neg' },
    { prob: glitchProbZ, class: 'glitch-z' },
    { prob: glitchProbZNeg, class: 'glitch-z-neg' },
    { prob: glitchProbBoth, class: 'glitch-xy' },
    { prob: glitchProbBothNeg, class: 'glitch-xy-neg' },
    { prob: glitchProbXYZ, class: 'glitch-xyz' },
    { prob: glitchProbXYZNeg, class: 'glitch-xyz-neg' },
  ];

  // Returns a glitch class based on probabilities
  function getGlitchClass(active = true) {
    if (!active) return '';
    const r = Math.random();
    let acc = 0;
    for (const { prob, class: cls } of glitchTypes) {
      acc += prob;
      if (r < acc) return cls;
    }
    return '';
  }

  return { getGlitchClass };
}
