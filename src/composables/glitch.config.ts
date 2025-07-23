export const config = {
    minDelay: 100,
    maxDelay: 400,
    minTransition: 0.1,
    maxTransition: 0.4,
    numGlitchMin: 1,
    numGlitchMax: 6,
    hideMultiplier: 4,
    minRotate: -180,
    maxRotate: 180,
    minSkew: -45,
    maxSkew: 45,
    minScale: 0.75,
    maxScale: 1.25,
    scaleProb: 1,
    colorPalette: [
        { value: '#B6B6B6', prob: 0.6 },
        { value: '#4B0082', prob: 0.3 },
        { value: '#FFE412', prob: 0.1 }
    ],
    revealMin: 1,
    revealMax: 4,
    enableAdd: true,
    enableRemove: true,
    modeChangeDelay: 1000,
    resetDelay: 1000, // Delay before resetting animation state
};