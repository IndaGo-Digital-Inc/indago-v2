<template>
    <div ref="svgContainer">
        <component :is="svgs[activeIndex].component" class="w-full block" :class="svgs[activeIndex].color" />
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    svgs: {
        type: Array,
        required: true,
    },
    deadSpace: {
        type: Number,
        default: 0.2,
    },
});

const activeIndex = ref(0);
const svgContainer = ref(null);

onMounted(() => {
    let observer = null;
    let scrollActive = false;

    function handleScroll() {
        if (!svgContainer.value) return;
        const rect = svgContainer.value.getBoundingClientRect();
        const vh = window.innerHeight;
        const start = vh - 100;
        const end = 100 - rect.height;
        const progress = Math.max(0, Math.min((rect.top - start) / (end - start), 1));
        const index = Math.floor(progress * props.svgs.length);
        activeIndex.value = Math.min(index, props.svgs.length - 1);
    }

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

    observer = new window.IntersectionObserver((entries) => {
        if (!svgContainer.value) return;
        if (entries[0].isIntersecting) {
            enableScroll();
        } else {
            disableScroll();
        }
    }, {
        threshold: 0.01,
    });
    if (svgContainer.value) {
        observer.observe(svgContainer.value);
    }

    onUnmounted(() => {
        if (observer && svgContainer.value) observer.unobserve(svgContainer.value);
        observer = null;
        disableScroll();
    });
});
</script>