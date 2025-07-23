<template>
    <span class="w-full id-glitching-letter">
        <template v-for="(word, wordIndex) in words" :key="wordIndex">
            <span class="word-block">
                <span v-for="(letter, letterIndex) in word" :key="letterIndex" class="letter" :class="{
                    'visible': letter.isVisible,
                    'glitch': letter.isGlitching
                }" :style="letter.glitchStyles">
                    {{ letter.char }}
                </span>
            </span>
            <!-- This adds a space between each word block -->
            <span v-if="wordIndex < words.length - 1">&nbsp;</span>
        </template>
    </span>
</template>

<script setup>
import { computed, defineExpose, onMounted } from 'vue';
import { useGlitchAnimation } from '../composables/useGlitchAnimation.ts';
import { config as defaultConfig } from '../composables/glitch.config.ts';

const props = defineProps({
    headline: { type: String, required: true },
    options: { type: Object, default: () => ({}) },
    loop: { type: Boolean, default: false },
});

const emit = defineEmits(['animationComplete']);

const config = computed(() => ({ ...defaultConfig, ...props.options }));

// This function is the callback for when one animation cycle finishes.
// It decides whether to loop internally or just notify the parent.
function onAnimationCycleComplete() {
    if (props.loop) {
        start(props.headline);
    }
    emit('animationComplete');
}

const { words, start } = useGlitchAnimation(config, onAnimationCycleComplete);

// A component with the `loop` prop will start itself once it's mounted.
// This handles self-contained looping animations.
onMounted(() => {
    if (props.loop) {
        start(props.headline);
    }
});

// This exposes the `start` function, making it callable from a parent
// component that has a template ref to this instance.
defineExpose({
    start
});
</script>

<style scoped>
.word-block {
    display: inline-block;
    white-space: nowrap;
}

.letter {
    font-size: 48px;
    font-weight: 200;
    letter-spacing: 0.1em;
    line-height: 1.2em;
    opacity: 0;
    display: inline-block;
    text-transform: uppercase;
    transition: transform var(--glitch-transition-duration) ease,
        color var(--glitch-transition-duration) ease;
}

.visible {
    opacity: 1;
}

.letter.glitch {
    opacity: 1;
    transform: var(--glitch-transform);
    color: var(--glitch-color);

}
</style>