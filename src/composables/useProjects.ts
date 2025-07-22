import { ref } from 'vue';

export function useProjects() {
  const projects = ref<any[]>([]);
  const loading = ref(false);
  const error = ref<Error | null>(null);

  const fetchProjects = async () => {
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch('https://indago-v2.local/wp-json/wp/v2/projects?_embed&orderby=menu_order&order=asc');
      const data = await res.json();
      // For each project, extract featured image and taxonomies
      const projectsCleaned = data.map((project: any) => {
        let image = '';
        if (project._embedded?.['wp:featuredmedia']?.[0]) {
          image = project._embedded['wp:featuredmedia'][0].source_url;
        }

        // Dynamically collect all taxonomies
        const taxonomies: Record<string, { id: number; name: string }[]> = {};
        if (project._embedded?.['wp:term']) {
          for (const termGroup of project._embedded['wp:term']) {
            if (Array.isArray(termGroup) && termGroup.length > 0) {
              const taxonomy = termGroup[0]?.taxonomy;
              if (taxonomy) {
                taxonomies[taxonomy] = termGroup.map((term: any) => ({ id: term.id, name: term.name }));
              }
            }
          }
        }
        // Strip HTML tags from excerpt
        let excerpt = project.excerpt?.rendered || '';
        excerpt = excerpt.replace(/<[^>]+>/g, '').trim();
        return {
          title: project.title.rendered,
          image,
          excerpt,
          taxonomies, // object: { [taxonomySlug]: [{ id, name }] }
        };
      });
      projects.value = projectsCleaned;
    } catch (err: any) {
      error.value = err;
    } finally {
      loading.value = false;
    }
  };

  return { projects, loading, error, fetchProjects };
}
