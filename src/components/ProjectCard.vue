<template>
  <article
    class="project-card flex flex-col items-start justify-start pt-[40px] pb-[20px] border-t border-id-dark-grey">
    <div class="card-header cursor-pointer w-full" @click="toggleContent">
      <div class="w-full flex gap-[20px]">
        <div class="w-1/2 flex flex-col gap-[3px] relative">
          <h4 class="color-id-light-grey">{{ title }}</h4>
          <template v-if="taxonomies?.category">
            <h5 v-for="category in taxonomies.category" :key="category.id" class="color-id-medium-grey pt-[5px]">
              {{ category.name }}
            </h5>
          </template>
        </div>
        <div class="w-1/2 relative">
          <img :src="image" :alt="title" />
          <ChevronDown
            class="w-full max-w-[14px] fill-id-medium-grey absolute bottom-[-40px] right-0 transition-transform duration-300 ease-in-out"
            :class="{ 'rotate-180': contentVisible }" />
        </div>
      </div>
    </div>

    <transition @before-enter="beforeEnter" @enter="enter" @before-leave="beforeLeave" @leave="leave">
      <div
        v-if="(sortedServices.length || excerpt || link || mobile_project_image || desktop_project_image) && contentVisible"
        class="card-content w-full">
        <div v-if="sortedServices.length" class="w-full flex gap-[20px]">
          <div class="w-1/2 flex flex-col gap-[3px]"></div>
          <div class="w-1/2">
            <div class="pt-[40px]">
              <h5 v-for="service in sortedServices" :key="service.id" class="color-id-medium-grey">
                {{ service.name }}
              </h5>
            </div>
          </div>
        </div>
        <p v-if="excerpt" class="color-id-medium-grey pt-[40px] pb-[50px]">{{ excerpt }}</p>

        <div v-if="mobile_project_image"
          class="w-full max-w-[70vw] m-x-auto max-h-[140vw] mb-[50px] border-[5px] border-id-dark-grey overflow-y-scroll">
          <img :src="mobile_project_image.url" :alt="mobile_project_image.alt || title" class="w-full" />
        </div>

        <div v-if="link" class="w-full flex justify-end mb-[10px]">
          <ArrowButton :link="link" :linkLabel="'Visit Website'" :arrowClass="'fill-id-purple h-[16px]'"
            :customClass="'text-id-purple'" :customTarget="'_blank'"></ArrowButton>
        </div>
      </div>
    </transition>
  </article>
</template>

<script setup>
import { ref, computed } from 'vue';

// --- Components ---
import ArrowButton from './ArrowButton.vue';
import ChevronDown from '../assets/id-chevron-down.svg';

// --- Props ---
const props = defineProps({
  title: { type: String, required: true },
  image: { type: String, required: true }, // Featured Image (main image)
  taxonomies: { type: Object, default: () => ({}) },
  excerpt: { type: String, default: '' },
  link: { type: String, default: '' }, // Project URL
  mobile_project_image: { type: Object, default: null },
  desktop_project_image: { type: Object, default: null },
});

// --- Reactive State ---
const contentVisible = ref(false);

// --- Computed Properties ---
const sortedServices = computed(() => {
  const services = props.taxonomies?.['project-service'];
  if (!Array.isArray(services)) {
    return [];
  }
  return [...services].sort((a, b) => a.menu_order - b.menu_order);
});

// --- Methods ---
function toggleContent() {
  contentVisible.value = !contentVisible.value;
}

// --- Transition Hooks ---
function beforeEnter(el) {
  el.style.height = '0';
  el.style.opacity = '0';
  el.style.transition = 'height 0.3s ease-out, opacity 0.3s ease-out';
}

function enter(el) {
  el.style.height = el.scrollHeight + 'px';
  el.style.opacity = '1';
  el.addEventListener('transitionend', () => {
    el.style.height = 'auto';
  }, { once: true });
}

function beforeLeave(el) {
  el.style.height = el.scrollHeight + 'px';
  el.style.opacity = '1';
  el.style.transition = 'height 0.3s ease-in, opacity 0.3s ease-in';
}

function leave(el) {
  el.style.height = '0';
  el.style.opacity = '0';
}
</script>

<style scoped>
.project-card+.project-card {
  border-top-width: 1px;
}

.project-card img {
  display: block;
  width: 100%;
}

.project-card h5+h5 {
  margin-top: 16px;
}

.card-content {
  overflow: hidden;
}
</style>