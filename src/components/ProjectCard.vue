<template>
  <div class="project-card flex flex-col items-start justify-start mt-[60px] pt-[30px] border-t border-id-dark-grey">
    <div class="card-header cursor-pointer" @click="toggleContent">
      <div class="w-full flex gap-[20px]">
        <div class="w-1/2 flex flex-col gap-[3px]">
          <h4 class="color-id-light-grey">{{ title }}</h4>
          <template v-if="taxonomies && taxonomies.category">
            <h5 v-for="(category, index) in taxonomies.category" :key="'cat-' + index"
              class="color-id-medium-grey pt-[5px]">{{ category.name }}</h5>
          </template>
        </div>
        <div class="w-1/2">
          <img :src="image" :alt="title" />
        </div>
      </div>
    </div>
    <transition
      @before-enter="beforeEnter"
      @enter="enter"
      @before-leave="beforeLeave"
      @leave="leave"
    >
      <div
        v-if="((taxonomies && taxonomies.service?.length) || excerpt) && contentVisible"
        class="card-content w-full"
      >
        <div
          v-if="taxonomies && taxonomies.service?.length"
          class="w-full flex gap-[20px]"
        >
          <!-- empty column -->
          <div class="w-1/2 flex flex-col gap-[3px]"></div>
          <div class="w-1/2">
            <div class="pt-[40px]">
              <h5 v-for="(service, index) in taxonomies.service" :key="'svc-' + index" class="color-id-medium-grey">{{
                service.name }}</h5>
            </div>
          </div>
        </div>
        <p v-if="excerpt" class="color-id-medium-grey">{{ excerpt }}</p>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  image: {
    type: String,
    required: true,
  },
  taxonomies: {
    type: Object,
    default: () => ({}),
  },
  excerpt: {
    type: String,
    default: '',
  },
  link: {
    type: String,
    default: '',
  },
});

const contentVisible = ref(false);
function toggleContent() {
  contentVisible.value = !contentVisible.value;
}

// Slide-down/up transition hooks
function beforeEnter(el) {
  el.style.height = '0';
  el.style.opacity = '0';
}
function enter(el) {
  el.style.transition = 'height 0.25s ease, opacity 0.25s';
  el.style.height = el.scrollHeight + 'px';
  el.style.opacity = '1';
}
function beforeLeave(el) {
  el.style.height = el.scrollHeight + 'px';
  el.style.opacity = '1';
}
function leave(el) {
  el.style.transition = 'height 0.25s ease, opacity 0.25s';
  // Force reflow to ensure the transition is applied
  void el.offsetHeight;
  el.style.height = '0';
  el.style.opacity = '0';
}
</script>

<style scoped>
.project-card {
  img {
    display: block;
  }
  h5+h5 {
    margin-top: 10px;
  }
}
.card-content {
  overflow: hidden;
}
</style>