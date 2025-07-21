import { ref } from 'vue';

/**
 * useProjects composable
 * Fetches WordPress projects and exposes reactive state and error feedback.
 */
export function useProjects() {
  const projects = ref([]);
  const loading = ref(true);
  const error = ref(null);

  async function fetchProjects() {
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch('https://indago-v2.local/wp-json/wp/v2/projects?_embed&orderby=menu_order&order=asc');
      const data = await res.json();
      projects.value = data.map(project => {
        let image = '';
        if (project._embedded?.['wp:featuredmedia']?.[0]) {
          image = project._embedded['wp:featuredmedia'][0].source_url;
        }
        let subtitle = '';
        if (project._embedded?.['wp:term']?.[0]) {
          subtitle = project._embedded['wp:term'][0].map(cat => cat.name).join(', ');
        }
        return {
          title: project.title.rendered,
          subtitle,
          image,
        };
      });
    } catch (e) {
      error.value = 'Unable to load project data. Please check your connection or try again later.';
      console.error('Failed to fetch projects from WordPress REST API:', e);
      console.log('User feedback:', error.value);
    } finally {
      loading.value = false;
    }
  }

  // Fetch on composable use
  fetchProjects();

  return {
    projects,
    loading,
    error,
    fetchProjects,
  };
}
