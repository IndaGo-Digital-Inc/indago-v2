<template>
  <!-- Main container for Home page content -->
  <div class="container flex flex-col">
    <!-- Hero section with glitching headline and call-to-action -->
    <div class="flex flex-col items-start justify-between w-full pt-[100px] pb-[30px]"
      :style="{ minHeight: `calc(100vh - ${headerHeight || 100}px)` }">
      <div class=" flex flex-col gap-[30px] w-full mx-auto">
        <!-- Animated glitching headline text -->
        <GlitchingText ref="glitchTextRef" :text="'Bespoke Digital Marketing that Captivates'" class="w-full" />
      </div>
      <div class="w-full flex justify-end">
        <!-- GO Digital button, fades in when animation completes or user scrolls -->
        <ArrowButton :class="['go-digital-btn', (showGoDigital || userScrolled) ? 'fade-in' : 'fade-out']"
          @click="() => { console.log('GO Digital direct click'); resetGlitchText(); }">GO Digital</ArrowButton>
        <!-- Down arrow for navigation cue -->
        <button class="w-[36px] hidden md:block">
          <ChevronDown class="fill-id-dark-grey" />
        </button>
      </div>
    </div>
  </div>
  <div class="container flex flex-col">
    <!-- SVG scroll animation and description section -->
    <div class="flex flex-col items-start pt-[150px]">
      <!-- Animated SVG sequence based on scroll position -->
      <SvgScrollAnimation :svgs="svgs" />
      <p class="pt-[90px]">
        IndaGo Digital crafts captivating digital experiences that set you apart from your competition and drive
        measurable results. We blend innovative website development, data-driven SEO strategies, and results-oriented
        digital marketing to fuel your online success.
      </p>
    </div>
    <!-- Projects showcase section -->
    <div class="project-wrapper flex flex-col pt-[60px] mb-[120px]">
      <!-- Render each project card from fetched data -->
      <ProjectCard
        v-for="(project, idx) in projects"
        :key="idx"
        :title="project.title"
        :image="project.image"
        :excerpt="project.excerpt"
        :taxonomies="project.taxonomies"
        :link="project.link"
      />
    </div>
  </div>
  <div class="container flex flex-col">
    <Reviews />
  </div>
</template>

<script setup>
import { inject, computed } from 'vue';
// Import core and UI components for the Home page
import SvgScrollAnimation from '../components/SvgScrollAnimation.vue';
import ArrowButton from '../components/ArrowButton.vue';
import ProjectCard from '../components/ProjectCard.vue';
import ChevronDown from '../assets/id-chevron-down.svg';
import GlitchingText from '../components/GlitchingText.vue';
// Import SVG assets for scroll animation
import StopAnim from '../assets/stop-blending-in/stop.svg';
import BlendingAnim from '../assets/stop-blending-in/blending.svg';
import InAnim from '../assets/stop-blending-in/in.svg';
import OutAnim from '../assets/stop-blending-in/out.svg';
import StandAnim from '../assets/stop-blending-in/stand.svg';
import ToAnim from '../assets/stop-blending-in/to.svg';
import TimeAnim from '../assets/stop-blending-in/time.svg';
import ItsAnim from '../assets/stop-blending-in/its.svg';
import { ref, onMounted } from 'vue';
import { useProjects } from '../composables/useProjects';

// Ref for GlitchingText component to control animation
const glitchTextRef = ref(null);

// Resets the glitch animation to initial state
function resetGlitchText() {
  if (glitchTextRef.value && glitchTextRef.value.resetAnimation) {
    glitchTextRef.value.resetAnimation();
  }
}

// Inject headerHeight from App.vue
const injectedHeaderHeight = inject('headerHeight', 100);
const headerHeight = computed(() => injectedHeaderHeight?.value ?? 100);

// Controls visibility of the GO DIGITAL button
const showGoDigital = ref(false);

// SVGs used for scroll-driven animation sequence
const svgs = [
  { component: ItsAnim, color: 'fill-id-purple' },
  { component: TimeAnim, color: 'fill-id-purple' },
  { component: ToAnim, color: 'fill-id-purple' },
  { component: StandAnim, color: 'fill-id-purple' },
  { component: OutAnim, color: 'fill-id-purple' },
];

// Use the useProjects composable
const { projects, loading: projectsLoading, error: projectsError, fetchProjects } = useProjects();
onMounted(fetchProjects);

// Tracks if the user has scrolled the page
const userScrolled = ref(false);

// Setup scroll and custom event listeners on mount
onMounted(() => {
  // Detect first scroll to trigger UI changes
  function handleFirstScroll() {
    if (!userScrolled.value) {
      userScrolled.value = true;
      window.removeEventListener('scroll', handleFirstScroll);
    }
  }
  window.addEventListener('scroll', handleFirstScroll, { once: true });

  // Show GO DIGITAL button when glitch animation completes
  window.addEventListener('showGoDigital', () => {
    showGoDigital.value = true;
  });

  // Listen for global glitch reset event
  window.addEventListener('reset-glitch', resetGlitchText);
});

// Cleanup event listeners on unmount
import { onUnmounted } from 'vue';
import Reviews from '../components/Reviews.vue';
onUnmounted(() => {
  window.removeEventListener('reset-glitch', resetGlitchText);
});


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