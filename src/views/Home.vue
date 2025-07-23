<template>
  <section class="container relative">
    <div class="flex flex-col items-start justify-center w-full min-h-screen">
      <div class="flex flex-col gap-[30px] w-full mx-auto">
        <GlitchingText ref="glitchTextRef" class="w-full pb-[90px]" />
      </div>
      <div class="w-full flex justify-end">
        <ArrowButton class="absolute bottom-[50px]" :link="'#contact-form'" :linkLabel="'GO Digital'"
          :customClass="'text-id-yellow'" :arrowClass="'fill-id-yellow h-[16px] rotate-90'"></ArrowButton>
      </div>
    </div>
  </section>
  <section class="container flex flex-col">
    <div class="flex flex-col items-center justify-center w-full gap-[100px] pt-[60px] pb-[60px]">
      <SvgScrollAnimation :svgs="standOutSvgs" class="max-w-[65vw]" />
      <p class="text-id-medium-grey">
        IndaGo Digital crafts captivating digital experiences that set you apart from your competition and drive
        measurable results. We blend innovative website development, data-driven SEO strategies, and results-oriented
        digital marketing to fuel your online success.
      </p>
    </div>
  </section>
  <section class="container flex flex-col pt-[32px]">
    <ProjectCard v-for="(project, idx) in projects" :key="idx" :title="project.title" :image="project.image"
      :excerpt="project.excerpt" :taxonomies="project.taxonomies" :link="project.link"
      :mobile_project_image="project.mobile_project_image" :desktop_project_image="project.desktop_project_image" />
  </section>
  <section class="container flex flex-col">
    <Reviews />
  </section>
  <section class="container flex flex-col">
    <ServicesList />
  </section>
  <section class="container flex flex-col">
    <SvgScrollAnimation :svgs="trustedPartnersSvgs" />
    <p class="">
      IndaGo Digital crafts captivating digital experiences that set you apart from your competition and drive
      measurable results. We blend innovative website development, data-driven SEO strategies, and results-oriented
      digital marketing to fuel your online success.
    </p>
  </section>
  <section class="container flex flex-col gap-[90px]">
    <h2>It’s time to transform your online presence. Contact us today for a free consultation.</h2>
    <ContactForm />
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted, inject, computed } from 'vue';
import { useProjects } from '../composables/useProjects';

// Components
import SvgScrollAnimation from '../components/SvgScrollAnimation.vue';
import ArrowButton from '../components/ArrowButton.vue';
import ProjectCard from '../components/ProjectCard.vue';
import GlitchingText from '../components/GlitchingText.vue';
import ServicesList from '../components/ServicesList.vue';
import ContactForm from '../components/ContactForm.vue';
import Reviews from '../components/Reviews.vue';

// Assets
import ChevronDown from '../assets/id-chevron-down.svg';

// Stand Out SVGs
import ItsAnim from '../assets/stop-blending-in/its.svg';
import TimeAnim from '../assets/stop-blending-in/time.svg';
import ToAnim from '../assets/stop-blending-in/to.svg';
import StandAnim from '../assets/stop-blending-in/stand.svg';
import OutAnim from '../assets/stop-blending-in/out.svg';

// Trusted Partners SVGs
import TrustedAnim from '../assets/trusted-partners/trusted.svg';
import PartnerAnim from '../assets/trusted-partners/partners.svg';
import TangibleAnim from '../assets/trusted-partners/tangible.svg';
import ResultsAnim from '../assets/trusted-partners/results.svg';

const standOutSvgs = [
  { component: ItsAnim, color: 'fill-id-purple' },
  { component: TimeAnim, color: 'fill-id-purple' },
  { component: ToAnim, color: 'fill-id-purple' },
  { component: StandAnim, color: 'fill-id-purple' },
  { component: OutAnim, color: 'fill-id-purple' },
];

const trustedPartnersSvgs = [
  { component: TrustedAnim, color: 'fill-id-purple' },
  { component: PartnerAnim, color: 'fill-id-purple' },
  { component: TangibleAnim, color: 'fill-id-purple' },
  { component: ResultsAnim, color: 'fill-id-purple' },
];

const glitchTextRef = ref(null);
const showGoDigital = ref(false);
const userScrolled = ref(false);

const injectedHeaderHeight = inject('headerHeight', 100);
const headerHeight = computed(() => injectedHeaderHeight?.value ?? 100);

const { projects, loading: projectsLoading, error: projectsError, fetchProjects } = useProjects();

function resetGlitchText() {
  if (glitchTextRef.value && glitchTextRef.value.resetAnimation) {
    glitchTextRef.value.resetAnimation();
  }
}

onMounted(() => {
  function handleFirstScroll() {
    if (!userScrolled.value) {
      userScrolled.value = true;
      window.removeEventListener('scroll', handleFirstScroll);
    }
  }
  window.addEventListener('scroll', handleFirstScroll, { once: true });

  window.addEventListener('showGoDigital', () => {
    showGoDigital.value = true;
  });

  window.addEventListener('reset-glitch', resetGlitchText);

  fetchProjects();
});

onUnmounted(() => {
  window.removeEventListener('reset-glitch', resetGlitchText);
});
</script>

<style scoped>
.go-digital-btn {
  opacity: 0;
  pointer-events: none;
  transition: opacity 2s;
}

.go-digital-btn.fade-in {
  opacity: 1;
  pointer-events: auto;
}
</style>