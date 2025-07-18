<script setup>
import { ref, provide } from 'vue';
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';
import { useResizeObserver } from './composables/useResizeObserver';

const headerRef = ref(null);
const { elementHeight: headerHeight } = useResizeObserver(headerRef);

provide('headerHeight', headerHeight);
</script>

<template>
	<Header ref="headerRef" />

	<main class="main-content bg-id-black font-sans">
		<router-view />
	</main>

	<Footer />
</template>

<style scoped></style>