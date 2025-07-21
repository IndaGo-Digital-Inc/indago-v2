<script setup>
import IdGO from '../assets/go.svg';

function emitResetGlitch() {
  window.dispatchEvent(new CustomEvent('reset-glitch'));
}
</script>

<template>
    <header class="bg-id-black pt-[30px]">
        <div class="container flex justify-between items-center">
            <IdGO class="h-[40px] fill-id-purple" @click="emitResetGlitch" style="cursor:pointer;" />
        </div>
    </header>
</template>

<style scoped></style>