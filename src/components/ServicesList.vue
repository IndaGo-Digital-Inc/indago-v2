<template>
    <div v-if="loading">Loading services...</div>
    <div v-else-if="error">{{ error.message }}</div>
    <h3 v-else-if="services.length" v-for="service in services" :key="service.id" v-html="service.title.rendered"
        class="color-id-light-grey pt-[20px] pb-[20px]"></h3>
    <div v-else>No services found.</div>
</template>

<script setup lang="ts">
import { useServices } from '../composables/useServices';

const { services, loading, error } = useServices();
</script>

<style scoped></style>
