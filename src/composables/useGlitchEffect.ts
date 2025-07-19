// useGlitchEffect.ts

export interface GlitchOptions {
  glitchProbNone?: number;
  glitchProbX?: number;
  glitchProbXNeg?: number;
  glitchProbY?: number;
  glitchProbYNeg?: number;
  glitchProbBoth?: number;
  glitchProbBothNeg?: number;
}

export function useGlitchEffect(options: GlitchOptions = {}) {
  const {
    glitchProbNone = 0.3,
    glitchProbX = 0.1167,
    glitchProbXNeg = 0.1167,
    glitchProbY = 0.1167,
    glitchProbYNeg = 0.1167,
    glitchProbBoth = 0.1167,
    glitchProbBothNeg = 0.1167,
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
    acc += glitchProbBoth;
    if (r < acc) return 'glitch-xy';
    acc += glitchProbBothNeg;
    if (r < acc) return 'glitch-xy-neg';
    return '';
  }

  return { getGlitchClass };
}
