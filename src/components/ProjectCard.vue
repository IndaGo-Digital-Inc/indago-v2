<template>
  <article
    class="project-card flex flex-col items-start justify-start mt-[60px] pt-[30px] border-t border-id-dark-grey">
    <div class="card-header cursor-pointer" @click="toggleContent">
      <div class="w-full flex gap-[20px]">
        <div class="w-1/2 flex flex-col gap-[3px]">
          <h4 class="color-id-light-grey">{{ title }}</h4>
          <template v-if="taxonomies?.category">
            <h5 v-for="category in taxonomies.category" :key="category.id" class="color-id-medium-grey pt-[5px]">
              {{ category.name }}
            </h5>
          </template>
        </div>
        <div class="w-1/2">
          <img :src="image" :alt="title" />
        </div>
      </div>
    </div>

    <transition @before-enter="beforeEnter" @enter="enter" @before-leave="beforeLeave" @leave="leave">
      <div v-if="(sortedServices.length || excerpt) && contentVisible" class="card-content w-full">
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
        <p v-if="excerpt" class="color-id-medium-grey pt-[20px]">{{ excerpt }}</p>
      </div>
    </transition>
  </article>
</template>

<script setup>
// Import 'computed' from Vue
import { ref, computed } from 'vue';

const props = defineProps({
  title: { type: String, required: true },
  image: { type: String, required: true },
  taxonomies: { type: Object, default: () => ({}) },
  excerpt: { type: String, default: '' },
  link: { type: String, default: '' },
});

// --- THIS IS THE FIX ---
// Create a computed property that returns the services sorted by their new 'menu_order' field.
const sortedServices = computed(() => {
  // Check if the services taxonomy exists
  const services = props.taxonomies?.['project-service'];
  if (!Array.isArray(services)) {
    return [];
  }

  // Create a copy of the array and sort it by the menu_order property.
  return [...services].sort((a, b) => a.menu_order - b.menu_order);
});

const contentVisible = ref(false);
function toggleContent() {
  contentVisible.value = !contentVisible.value;
}

// ... Transition hooks remain the same ...
function beforeEnter(el) { /* ... */ }
function enter(el) { /* ... */ }
function beforeLeave(el) { /* ... */ }
function leave(el) { /* ... */ }
</script>

<style scoped>
/* ... Styles remain the same ... */
.project-card img {
  display: block;
  width: 100%;
}

.project-card h5+h5 {
  margin-top: 10px;
}

.card-content {
  overflow: hidden;
}
</style>