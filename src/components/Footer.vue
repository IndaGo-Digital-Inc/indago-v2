<script setup>
import { useCompanyInfo } from '../composables/useCompanyInfo';
import IdGO from '../assets/go.svg';

const { companyInfo, loading, error } = useCompanyInfo();
</script>

<template>
    <footer class="bg-id-black pb-[30px] pt-[120px]">
        <section class="container contact-info">
            <div v-if="loading">Loading...</div>
            <div v-else-if="error">Could not load company info.</div>
            <div v-else class="flex justify-between items-end gap-[20px]">
                <div class="flex flex-col justify-center items-start gap-[21px]">
                    <IdGO class="h-[40px] fill-id-purple" />
                    <div class="flex flex-col items-start gap-[5px]">
                        <p>{{ companyInfo.company_name }}</p>
                        <p>{{ companyInfo.company_location }}</p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-[5px]">
                    <p><a :href="`tel:${companyInfo.company_phone}`">{{ companyInfo.company_phone }}</a></p>
                    <p><a :href="`mailto:${companyInfo.company_email}`">{{ companyInfo.company_email }}</a></p>
                </div>
            </div>
        </section>
    </footer>
</template>

<style scoped>
.contact-info p {
    font-size: 14px;
}
</style>