import { ref, onMounted, onUnmounted } from 'vue';

/**
 * A Vue composable that observes the height of a given element ref.
 * @param {import('vue').Ref<HTMLElement | { $el: HTMLElement }>} elementRef - A ref to the element or component to observe.
 * @returns {{elementHeight: import('vue').Ref<number>}} - An object containing the reactive height of the element.
 */
export function useResizeObserver(elementRef) {
    const elementHeight = ref(0);
    let resizeObserver;

    onMounted(() => {
        // The ref might point to a Vue component, so we access its root element via .$el
        // If it's a standard element, we use it directly.
        const target = elementRef.value && elementRef.value.$el ? elementRef.value.$el : elementRef.value;

        if (target) {
            resizeObserver = new ResizeObserver(entries => {
                for (const entry of entries) {
                    elementHeight.value = entry.target.offsetHeight;
                }
            });
            resizeObserver.observe(target);
        }
    });

    onUnmounted(() => {
        if (resizeObserver) {
            // Clean up the observer when the component is unmounted.
            resizeObserver.disconnect();
        }
    });

    return { elementHeight };
}
