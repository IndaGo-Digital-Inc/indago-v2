<template>
    <h1 class="w-full">
        <span v-for="(word, wIdx) in displayedWords" :key="wIdx" class="word-block">
            <span v-for="(char, cIdx) in word" :key="cIdx" class="letter"
                :class="{ visible: charIndex(wIdx, cIdx) < displayedText.length }">
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
        default: 50, // ms per character
    },
});

const displayedText = ref('');

const words = computed(() => props.text.split(' '));
const displayedWords = computed(() => words.value.map(word => word.split('')));

function charIndex(wIdx, cIdx) {
    let idx = 0;
    for (let i = 0; i < wIdx; i++) {
        idx += words.value[i].length + 1; // +1 for space
    }
    return idx + cIdx;
}

function startTyping() {
    let i = 0;
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
    transition: opacity 0.2s;
    display: inline-block;
}

.letter.visible {
    opacity: 1;
}

.word-block {
    display: block;
}

h1 {
    font-size: 3rem;
}
</style>
