import { ref, onMounted } from 'vue';

/**
 * useHeadlines composable
 * Fetches headline strings from the WordPress REST API (custom post type 'headline').
 * Returns: headlines (array of strings), loading (bool), error (string|null), and a manual fetchHeadlines() method.
 */
export function useHeadlines() {
  const headlines = ref([]);
  const loading = ref(true);
  const error = ref(null);

  async function fetchHeadlines() {
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch('https://indago-v2.local/wp-json/wp/v2/headlines?per_page=100');
      if (!res.ok) throw new Error('Failed to fetch headlines');
      const data = await res.json();
      // Use post titles as headline strings
      headlines.value = data.map(post => post.title.rendered).filter(Boolean);
    } catch (e) {
      error.value = e.message;
      headlines.value = [];
    } finally {
      loading.value = false;
    }
  }

  onMounted(fetchHeadlines);

  return { headlines, loading, error, fetchHeadlines };
}
