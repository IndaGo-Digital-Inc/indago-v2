<template>
  <section class="container relative bg-id-black">
    <div class="flex flex-col items-start justify-center w-full min-h-screen bg-id-black">
      <div class="flex flex-col gap-[30px] w-full mx-auto pb-[10vh] bg-id-black hero-section">
        <GlitchingText ref="heroGlitch" :headline="currentHeadline" @animation-complete="showNextHeadline"
          :options="glitchOptions" />
      </div>
      <div class="w-full flex justify-end">
        <ArrowButton class="absolute bottom-[50px] go-digital-btn fade-in" :link="'#contact-form'"
          :linkLabel="'GO Digital'" :customClass="'text-id-purple'" :arrowClass="'fill-id-purple h-[16px] rotate-90'">
        </ArrowButton>
      </div>
    </div>
  </section>

  <section class="container flex flex-col bg-id-black">
    <div class="flex flex-col items-center justify-center w-full gap-[230px] pt-[150px] pb-[184px] bg-id-black">
      <SvgScrollAnimation :svgs="standOutSvgs" :direction="'vertical'" />
      <p class="text-id-light-grey max-w-[200px] text-center">
        IndaGo Digital crafts captivating digital experiences that set you apart from your competition and drive
        measurable results. We blend innovative website development, data-driven SEO strategies, and results-oriented
        digital marketing to fuel your online success.
      </p>
    </div>
  </section>

  <section class="container flex flex-col gap-[40px] bg-id-black">
    <GlitchingText :headline="'Select Projects'" :options="glitchOptions" loop />
    <ProjectCard v-for="(project, idx) in projects" :key="idx" :title="project.title" :image="project.image"
      :excerpt="project.excerpt" :taxonomies="project.taxonomies" :link="project.link"
      :mobile_project_image="project.mobile_project_image" :desktop_project_image="project.desktop_project_image" />
  </section>

  <section class="container flex flex-col bg-id-black">
    <Reviews />
  </section>

  <section class="container flex flex-col bg-id-black">
    <ServicesList />
  </section>

  <section class="container flex flex-col bg-id-black">
    <SvgScrollAnimation :svgs="trustedPartnersSvgs" />
    <p class="">
      IndaGo Digital crafts captivating digital experiences that set you apart from your competition and drive
      measurable results. We blend innovative website development, data-driven SEO strategies, and results-oriented
      digital marketing to fuel your online success.
    </p>
  </section>

  <section class="container flex flex-col gap-[90px] bg-id-black">
    <h2>It’s time to transform your online presence. Contact us today for a free consultation.</h2>
    <ContactForm />
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useProjects } from '../composables/useProjects';
import { useHeadlines } from '../composables/useHeadlines.ts';

// Components
import SvgScrollAnimation from '../components/SvgScrollAnimation.vue';
import ArrowButton from '../components/ArrowButton.vue';
import ProjectCard from '../components/ProjectCard.vue';
import GlitchingText from '../components/GlitchingText.vue';
import ServicesList from '../components/ServicesList.vue';
import ContactForm from '../components/ContactForm.vue';
import Reviews from '../components/Reviews.vue';

// Assets
import ItsAnim from '../assets/stop-blending-in/its.svg';
import TimeAnim from '../assets/stop-blending-in/time.svg';
import ToAnim from '../assets/stop-blending-in/to.svg';
import StandAnim from '../assets/stop-blending-in/stand.svg';
import OutAnim from '../assets/stop-blending-in/out.svg';
import TrustedAnim from '../assets/trusted-partners/trusted.svg';
import PartnerAnim from '../assets/trusted-partners/partners.svg';
import TangibleAnim from '../assets/trusted-partners/tangible.svg';
import ResultsAnim from '../assets/trusted-partners/results.svg';

// 2. Create a ref to hold the component instance
const heroGlitch = ref(null);

const standOutSvgs = [
  { component: ItsAnim, color: 'fill-id-yellow' },
  { component: TimeAnim, color: 'fill-id-yellow' },
  { component: ToAnim, color: 'fill-id-yellow' },
  { component: StandAnim, color: 'fill-id-yellow' },
  { component: OutAnim, color: 'fill-id-yellow' },
];
const trustedPartnersSvgs = [
  { component: TrustedAnim, color: 'fill-id-purple' },
  { component: PartnerAnim, color: 'fill-id-purple' },
  { component: TangibleAnim, color: 'fill-id-purple' },
  { component: ResultsAnim, color: 'fill-id-purple' },
];

// --- HEADLINE STATE MANAGEMENT ---
const { headlines, fetchHeadlines } = useHeadlines();
const currentHeadlineIndex = ref(0);
const currentHeadline = ref('');

// 3. Update this function to explicitly command the animation to start
const showNextHeadline = () => {
  if (headlines.value.length === 0) {
    return;
  }
  const headlineObj = headlines.value[currentHeadlineIndex.value];
  currentHeadline.value = headlineObj?.title || '';
  currentHeadlineIndex.value = (currentHeadlineIndex.value + 1) % headlines.value.length;

  // After updating the headline, command the child component to run its animation
  if (heroGlitch.value) {
    heroGlitch.value.start(currentHeadline.value);
  }
};

const glitchOptions = ref({
  minDelay: 20,
  maxDelay: 200,
  minTransition: 0.1,
  maxTransition: 0.2,
  numGlitchMin: 1,
  numGlitchMax: 12,
  modeChangeDelay: 3000, // Added a slight pause
  resetDelay: 500,
});

// --- OTHER COMPOSABLES AND LOGIC ---
const { projects, fetchProjects } = useProjects();

onMounted(async () => {
  // Fetch all necessary data when the component mounts
  await Promise.all([
    fetchHeadlines(),
    fetchProjects()
  ]);
  // Start the first headline animation after data is fetched
  showNextHeadline();
});
</script>
<style scoped></style>