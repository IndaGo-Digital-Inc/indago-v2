// useHeadlines.ts
// Fetches and processes Headlines custom post type from WordPress REST API
import { ref } from "vue";

export interface ProcessedHeadline {
    id: number;
    title: string;
    words: string[][]; // Array of words, each word is an array of characters
}

export function useHeadlines() {
    const headlines = ref<ProcessedHeadline[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Fetch headlines from WP REST API (custom post type: headline)
    async function fetchHeadlines() {
        loading.value = true;
        error.value = null;
        try {
            // Adjust the endpoint if your WP site is not at root
            const res = await fetch(
                "/wp-json/wp/v2/headlines?_fields=id,title.rendered"
            );
            if (!res.ok) throw new Error("Failed to fetch headlines");
            const data = await res.json();
            headlines.value = data.map((item: any) => ({
                id: item.id,
                title: item.title.rendered,
                words: item.title.rendered
                    .split(/\s+/)
                    .map((word: string) => word.split("")),
            }));
        } catch (e: any) {
            error.value = e.message || "Unknown error";
        } finally {
            loading.value = false;
        }
    }

    return {
        headlines,
        loading,
        error,
        fetchHeadlines,
    };
}
