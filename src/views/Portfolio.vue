<template>
	<div>
		<h2 class="text-3xl font-bold mb-8 text-gray-800">Our Work</h2>

		<div v-if="loading" class="text-center text-gray-500 py-10">
			<p>Loading projects...</p>
		</div>

		<div
			v-if="error"
			class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md"
		>
			{{ error }}
		</div>

		<div
			v-if="projects.length > 0"
			class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
		>
			<article
				v-for="project in projects"
				:key="project.id"
				class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-1"
			>
				<!-- Featured Image -->
				<div
					class="h-48 w-full bg-gray-200"
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
						class="w-full h-full object-cover"
					/>
				</div>

				<div class="p-6 flex flex-col flex-grow">
					<!-- Title -->
					<h3
						class="text-xl font-bold text-gray-900 mb-2"
						v-html="project.title.rendered"
					></h3>

					<!-- Categories -->
					<div
						class="mb-4 flex flex-wrap gap-2"
						v-if="project._embedded && project._embedded['wp:term']"
					>
						<template
							v-for="term_group in project._embedded['wp:term']"
						>
							<template v-for="term in term_group">
								<span
									v-if="term.taxonomy === 'project_category'"
									:key="term.id"
									class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full"
								>
									{{ term.name }}
								</span>
							</template>
						</template>
					</div>

					<!-- Excerpt -->
					<div
						class="text-gray-700 text-base mb-4 flex-grow"
						v-html="project.excerpt.rendered"
					></div>

					<!-- Client Info -->
					<div
						class="mt-auto pt-4 border-t border-gray-200"
						v-if="project._embedded && project._embedded['wp:term']"
					>
						<template
							v-for="term_group in project._embedded['wp:term']"
						>
							<template v-for="term in term_group">
								<div
									v-if="term.taxonomy === 'client'"
									:key="term.id"
									class="flex items-center"
								>
									<img
										v-if="term.client_logo_url"
										:src="term.client_logo_url"
										:alt="term.name + ' Logo'"
										class="h-10 w-10 rounded-full object-contain mr-3 bg-gray-100 p-1"
									/>
									<span
										class="text-sm font-semibold text-gray-600"
										>{{ term.name }}</span
									>
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
						// ...existing code...
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
