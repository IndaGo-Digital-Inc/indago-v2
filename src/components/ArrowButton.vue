<template>
    <a :href="link"
        :class="`${customClass} arrow-button uppercase text-[18px] flex flex-row items-center justify-center gap-[15px]`"
        :target="`${customTarget}`" rel="noopener noreferrer">
        {{ linkLabel }}
        <ArrowRight :class="arrowClass" />
    </a>
</template>

<script setup>
import ArrowRight from '../assets/arrow-right.svg';

const props = defineProps({
    link: {
        type: String,
        required: true,
    },
    linkLabel: {
        type: String,
        default: '',
    },
    arrowClass: {
        type: String,
        default: 'fill-id-yellow h-[18px] rotate-90',
    },
    customClass: {
        type: String,
        default: '',
    },
    customTarget: {
        type: String,
        default: '_self',
    },
});
</script>
