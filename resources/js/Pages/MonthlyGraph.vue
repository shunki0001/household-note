<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import { computed, ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import { YEAR_START_MONTH, YEAR_END_MONTH } from '@/config/constants';
import { useQuarter } from '@/composables/useQuarter';
import { useResponsive } from '@/composables/useResponsive';

// =============================
// 動的データ
// =============================
const availableYears = ref([2023, 2024, 2025, 2026, 2027]);

const {
    currentYear,
    currentQuarter,
    currentQuarterLabel,
    currentRange,
    nextQuarter,
    prevQuarter,
    changeYear,
    resetQuarterToCurrentMonth,
} = useQuarter(availableYears);
const { isMobile } = useResponsive();

// =============================
// グラフ期間
// =============================
const chartRange = computed(() => {
    // スマホ → 四半期ごと
    if (isMobile.value) {
        return {
            startMonth: currentRange.value.start,
            endMonth: currentRange.value.end,
        };
    }
    // PC → 年間
    return {
        startMonth: YEAR_START_MONTH,
        endMonth: YEAR_END_MONTH,
    };
});

// スマホ↔︎PC切り替え時に四半期や年を再計算
watch(isMobile, (newVal) => {
    if (!newVal) {
        // PC画面に戻った時
        resetQuarterToCurrentMonth();
    }
});
</script>

<template>
    <Head title="月別グラフ" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                月別支出グラフ
            </h2>
        </template>
        <div class="py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="w-full rounded-lg bg-white shadow-sm">
                    <div
                        class="w-full overflow-x-auto p-4 text-gray-900 sm:min-w-[700px] sm:p-6"
                    >
                        <!-- PC表示(年度切り替え) -->
                        <div class="hidden md:block">
                            <div
                                class="mb-4 flex flex-wrap items-center justify-center gap-2 sm:justify-start"
                            >
                                <span class="text-lg font-bold"
                                    >年別表示：</span
                                >
                                <button
                                    v-for="year in availableYears"
                                    :key="year"
                                    class="rounded border px-3 py-1"
                                    :class="{
                                        'bg-blue-500 text-white':
                                            currentYear === year,
                                    }"
                                    @click="changeYear(year)"
                                >
                                    {{ year }}年
                                </button>
                            </div>
                        </div>

                        <!-- スマホ表示(年 + 四半期まとめ) -->
                        <div
                            class="mx-auto flex w-full max-w-xl justify-end px-4 sm:px-0 md:hidden"
                        >
                            <button
                                class="rounded border px-2 py-1 text-sm"
                                @click="prevQuarter"
                            >
                                ◀︎
                            </button>
                            <span class="mx-auto text-lg font-bold"
                                >{{ currentYear }}年
                                {{ currentQuarterLabel }}</span
                            >
                            <button
                                class="rounded border px-2 py-1 text-sm"
                                @click="nextQuarter"
                            >
                                ▶︎
                            </button>
                        </div>

                        <!-- グラフ -->
                        <div class="w-full">
                            <BarChart
                                :key="`${isMobile}-${currentYear}-${isMobile ? currentQuarter : 'year'}`"
                                :year="currentYear"
                                :start-month="chartRange.startMonth"
                                :end-month="chartRange.endMonth"
                                label="月別支出合計"
                                api-url="/api/chart-data"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
