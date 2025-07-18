<template>
    <h1 class="w-full">
        <span v-for="(word, wIdx) in displayedWords" :key="wIdx" class="word-block">
            <span v-for="(char, cIdx) in word" :key="cIdx" class="letter" :class="[
                { visible: charIndex(wIdx, cIdx) < displayedText.length },
                glitchClass(wIdx, cIdx, charIndex(wIdx, cIdx) < displayedText.length)
            ]">
                <template v-if="char === ' '">
                    &nbsp;
                </template>
                <template v-else>{{ char }}</template>
            </span>
            <br />
        </span>
    </h1>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';

const props = defineProps({
    text: {
        type: String,
        required: true,
    },
    speed: {
        type: Number,
        default: 30,
    },
});

const displayedText = ref('');

const words = computed(() => props.text.split(' '));
const displayedWords = computed(() => words.value.map(word => word.split('')));

// Store random glitch directions for each character
const glitchMap = ref([]);

function charIndex(wIdx, cIdx) {
    let idx = 0;
    for (let i = 0; i < wIdx; i++) {
        idx += words.value[i].length + 1; // +1 for space
    }
    return idx + cIdx;
}

function randomGlitch() {
    // 0: none, 1: X, 2: Y, 3: both
    const r = Math.random();
    if (r < 0.5) return 0; // 50% no glitch
    if (r < 0.75) return 1; // 25% X
    if (r < 0.95) return 2; // 20% Y
    return 3; // 5% both
}

function glitchClass(wIdx, cIdx, isVisible) {
    const idx = charIndex(wIdx, cIdx);
    const g = glitchMap.value[idx] || 0;
    if (!isVisible) {
        if (g === 1) return 'glitch-x';
        if (g === 2) return 'glitch-y';
        if (g === 3) return 'glitch-xy';
    }
    return '';
}

function startTyping() {
    let i = 0;
    glitchMap.value = Array(props.text.length).fill(0).map(() => randomGlitch());
    function type() {
        if (i <= props.text.length) {
            displayedText.value = props.text.slice(0, i);
            i++;
            setTimeout(type, props.speed);
        }
    }
    type();
}

onMounted(startTyping);

watch(() => props.text, () => {
    displayedText.value = '';
    startTyping();
});
</script>

<style scoped>
.letter {
    opacity: 0;
    transition: opacity 0.2s, transform 0.3s cubic-bezier(.68, -0.55, .27, 1.55);
    display: inline-block;
    transform: none;
}

.letter.visible {
    opacity: 1;
    transform: none;
}

.letter.glitch-x {
    transform: rotateY(90deg);
}

.letter.glitch-y {
    transform: rotateX(90deg);
}

.letter.glitch-xy {
    transform: rotateY(90deg) rotateX(90deg);
}

.word-block {
    display: block;
}

h1 {
    font-size: 3rem;
}
</style>
