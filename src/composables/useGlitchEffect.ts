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
  glitchProbLowercase?: number;
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
    glitchProbLowercase = 0.05,
  } = options;

  function getGlitchClass(active = true) {
    if (!active) return '';
    const r = Math.random();
    let acc = glitchProbNone;
    if (r < acc) return '';
    acc += glitchProbX;
    if (r < acc) return 'glitch-x';
    acc += glitchProbXNeg;
    if (r < acc) return 'glitch-x-neg';
    acc += glitchProbY;
    if (r < acc) return 'glitch-y';
    acc += glitchProbYNeg;
    if (r < acc) return 'glitch-y-neg';
    acc += glitchProbZ;
    if (r < acc) return 'glitch-z';
    acc += glitchProbZNeg;
    if (r < acc) return 'glitch-z-neg';
    acc += glitchProbBoth;
    if (r < acc) return 'glitch-xy';
    acc += glitchProbBothNeg;
    if (r < acc) return 'glitch-xy-neg';
    acc += glitchProbXYZ;
    if (r < acc) return 'glitch-xyz';
    acc += glitchProbXYZNeg;
    if (r < acc) return 'glitch-xyz-neg';
    acc += glitchProbLowercase;
    if (r < acc) return 'glitch-lowercase';
    return '';
  }

  return { getGlitchClass };
}
