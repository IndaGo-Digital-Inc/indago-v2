// useGlitchUtils.ts
// Utility functions for glitch animation and visibility

import { GlitchConfig } from "./useGlitchLifecycle";

export function pickWeightedColor(
    config: GlitchConfig,
    animationState: string
): string {
    // Example: use different highlight colors for different animation states
    if (animationState === "HIDING") return "#4B0082";
    if (animationState === "REVEAL_PAUSE") return "#FFE412";
    // Default: pick from palette
    const r = Math.random();
    let acc = 0;
    for (const c of config.colorPalette) {
        acc += c.prob;
        if (r < acc) return c.value;
    }
    return config.colorPalette[config.colorPalette.length - 1].value;
}

export function pickRandomIndices(length: number, count: number): number[] {
    const indices: number[] = [];
    while (indices.length < count) {
        const idx = Math.floor(Math.random() * length);
        if (!indices.includes(idx)) indices.push(idx);
    }
    return indices;
}

export interface UpdateLetterVisibilityOptions {
    candidates: HTMLElement[];
    min: number;
    max: number;
    action: "reveal" | "hide";
}

export function updateLetterVisibility({
    candidates,
    min,
    max,
    action,
}: UpdateLetterVisibilityOptions): void {
    if (!candidates.length) return;
    const count = Math.min(
        candidates.length,
        Math.floor(Math.random() * (max - min + 1)) + min
    );
    const indices: number[] = [];
    while (indices.length < count && candidates.length > 0) {
        const idx = Math.floor(Math.random() * candidates.length);
        if (!indices.includes(idx)) indices.push(idx);
    }
    indices.forEach((idx) => {
        if (action === "reveal") {
            candidates[idx].classList.add("visible");
        } else if (action === "hide") {
            candidates[idx].classList.remove("visible");
        }
    });
}
