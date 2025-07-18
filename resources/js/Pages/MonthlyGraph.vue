<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

const currentYear = ref(new Date().getFullYear())
const availableYears = ref([2023, 2024, 2025, 2026, 2027]) // ← DBにある年を自動取得したい場合はAPI化も可能

const changeYear = (year) => {
    currentYear.value = year
}
</script>

<template>
    <Head title="月別グラフ"/>

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
                            <span class="font-bold text-lg">年別表示：</span>
                            <button
                                v-for="year in availableYears"
                                :key="year"
                                class="mx-1 px-3 py-1 border rounded"
                                :class="{ 'bg-blue-500 text-white': currentYear === year }"
                                @click="changeYear(year)"
                            >
                                {{ year }}年
                            </button>
                        </div>

                        <BarChart
                            :year="currentYear"
                            label="月別支出合計"
                            apiUrl="/api/chart-data"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
