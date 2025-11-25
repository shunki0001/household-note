<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

const currentYear = ref(new Date().getFullYear());
const availableYears = ref([2023, 2024, 2025, 2026, 2027]); // ← DBにある年を自動取得したい場合はAPI化も可能

const changeYear = (year) => {
    currentYear.value = year;
};
</script>

<template>
    <Head title="月別収入グラフ" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
                            <span class="text-lg font-bold">年別表示：</span>
                            <button
                                v-for="year in availableYears"
                                :key="year"
                                class="mx-1 rounded border px-3 py-1"
                                :class="{
                                    'bg-blue-500 text-white':
                                        currentYear === year,
                                }"
                                @click="changeYear(year)"
                            >
                                {{ year }}年
                            </button>
                        </div>

                        <BarChart
                            :year="currentYear"
                            label="月別収入合計"
                            api-url="mock"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
