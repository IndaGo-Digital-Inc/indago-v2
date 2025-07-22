<template>
  <div class="reviews-wrapper flex flex-col gap-10 py-12">
    <div v-if="loading" class="text-center py-8">Loading reviews...</div>
    <div v-else-if="error" class="text-center text-red-500 py-8">Error loading reviews.</div>
    <div v-else>
      <div v-for="review in reviews" :key="review.id" class="review-card border border-id-dark-grey rounded-lg p-6 bg-id-bg">
        <div class="review-content text-lg mb-4" v-html="review.content"></div>
        <div class="review-meta text-sm text-id-medium-grey flex flex-col gap-1">
          <span v-if="review.reviewer_name" class="font-bold">{{ review.reviewer_name }}</span>
          <span v-if="review.reviewer_title">{{ review.reviewer_title }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useReviews } from '../composables/useReviews';
const { reviews, loading, error } = useReviews();
</script>

<style scoped>
.reviews-wrapper {
  max-width: 700px;
  margin: 0 auto;
}
.review-card {
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.review-meta {
  margin-top: 10px;
}
</style>
