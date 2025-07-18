<template>
    <div ref="svgContainer">
        <component :is="svgs[activeIndex]" class="w-full block" :class="color" />
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    svgs: {
        type: Array,
        required: true,
    },
    color: {
        type: String,
        default: 'fill-id-purple',
    },
});

const activeIndex = ref(0);
const svgContainer = ref(null);

onMounted(() => {
    const handleScroll = () => {
        if (!svgContainer.value) return;

        const rect = svgContainer.value.getBoundingClientRect();
        const vh = window.innerHeight;

        // Start: top of container enters (bottom - 100px) of viewport
        // End: bottom of container reaches (top + 100px) of viewport
        const start = vh - 100;
        const end = 100 - rect.height;

        // Progress: 0 when top enters (bottom - 100px), 1 when bottom reaches (top + 100px)
        const progress = Math.max(0, Math.min((rect.top - start) / (end - start), 1));

        const index = Math.floor(progress * props.svgs.length);
        activeIndex.value = Math.min(index, props.svgs.length - 1);
    };

    window.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', handleScroll);
    handleScroll();

    onUnmounted(() => {
        window.removeEventListener('scroll', handleScroll);
        window.removeEventListener('resize', handleScroll);
    });
});
</script>