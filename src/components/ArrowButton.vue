<template>
    <button :class="[
        'btn btn-primary flex flex-row items-center justify-center gap-[15px]',
        customClass
    ]" @click="$emit('click')">
        <slot />
        <ArrowRight :class="arrowClass" />
    </button>
</template>

<script setup>
import ArrowRight from '../assets/arrow-right.svg';

const props = defineProps({
    arrowClass: {
        type: String,
        default: 'fill-id-yellow h-[18px] rotate-90',
    },
    customClass: {
        type: String,
        default: '',
    },
});
</script>
