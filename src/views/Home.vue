<template>
  <div class="container pt-[100px] flex flex-col">
    <!-- Header Section -->
    <div class="flex flex-col items-start justify-between w-full gap-[140px]">
      <div class="flex flex-col gap-[30px] w-full mx-auto">
        <IdLogoText class="h-[20px] w-[191px] fill-id-medium-grey" />
        <TypingText :text="'Bespoke Digital Marketing that Captivates and Converts'" class="w-full" />
      </div>
      <div class="w-full flex justify-end">
        <ArrowButton>GO Digital</ArrowButton>
        <button class="w-[36px] hidden md:block">
          <ChevronDown class="fill-id-dark-grey" />
        </button>
      </div>
    </div>

    <!-- Animation & Description Section -->
    <div class="flex flex-col items-start max-w-[342px] mx-auto pt-[150px]">
      <SvgScrollAnimation :svgs="svgs" :deadSpace="0.2" />
      <p class="pt-[60px]">
        IndaGo Digital crafts captivating digital experiences that set you apart from your competition and drive measurable results. We blend innovative website development, data-driven SEO strategies, and results-oriented digital marketing to fuel your online success.
      </p>
    </div>

    <!-- Projects Section -->
    <div class="project-wrapper flex flex-col pt-[60px] mb-[120px]">
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
// Components
import SvgScrollAnimation from '../components/SvgScrollAnimation.vue';
import TypingText from '../components/TypingText.vue';
import ArrowButton from '../components/ArrowButton.vue';
import ProjectCard from '../components/ProjectCard.vue';

// SVGs
import ChevronDown from '../assets/id-chevron-down.svg';
import IdLogoText from '../assets/id-logo-text.svg';
import StopAnim from '../assets/stop-blending-in/stop.svg';
import BlendingAnim from '../assets/stop-blending-in/blending.svg';
import InAnim from '../assets/stop-blending-in/in.svg';
import OutAnim from '../assets/stop-blending-in/out.svg';
import StandAnim from '../assets/stop-blending-in/stand.svg';
import ToAnim from '../assets/stop-blending-in/to.svg';
import TimeAnim from '../assets/stop-blending-in/time.svg';
import ItsAnim from '../assets/stop-blending-in/its.svg';

import { ref, onMounted } from 'vue';

// Animation SVGs
const svgs = [
  { component: StopAnim, color: 'fill-id-purple' },
  { component: BlendingAnim, color: 'fill-id-purple' },
  { component: InAnim, color: 'fill-id-purple' },
  { component: ItsAnim, color: 'fill-id-purple' },
  { component: TimeAnim, color: 'fill-id-purple' },
  { component: ToAnim, color: 'fill-id-purple' },
  { component: StandAnim, color: 'fill-id-purple' },
  { component: OutAnim, color: 'fill-id-purple' },
];

// Projects state
const projects = ref([]);

// Fetch projects from WP REST API
onMounted(async () => {
  try {
    const res = await fetch('https://indago-v2.local/wp-json/wp/v2/projects?_embed&orderby=menu_order&order=asc');
    const data = await res.json();
    projects.value = data.map(project => {
      let image = '';
      if (project._embedded?.['wp:featuredmedia']?.[0]) {
        image = project._embedded['wp:featuredmedia'][0].source_url;
      }
      let subtitle = '';
      if (project._embedded?.['wp:term']?.[0]) {
        subtitle = project._embedded['wp:term'][0].map(cat => cat.name).join(', ');
      }
      return {
        title: project.title.rendered,
        subtitle,
        image,
      };
    });
  } catch (e) {
    console.error('Error fetching projects:', e);
  }
});
</script>