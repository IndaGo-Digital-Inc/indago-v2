import { ref, onMounted, Ref } from "vue";

export interface Review {
	id: number;
	title: string;
	content: string;
	reviewer_name: string;
	reviewer_title: string;
	clients: Array<{ id: number; name: string; slug: string }>;
}

export function useReviews() {
	const reviews: Ref<Review[]> = ref([]);
	const loading = ref(false);
	const error = ref<Error | null>(null);

	async function fetchReviews() {
		loading.value = true;
		error.value = null;
		try {
			// Add _embed to get taxonomy terms with the review
			const res = await fetch(
				"/wp-json/wp/v2/reviews?orderby=menu_order&order=asc&_embed"
			);
			if (!res.ok) throw new Error("Failed to fetch reviews");
			const data = await res.json();
			reviews.value = data.map((post: any) => {
				// Find client terms in _embedded['wp:term']
				let clients: Array<{ id: number; name: string; slug: string }> =
					[];
				if (
					post._embedded &&
					Array.isArray(post._embedded["wp:term"])
				) {
					// Find the array for the 'clients' taxonomy
					const clientTermsArr = post._embedded["wp:term"].find(
						(terms: any[]) =>
							terms.length && terms[0].taxonomy === "client"
					);
					if (Array.isArray(clientTermsArr)) {
						clients = clientTermsArr.map((term) => ({
							id: term.id,
							name: term.name,
							slug: term.slug,
						}));
					}
				}
				return {
					id: post.id,
					title: post.title?.rendered || "",
					content: post.content?.rendered || "",
					reviewer_name: post.reviewer_name || "",
					reviewer_title: post.reviewer_title || "",
					clients,
				};
			});
		} catch (e: any) {
			error.value = e;
		} finally {
			loading.value = false;
		}
	}

	onMounted(fetchReviews);

	return {
		reviews,
		loading,
		error,
		fetchReviews,
	};
}
