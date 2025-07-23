<template>
	<Header ref="headerRef" @reset-animation="emitter.emit('reset-animation')" class="z-2" />
	<main class="main-content font-sans overflow-hidden z-1 bg-id-black">
		<router-view />
	</main>
	<Footer />
</template>

<script setup>
import { ref, provide, onMounted } from 'vue';
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';
// import { useResizeObserver } from './composables/useResizeObserver';
import mitt from 'mitt';

const headerRef = ref(null);
// const { elementHeight: headerHeight } = useResizeObserver(headerRef);

// provide('headerHeight', headerHeight);

// Listen for reset-animation from Header and provide a global event
const emitter = mitt();
provide('globalEmitter', emitter);
</script>

<style scoped></style>