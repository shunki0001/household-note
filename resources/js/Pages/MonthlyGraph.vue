<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';

const currentYear = ref(new Date().getFullYear());
const availableYears = ref([2023, 2024, 2025, 2026, 2027]);

// 今月の月を取得
const currentMonth = new Date().getMonth() + 1;
// 今月が属する四半期を計算(1~3月:1, 4~6月:2, 7~9月:3, 10~12月:4)
const getQuarter = (month) => Math.ceil(month / 3);
// 初期値を動的に設定
const currentQuarter = ref(getQuarter(currentMonth));

// 四半期の定義
const quarters = [
    { start: 1, end: 3, label: '1~3月' },
    { start: 4, end: 6, label: '4~6月' },
    { start: 7, end: 9, label: '7~9月' },
    { start: 10, end: 12, label: '10~12月' },
];

const currentQuarterLabel = computed(() => quarters[currentQuarter.value - 1].label);
const currentRange = computed(() => quarters[currentQuarter.value - 1]);

const nextQuarter = () => {
    if (currentQuarter.value < 4) currentQuarter.value++;
};
const prevQuarter = () => {
    if (currentQuarter.value > 1) currentQuarter.value--;
};

const changeYear = (year) => {
    currentYear.value = year;
    currentQuarter.value = getQuarter(currentMonth);
};
</script>

<template>
<Head title="月別グラフ" />

<AuthenticatedLayout>
<div class="py-8 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg w-full">
        <div
        class="
            p-4 sm:p-6 text-gray-900
            w-full
            sm:min-w-[700px]
            overflow-x-auto
        "
        >
        <!-- 年度切り替え -->
        <div class="mb-4 flex flex-wrap items-center gap-2 justify-center sm:justify-start">
            <span class="font-bold text-lg">年別表示：</span>
            <button
            v-for="year in availableYears"
            :key="year"
            class="px-3 py-1 border rounded"
            :class="{ 'bg-blue-500 text-white': currentYear === year }"
            @click="changeYear(year)"
            >
            {{ year }}年
            </button>
        </div>

        <!-- 四半期ページネーション -->
        <div class="mb-4 flex items-center justify-between sm:justify-center gap-4">
            <button
                @click="prevQuarter"
                :disabled="currentQuarter === 1"
                class="px-3 py-1 border rounded disabled:opacity-50"
                >
                ◀︎
            </button>

            <span class="font-bold text-lg">{{ currentQuarterLabel }}</span>

            <button
                @click="nextQuarter"
                :disabled="currentQuarter === 4"
                class="px-3 py-1 border rounded disabled:opacity-50"
                >
                ▶︎
            </button>
        </div>

        <!-- グラフ -->
        <div class="w-full">
            <BarChart
            :year="currentYear"
            :startMonth="currentRange.start"
            :endMonth="currentRange.end"
            label="月別支出合計"
            apiUrl="/api/chart-data"
            />
        </div>
        </div>
    </div>
    </div>
</div>
</AuthenticatedLayout>
</template>
