import { ref, onMounted } from "vue";

/**
 * A helper function to decode HTML entities (e.g., &amp;) into characters (e.g., &).
 * @param text The string containing HTML entities.
 * @returns The decoded string.
 */
function decodeHtmlEntities(text: string): string {
    if (!text) return "";
    const textarea = document.createElement("textarea");
    textarea.innerHTML = text;
    return textarea.value;
}

export function useServices() {
    const services = ref<any[]>([]);
    const loading = ref(false);
    const error = ref<Error | null>(null);

    const fetchServices = async () => {
        loading.value = true;
        error.value = null;
        try {
            const response = await fetch(
                "/wp-json/wp/v2/services?per_page=100&orderby=menu_order&order=asc"
            );
            if (!response.ok) throw new Error("Failed to fetch services");
            const data = await response.json();

            // FIX: Map over the raw data to clean and decode it before saving to state.
            const cleanedServices = data.map((service: any) => {
                return {
                    ...service, // Keep all original properties
                    title: {
                        rendered: decodeHtmlEntities(service.title.rendered),
                    },
                    content: {
                        rendered: decodeHtmlEntities(service.content.rendered),
                    },
                    excerpt: {
                        rendered: decodeHtmlEntities(service.excerpt.rendered),
                    },
                };
            });

            services.value = cleanedServices;
        } catch (err: any) {
            error.value = err;
        } finally {
            loading.value = false;
        }
    };

    onMounted(fetchServices);

    return { services, loading, error, fetchServices };
}
