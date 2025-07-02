<template>
	<div>
		<h2>Our Work</h2>

		<div v-if="loading">Loading projects...</div>

		<div v-if="error">
			{{ error }}
		</div>

		<div v-if="projects.length > 0">
			<article v-for="project in projects" :key="project.id">
				<!-- Featured Image -->
				<div
					v-if="
						project._embedded &&
						project._embedded['wp:featuredmedia']
					"
				>
					<img
						:src="
							project._embedded['wp:featuredmedia'][0].source_url
						"
						:alt="project.title.rendered"
					/>
				</div>

				<div>
					<!-- Title -->
					<h3 v-html="project.title.rendered"></h3>

					<!-- Categories -->
					<div
						v-if="project._embedded && project._embedded['wp:term']"
					>
						<template
							v-for="term_group in project._embedded['wp:term']"
						>
							<template v-for="term in term_group">
								<span
									v-if="term.taxonomy === 'project_category'"
									:key="term.id"
								>
									{{ term.name }}
								</span>
							</template>
						</template>
					</div>

					<!-- Excerpt -->
					<div v-html="project.excerpt.rendered"></div>

					<!-- Client Info -->
					<div
						v-if="project._embedded && project._embedded['wp:term']"
					>
						<template
							v-for="term_group in project._embedded['wp:term']"
						>
							<template v-for="term in term_group">
								<div
									v-if="term.taxonomy === 'client'"
									:key="term.id"
								>
									<!-- The src now points to the URL we fetch and attach in the script -->
									<img
										v-if="term.client_logo_url"
										:src="term.client_logo_url"
										:alt="term.name + ' Logo'"
									/>
									<span>{{ term.name }}</span>
								</div>
							</template>
						</template>
					</div>
				</div>
			</article>
		</div>
	</div>
</template>

<script setup>
import {ref, onMounted} from 'vue';

const projects = ref([]);
const loading = ref(true);
const error = ref(null);

async function fetchProjects() {
	try {
		// 1. Fetch the initial list of projects
		const projectsResponse = await fetch('/wp-json/wp/v2/projects?_embed');
		if (!projectsResponse.ok) {
			throw new Error('Failed to fetch projects.');
		}
		let projectsData = await projectsResponse.json();

		// 2. Gather all unique client logo IDs from the fetched data
		const logoIds = new Set();
		projectsData.forEach((project) => {
			if (project._embedded && project._embedded['wp:term']) {
				project._embedded['wp:term'].forEach((term_group) => {
					term_group.forEach((term) => {
						if (
							term.taxonomy === 'client' &&
							term.acf &&
							term.acf.client_logo
						) {
							logoIds.add(term.acf.client_logo);
						}
					});
				});
			}
		});

		// 3. If we found any logo IDs, fetch their details from the media endpoint
		if (logoIds.size > 0) {
			const mediaPromises = Array.from(logoIds).map((id) =>
				fetch(`/wp-json/wp/v2/media/${id}`).then((res) => {
					if (!res.ok) {
						console.error(`Failed to fetch media for ID: ${id}`);
						return null; // Return null on failure to not break Promise.all
					}
					return res.json();
				})
			);
			const mediaItems = await Promise.all(mediaPromises);

			// 4. Create a map of logo ID to its full URL
			const logoUrlMap = new Map();
			mediaItems.forEach((media) => {
				if (media && media.source_url) {
					logoUrlMap.set(media.id, media.source_url);
				}
			});

			// 5. Attach the fetched URLs back to the original project data
			projectsData = projectsData.map((project) => {
				if (project._embedded && project._embedded['wp:term']) {
					project._embedded['wp:term'].forEach((term_group) => {
						term_group.forEach((term) => {
							if (
								term.taxonomy === 'client' &&
								term.acf &&
								term.acf.client_logo
							) {
								// Add a new property `client_logo_url` to the term object
								term.client_logo_url = logoUrlMap.get(
									term.acf.client_logo
								);
							}
						});
					});
				}
				return project;
			});
		}

		// 6. Set the final, enriched data to be rendered
		projects.value = projectsData;
	} catch (e) {
		error.value = e.message;
	} finally {
		loading.value = false;
	}
}

onMounted(() => {
	fetchProjects();
});
</script>

<style scoped></style>
