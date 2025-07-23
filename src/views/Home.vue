<!-- Home.vue -->
<template>
  <section class="container relative">
    <div class="flex flex-col items-start justify-center w-full min-h-screen">
      <div class="flex flex-col gap-[30px] w-full mx-auto pb-[10vh]">
        <GlitchingText :headline="currentHeadline" @animation-complete="showNextHeadline" @hiding="onGlitchHiding" />
      </div>
      <div class="w-full flex justify-end">
        <ArrowButton class="absolute bottom-[50px] go-digital-btn fade-in" :link="'#contact-form'"
          :linkLabel="'GO Digital'" :customClass="'text-id-purple'" :arrowClass="'fill-id-purple h-[16px] rotate-90'">
        </ArrowButton>
      </div>
    </div>
  </section>
  <section class="container flex flex-col">
    <div class="flex flex-col items-center justify-center w-full gap-[180px] pt-[180px] pb-[60px]">
      <SvgScrollAnimation :svgs="standOutSvgs" />
      <p class="text-id-light-grey">
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
import { ref, onMounted } from 'vue';
import { useProjects } from '../composables/useProjects';
import { useHeadlines } from '../composables/useHeadlines.ts'; // Ensure this is imported

// Components
import SvgScrollAnimation from '../components/SvgScrollAnimation.vue';
import ArrowButton from '../components/ArrowButton.vue';
import ProjectCard from '../components/ProjectCard.vue';
import GlitchingText from '../components/GlitchingText.vue';
import ServicesList from '../components/ServicesList.vue';
import ContactForm from '../components/ContactForm.vue';
import Reviews from '../components/Reviews.vue';

// Assets
// import StopAnim from '../assets/stop-blending-in/stop.svg';
// import BlendingAnim from '../assets/stop-blending-in/blending.svg';
// import InAnim from '../assets/stop-blending-in/in.svg';
import ItsAnim from '../assets/stop-blending-in/its.svg';
import TimeAnim from '../assets/stop-blending-in/time.svg';
import ToAnim from '../assets/stop-blending-in/to.svg';
import StandAnim from '../assets/stop-blending-in/stand.svg';
import OutAnim from '../assets/stop-blending-in/out.svg';
import TrustedAnim from '../assets/trusted-partners/trusted.svg';
import PartnerAnim from '../assets/trusted-partners/partners.svg';
import TangibleAnim from '../assets/trusted-partners/tangible.svg';
import ResultsAnim from '../assets/trusted-partners/results.svg';

const standOutSvgs = [
  // { component: StopAnim, color: 'fill-id-yellow' },  
  // { component: BlendingAnim, color: 'fill-id-yellow' },
  // { component: InAnim, color: 'fill-id-yellow' },
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
// const showGoDigital = ref(false); // Local state to control the button

// Function to advance to the next headline
const showNextHeadline = () => {
  if (headlines.value.length === 0) {
    // Handle empty headlines gracefully
    return;
  }
  const headlineObj = headlines.value[currentHeadlineIndex.value];
  let headlineText = '';
  if (headlineObj && headlineObj.title) {
    headlineText = headlineObj.title;
  }
  if (headlineText) {
    currentHeadline.value = headlineText;
  } else {
    currentHeadline.value = '';
  }
  currentHeadlineIndex.value = (currentHeadlineIndex.value + 1) % headlines.value.length;
};

// Event handler for when the animation enters the hiding phase
const onGlitchHiding = () => {
  // showGoDigital.value = true;
};

// --- OTHER COMPOSABLES AND LOGIC ---
const { projects, fetchProjects } = useProjects();

onMounted(async () => {
  // Fetch all necessary data
  await Promise.all([
    fetchHeadlines(),
    fetchProjects()
  ]);
  // Start the first headline animation
  showNextHeadline();
});
</script>

<style scoped></style>