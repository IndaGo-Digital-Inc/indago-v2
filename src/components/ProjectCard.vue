<template>
  <div class="project-card flex items-start justify-between gap-[20px] mt-[60px] pt-[30px] border-t border-id-dark-grey">
    <div class="w-1/2 flex flex-col gap-[3px]">
      <h4 class="color-id-light-grey">{{ title }}</h4>
      <h5 class="color-id-medium-grey">{{ subtitle }}</h5>
    </div>
    <div class="w-1/2">
      <img :src="image" :alt="title" />
    </div>
  </div>
</template>

<script setup>
/**
 * ProjectCard - reusable project display component
 * Props:
 *  - title: string (required)
 *  - subtitle: string (optional)
 *  - image: string (required, image src)
 */
const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  image: {
    type: String,
    required: true,
  },
});
</script>

<style scoped>
.project-card {

    img {
        display: block;
    }
}
</style>
