<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import { computed, onMounted, ref, onUnmounted, watch } from 'vue';
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

// 画面サイズ監視
const isMobile = ref(window.innerWidth < 768);
const handleResize = () => {
    isMobile.value = window.innerWidth < 768;
};

// mount時とunmount時にイベント設定
onMounted(() => {
    window.addEventListener('resize', handleResize);
});
onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});

const nextQuarter = () => {
    if (currentQuarter.value < 4)
        currentQuarter.value++;
    else if (currentYear.value < availableYears.value[availableYears.value.length - 1]) {
        currentYear.value++;
        currentQuarter.value = 1;
    }
};
const prevQuarter = () => {
    if (currentQuarter.value > 1)
        currentQuarter.value--;
    else if (currentYear.value > availableYears.value[0]) {
        currentYear.value--;
        currentQuarter.value = 4;
    }
};

const changeYear = (year) => {
    currentYear.value = year;
    currentQuarter.value = getQuarter(currentMonth);
};

// グラフに渡す動的な期間
const chartRange = computed(() => {
    // スマホ → 四半期ごと
    if (isMobile.value) {
        return {
            startMonth: currentRange.value.start,
            endMonth: currentRange.value.end
        };
    }
    // PC → 年間
    return {
        startMonth: 1,
        endMonth: 12
    };
});

// スマホ↔︎PC切り替え時に四半期や年を再計算
watch(isMobile, (newVal) => {
    if (!newVal) {
        // PC画面に戻った時
        currentQuarter.value = getQuarter(currentMonth);
    }
});
</script>

<template>
<Head title="月別グラフ" />

<AuthenticatedLayout>
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                月別支出グラフ
            </h2>
        </template>
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
        <!-- PC表示(年度切り替え) -->
        <div class="hidden md:block">
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

        </div>

        <!-- スマホ表示(年 + 四半期まとめ) -->
        <div class="flex justify-end w-full max-w-xl mx-auto px-4 sm:px-0 md:hidden">
            <button @click="prevQuarter" class="px-2 py-1 border rounded text-sm">◀︎</button>
            <span class="font-bold text-lg mx-auto">{{ currentYear }}年 {{ currentQuarterLabel }}</span>
            <button @click="nextQuarter" class="px-2 py-1 border rounded text-sm">▶︎</button>
        </div>

        <!-- グラフ -->
        <div class="w-full">
            <BarChart
            :key="`${isMobile}-${currentYear}-${isMobile ? currentQuarter : 'year'}`"
            :year="currentYear"
            :startMonth="chartRange.startMonth"
            :endMonth="chartRange.endMonth"
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
