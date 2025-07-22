<template>
  <div v-if="reviews?.length" class="reviews-wrapper flex flex-col">

    <div v-for="review in reviews" :key="review.id" class="review w-full flex flex-col gap-[30px]">

      <div v-html="review.content" class="review-content"></div>

      <div class="review-meta">
        <h4 v-if="review.reviewer_name">{{ review.reviewer_name }}</h4>

        <h5 v-if="review.reviewer_title" class="pt-[3px] pb-[3px]">{{ review.reviewer_title }}</h5>

        <div v-if="review.clients?.length" class="clients flex flex-col gap-[5px]">
          <h5 v-for="client in review.clients" :key="client.id" v-html="client.name"></h5>
        </div>

        <div class="stars flex justify-start items-center gap-[5px] pt-[30px]">
          <span v-for="star in 5" :key="star" class="star color-id-purple">★</span>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { useReviews } from '../composables/useReviews.ts';
const { reviews } = useReviews();
</script>

<style scoped>
.review-content * {
  font-size: 21px;
  line-height: 1.675em;
}

.star {
  font-size: 20px;
}
</style>