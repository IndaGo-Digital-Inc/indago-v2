<script setup>
import IdGO from '../assets/go.svg';
import { defineEmits } from 'vue';

const emit = defineEmits(['reset-animation']);
</script>

<template>
    <header class="container flex justify-between items-center bg-id-black pt-[30px]">
        <IdGO class="h-[40px] fill-id-purple" @click="emit('reset-animation')" style="cursor:pointer;" />
    </header>
</template>

<style scoped></style>