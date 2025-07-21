// useScrollObserver.js
// Manages IntersectionObserver and scroll event activation for a container
import { onUnmounted } from 'vue';

export function useScrollObserver(containerRef, handleScroll) {
  let observer = null;
  let scrollActive = false;

  function enableScroll() {
    if (!scrollActive) {
      window.addEventListener('scroll', handleScroll);
      window.addEventListener('resize', handleScroll);
      handleScroll();
      scrollActive = true;
    }
  }
  function disableScroll() {
    if (scrollActive) {
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('resize', handleScroll);
      scrollActive = false;
    }
  }

  function mountObserver() {
    observer = new window.IntersectionObserver((entries) => {
      if (!containerRef.value) return;
      if (entries[0].intersectionRatio > 0) {
        enableScroll();
      } else {
        disableScroll();
      }
    }, {
      threshold: [0],
    });
    if (containerRef.value) {
      observer.observe(containerRef.value);
    }
  }

  onUnmounted(() => {
    if (observer && containerRef.value) observer.unobserve(containerRef.value);
    observer = null;
    disableScroll();
  });

  return {
    mountObserver,
    enableScroll,
    disableScroll
  };
}
