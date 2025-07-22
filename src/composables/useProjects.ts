import { ref } from "vue";

// Helper function to decode HTML entities like &amp; into characters like &
function decodeHtmlEntities(text: string): string {
    // This is a standard and safe way to decode entities in a browser environment.
    const textarea = document.createElement("textarea");
    textarea.innerHTML = text;
    return textarea.value;
}

export function useProjects() {
    const projects = ref<any[]>([]);
    const loading = ref(false);
    const error = ref<Error | null>(null);

    const fetchProjects = async () => {
        loading.value = true;
        error.value = null;
        try {
            const res = await fetch(
                "https://indago-v2.local/wp-json/wp/v2/projects?_embed&orderby=menu_order&order=asc"
            );
            if (!res.ok) throw new Error("Failed to fetch projects");
            const data = await res.json();

            const projectsCleaned = data.map((project: any) => {
                let image = "";
                if (project._embedded?.["wp:featuredmedia"]?.[0]) {
                    image = project._embedded["wp:featuredmedia"][0].source_url;
                }

                const taxonomies: Record<
                    string,
                    { id: number; name: string }[]
                > = {};
                if (project._embedded?.["wp:term"]) {
                    for (const termGroup of project._embedded["wp:term"]) {
                        if (Array.isArray(termGroup) && termGroup.length > 0) {
                            const taxonomy = termGroup[0]?.taxonomy;
                            if (taxonomy) {
                                // FIX: Decode the name for each term
                                taxonomies[taxonomy] = termGroup.map(
                                    (term: any) => ({
                                        id: term.id,
                                        name: decodeHtmlEntities(term.name),
                                    })
                                );
                            }
                        }
                    }
                }

                // FIX: Decode the excerpt and strip HTML tags
                let excerpt = project.excerpt?.rendered || "";
                excerpt = decodeHtmlEntities(excerpt)
                    .replace(/<[^>]+>/g, "")
                    .trim();

                return {
                    // FIX: Decode the project title
                    title: decodeHtmlEntities(project.title.rendered),
                    image,
                    excerpt,
                    taxonomies,
                };
            });
            projects.value = projectsCleaned;
        } catch (err: any) {
            error.value = err;
        } finally {
            loading.value = false;
        }
    };

    // Note: You might want to call fetchProjects() here or in the component that uses the composable.
    // For example: fetchProjects();

    return { projects, loading, error, fetchProjects };
}
