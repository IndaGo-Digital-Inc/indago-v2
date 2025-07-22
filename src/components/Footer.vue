<script setup>
import { useCompanyInfo } from '../composables/useCompanyInfo';

const { companyInfo, loading, error } = useCompanyInfo();
</script>

<template>
    <footer class="container bg-id-black">
        <div v-if="loading">Loading...</div>
        <div v-else-if="error">Could not load company info.</div>
        <div v-else>
            <p>&copy; {{ new Date().getFullYear() }} {{ companyInfo.company_name }}</p>
            <p>{{ companyInfo.company_location }}</p>
            <p>
                <a :href="`tel:${companyInfo.company_phone}`">{{ companyInfo.company_phone }}</a> |
                <a :href="`mailto:${companyInfo.company_email}`">{{ companyInfo.company_email }}</a>
            </p>
        </div>
    </footer>
</template>

<style scoped></style>