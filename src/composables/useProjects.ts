// src/composables/useProjects.ts

import { ref } from "vue";

// Helper function to decode HTML entities like &amp; into characters like &
function decodeHtmlEntities(text: string): string {
    const textarea = document.createElement("textarea");
    textarea.innerHTML = text;
    return textarea.value;
}

// Define a more specific type for an Image Object
interface ProjectImage {
    id: number;
    url: string;
    alt: string;
}

// Define a more specific type for a Project
interface Project {
    title: string;
    image: string; // Featured image URL
    excerpt: string;
    taxonomies: Record<string, { id: number; name: string }[]>;
    link: string; // Project URL
    // NEW: Optional properties for the mobile and desktop images
    mobile_project_image?: ProjectImage;
    desktop_project_image?: ProjectImage;
}

export function useProjects() {
    const projects = ref<Project[]>([]);
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

            const projectsCleaned: Project[] = data.map((project: any) => {
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

                let excerpt = project.excerpt?.rendered || "";
                excerpt = decodeHtmlEntities(excerpt)
                    .replace(/<[^>]+>/g, "")
                    .trim();

                const projectUrl = project.project_url || '';

                // === NEW: Extract individual mobile and desktop images ===
                const mobileProjectImage = project.mobile_project_image || undefined; // Will be null or object
                const desktopProjectImage = project.desktop_project_image || undefined; // Will be null or object

                return {
                    title: decodeHtmlEntities(project.title.rendered),
                    image, // This is still the featured image
                    excerpt,
                    taxonomies,
                    link: projectUrl,
                    mobile_project_image: mobileProjectImage,
                    desktop_project_image: desktopProjectImage,
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