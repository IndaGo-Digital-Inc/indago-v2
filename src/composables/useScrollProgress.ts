// useScrollProgress.ts
// Calculates scroll progress and next index for scroll-driven animation

export function getScrollProgress(rect: DOMRect, vh: number): number {
  const start = vh - 100;
  const end = 100 - rect.height;
  return Math.max(0, Math.min((rect.top - start) / (end - start), 1));
}

export function getNextIndex(progress: number, svgsLength: number): number {
  const index = Math.floor(progress * svgsLength);
  return Math.min(index, svgsLength - 1);
}
