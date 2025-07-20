export function useGlitchEffect(options = {}) {
  const config = {
    minDelay: 300,
    maxDelay: 800,
    minScale: 1,
    maxScale: 2,
    scaleProb: 0.3,
    minRotate: 0,
    maxRotate: 360,
    minSkew: 0,
    maxSkew: 20,
    minTransition: 0.2,
    maxTransition: 1,
    numGlitchMin: 1,
    numGlitchMax: 1,
    stuckProbMin: 0,
    stuckProbability: 0,
    colorPalette: ['#B6B6B6', '#FFE412', '#4B0082'],
    targetSelector: null,
    ...options,
  };

  let glitchInterval = null;
  let targets = [];

  function setConfig(newConfig) {
    Object.assign(config, newConfig);
  }

  function setTargets(refsOrSelector) {
    if (typeof refsOrSelector === 'string') {
      config.targetSelector = refsOrSelector;
      targets = Array.from(document.querySelectorAll(refsOrSelector));
    } else if (Array.isArray(refsOrSelector)) {
      targets = refsOrSelector.map(r => r.value || r);
    }
    
  }

  function applyGlitchStyle(el) {
    const color = config.colorPalette[Math.floor(Math.random() * config.colorPalette.length)];
    const transitionDuration = (config.minTransition + Math.random() * (config.maxTransition - config.minTransition)).toFixed(2) + 's';
    const rotX = (Math.floor(Math.random() * (config.maxRotate - config.minRotate + 1)) + config.minRotate) + 'deg';
    const rotY = (Math.floor(Math.random() * (config.maxRotate - config.minRotate + 1)) + config.minRotate) + 'deg';
    const rotZ = (Math.floor(Math.random() * (config.maxRotate - config.minRotate + 1)) + config.minRotate) + 'deg';
    const skewX = (config.minSkew + Math.random() * (config.maxSkew - config.minSkew)).toFixed(2) + 'deg';
    const skewY = (config.minSkew + Math.random() * (config.maxSkew - config.minSkew)).toFixed(2) + 'deg';
    const scale = Math.random() < config.scaleProb ? (config.minScale + Math.random() * (config.maxScale - config.minScale)).toFixed(2) : '1';
    const transform = `rotateX(${rotX}) rotateY(${rotY}) rotateZ(${rotZ}) skewX(${skewX}) skewY(${skewY}) scale(${scale})`;
    const textShadow = `0 0 16px ${color}, 0 0 8px ${color}, 0 0 2px ${color}`;
    el.style.setProperty('--glitch-transform', transform);
    el.style.setProperty('--glitch-color', color);
    el.style.setProperty('--glitch-transition-duration', transitionDuration);
    el.style.setProperty('--glitch-text-shadow', textShadow);
    el.classList.add('glitch');
    
    setTimeout(() => {
      if (Math.random() > config.stuckProbability) {
        el.classList.remove('glitch');
      }
    }, parseFloat(transitionDuration) * 1000);
  }

  function glitchCycle() {
    if (!targets.length && config.targetSelector) {
      targets = Array.from(document.querySelectorAll(config.targetSelector));
    }
    if (!targets.length) {
      
      return;
    }
    const numGlitch = Math.floor(Math.random() * (config.numGlitchMax - config.numGlitchMin + 1)) + config.numGlitchMin;
    const indices = [];
    while (indices.length < numGlitch) {
      const idx = Math.floor(Math.random() * targets.length);
      if (!indices.includes(idx)) indices.push(idx);
    }
    indices.forEach(idx => {
      
      applyGlitchStyle(targets[idx]);
    });
    const nextDelay = config.minDelay + Math.random() * (config.maxDelay - config.minDelay);
    glitchInterval = setTimeout(glitchCycle, nextDelay);
  }

  function startGlitching() {
    if (glitchInterval) clearTimeout(glitchInterval);
    glitchCycle();
  }


  return {
    startGlitching,
    setConfig,
    setTargets,
    config,
  };
}
