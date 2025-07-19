import { Ref, ref, onMounted, onUnmounted, watch } from 'vue';

export function useResizeObserver(targetRef: Ref<HTMLElement | null>) {
  const elementHeight = ref(0);
  let observer: ResizeObserver | null = null;

  function updateHeight() {
    if (targetRef.value) {
      elementHeight.value = targetRef.value.offsetHeight;
    }
  }

  onMounted(() => {
    updateHeight();
    if (targetRef.value instanceof Element) {
      observer = new ResizeObserver(() => {
        updateHeight();
      });
      observer.observe(targetRef.value);
    }
  });

  onUnmounted(() => {
    if (observer && targetRef.value instanceof Element) {
      observer.unobserve(targetRef.value);
      observer.disconnect();
    }
  });

  watch(targetRef, (el) => {
    updateHeight();
    if (observer && el instanceof Element) {
      observer.observe(el);
    }
  });

  return { elementHeight };
}
