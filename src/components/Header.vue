<script setup>
import IdGO from '../assets/go.svg';

function emitResetGlitch() {
  window.dispatchEvent(new CustomEvent('reset-glitch'));
}
</script>

<template>
    <header class="container flex justify-between items-center bg-id-black pt-[30px]">
        <IdGO class="h-[40px] fill-id-purple" @click="emitResetGlitch" style="cursor:pointer;" />
    </header>
</template>

<style scoped></style>