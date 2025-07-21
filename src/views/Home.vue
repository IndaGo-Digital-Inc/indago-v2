<template>
  <!-- Main container for Home page content -->
  <div class="container pt-[140px] flex flex-col">
    <!-- Hero section with glitching headline and call-to-action -->
    <div class="flex flex-col items-start justify-between w-full gap-[200px]">
      <div class="flex flex-col gap-[30px] w-full mx-auto">
        <!-- Animated glitching headline text -->
        <GlitchingText
          v-if="glitchText"
          ref="glitchTextRef"
          :text="glitchText"
        />
        <div v-else class="pt-2 text-xs text-gray-400">Loading headlines...</div>
        <div v-if="error" class="pt-2 text-xs text-red-500">{{ error }}</div>
      </div>
      <div class="w-full flex justify-end">
        <!-- GO Digital button, fades in when animation completes or user scrolls -->
        <ArrowButton
          :class="['go-digital-btn', (showGoDigital || userScrolled) ? 'fade-in' : 'fade-out']"
          @click="() => { resetGlitchText(); }"
        >GO Digital</ArrowButton>
        <!-- Down arrow for navigation cue -->
        <button class="w-[36px] hidden md:block">
          <ChevronDown class="fill-id-dark-grey" />
        </button>
      </div>
    </div>
    <!-- SVG scroll animation and description section -->
    <div class="flex flex-col items-start max-w-[320px] mx-auto pt-[150px]">
      <!-- Animated SVG sequence based on scroll position -->
      <SvgScrollAnimation :svgs="svgs" :deadSpace="0.2" />
      <p class="pt-[90px]">
        IndaGo Digital crafts captivating digital experiences that set you apart from your competition and drive measurable results. We blend innovative website development, data-driven SEO strategies, and results-oriented digital marketing to fuel your online success.
      </p>
    </div>
    <!-- Projects showcase section -->
    <div class="project-wrapper flex flex-col pt-[60px] mb-[120px]">
      <!-- Render each project card from fetched data -->
      <ProjectCard
        v-for="(project, idx) in projects"
        :key="idx"
        :title="project.title"
        :subtitle="project.subtitle"
        :image="project.image"
      />
    </div>
  </div>
</template>

<script setup>
import SvgScrollAnimation from '../components/SvgScrollAnimation.vue';
import ArrowButton from '../components/ArrowButton.vue';
import ProjectCard from '../components/ProjectCard.vue';
import ChevronDown from '../assets/id-chevron-down.svg';
import GlitchingText from '../components/GlitchingText.vue';
import { ref, computed } from 'vue';
import { useHeadlines } from '../composables/useHeadlines.js';
import { useProjects } from '../composables/useProjects.js';
import { useHeroUI } from '../composables/useHeroUI.js';
import { useSVGs } from '../composables/useSVGs.js';

// Glitch headline logic
const glitchTextRef = ref(null);
const { headlines, error } = useHeadlines();
const glitchText = computed(() => {
  if (Array.isArray(headlines.value) && headlines.value.length > 0) {
    return headlines.value[0] || '';
  }
  return '';
});
function resetGlitchText() {
  if (glitchTextRef.value && glitchTextRef.value.resetAnimation) {
    glitchTextRef.value.resetAnimation();
  }
}

// Use composables for projects, hero UI, and SVGs
const { projects } = useProjects();
const { userScrolled, showGoDigital } = useHeroUI(resetGlitchText);
const { svgs } = useSVGs();
</script>

<style scoped>
/* Styles for the GO DIGITAL button fade-in/fade-out animation */
.go-digital-btn {
  opacity: 0;
  pointer-events: none;
  transition: opacity 2s;
}
.go-digital-btn.fade-in {
  opacity: 1;
  pointer-events: auto;
}
.go-digital-btn.fade-out {
  opacity: 0;
  pointer-events: none;
}
</style>