import { ref, onMounted } from 'vue';

export function useServices() {
  const services = ref<any[]>([]);
  const loading = ref(false);
  const error = ref<Error | null>(null);

  const fetchServices = async () => {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch('/wp-json/wp/v2/services?per_page=100');
      if (!response.ok) throw new Error('Failed to fetch services');
      services.value = await response.json();
    } catch (err: any) {
      error.value = err;
    } finally {
      loading.value = false;
    }
  };

  onMounted(fetchServices);

  return { services, loading, error, fetchServices };
}