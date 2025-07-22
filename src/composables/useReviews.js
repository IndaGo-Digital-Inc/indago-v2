import { ref, onMounted } from 'vue';

export function useReviews() {
    const reviews = ref([]);
    const loading = ref(false);
    const error = ref(null);

    async function fetchReviews() {
        loading.value = true;
        error.value = null;
        try {
            // Order by menu_order (page order)
            const res = await fetch('/wp-json/wp/v2/reviews?orderby=menu_order&order=asc');
            if (!res.ok) throw new Error('Failed to fetch reviews');
            const data = await res.json();
            // Map to include body content and custom fields
            reviews.value = data.map(post => ({
                id: post.id,
                title: post.title?.rendered || '',
                content: post.content?.rendered || '',
                // Access fields directly on the post object
                reviewer_name: post.reviewer_name || '',
                reviewer_title: post.reviewer_title || '',
            }));
        } catch (e) {
            error.value = e;
        } finally {
            loading.value = false;
        }
    }

    onMounted(fetchReviews);

    return {
        reviews,
        loading,
        error,
        fetchReviews,
    };
}
