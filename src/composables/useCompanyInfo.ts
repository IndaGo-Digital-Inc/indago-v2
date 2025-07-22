import { ref, onMounted } from "vue";

/**
 * Interface defining the structure of the company information object.
 */
export interface CompanyInfo {
	company_name: string;
	company_location: string;
	company_phone: string;
	company_email: string;
}

/**
 * A Vue composable to fetch and manage company information.
 */
export function useCompanyInfo() {
	// A reactive ref to store the company info object. Initialized with a default structure.
	const companyInfo = ref<CompanyInfo>({
		company_name: "",
		company_location: "",
		company_phone: "",
		company_email: "",
	});

	// Reactive state for tracking the loading status of the API request.
	const loading = ref(false);
	// Reactive state for storing any potential errors from the API request.
	const error = ref<Error | null>(null);

	/**
	 * Fetches the company information from the custom WordPress REST API endpoint.
	 */
	const fetchCompanyInfo = async () => {
		loading.value = true;
		error.value = null;
		try {
			const response = await fetch("/wp-json/indago/v1/company-info");

			if (!response.ok) {
				throw new Error("Failed to fetch company information");
			}

			const data: CompanyInfo = await response.json();
			companyInfo.value = data;
		} catch (err: any) {
			error.value = err;
			console.error("Error fetching company info:", err);
		} finally {
			loading.value = false;
		}
	};

	// Automatically call the fetch function when the composable is first used in a component.
	onMounted(fetchCompanyInfo);

	// Return the reactive state and the fetch function to the component.
	return {
		companyInfo,
		loading,
		error,
		fetchCompanyInfo, // Exposing the function allows for manual refetching if needed.
	};
}
