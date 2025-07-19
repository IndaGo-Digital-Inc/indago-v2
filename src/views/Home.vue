<template>
  <div class="container min-h-[300vh] pt-[100px] flex flex-col">
    <div class="flex flex-col items-start justify-between w-full gap-[140px]">
      <div class="flex flex-col gap-[30px] w-full mx-auto">
        <IdLogoText class="h-[20px] w-[191px] fill-id-medium-grey" />
        <TypingText :text="'Bespoke Digital Marketing that Captivates and Converts'" :speed="40" class="w-full" />
      </div>
      <div class="w-full">
        <div class="flex justify-end w-full">
          <ArrowButton>
            Let’s Connect
          </ArrowButton>
        </div>
        <button class="w-[36px] hidden md:block">
          <ChevronDown class="fill-id-dark-grey" />
        </button>
      </div>
    </div>
    <div class="flex flex-col items-start justify-between max-w-[342px] mx-auto pt-[150px]">
      <SvgScrollAnimation :svgs="svgs" :deadSpace="0.2" />
      <p class="pt-[60px]">IndaGo Digital crafts captivating digital experiences that set you apart from your
        competition and drive measurable results. We blend innovative website development, data-driven SEO strategies,
        and results-oriented digital marketing to fuel your online success.</p>
    </div>
    <div class="project-wrapper flex">
      <div>
        <h4>LOFTY Brickell</h4>
        <h5>Luxury Residential</h5>
      </div>
      <div>
        <img :src="loftyThumb" alt="LOFTY Brickell" />
      </div>
    </div>
  </div>
</template>

<script setup>
import SvgScrollAnimation from '../components/SvgScrollAnimation.vue';
import TypingText from '../components/TypingText.vue';
import ArrowButton from '../components/ArrowButton.vue';

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

import loftyThumb from '../assets/lofty-brickell-thumbnail.jpg';

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
</script>

<style scoped>
h1 {
  font-size: 44px;
}
</style>