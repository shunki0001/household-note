<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BarChart2 from '@/Components/BarChart2.vue';
import { Head } from '@inertiajs/vue3';
import { useYearMonth } from '@/composables/useYearMonth';

const {
    currentYear,
    currentMonth,
    availableYears,
    changeYear,
    changeMonths,
    nextMonth,
    prevMonth,
} = useYearMonth();
</script>

<template>
    <Head title="カテゴリー別グラフ" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                カテゴリー別グラフ
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">

                        <!-- ページネーション -->

                        <!-- PC表示 -->
                        <div class="hidden md:block">
                            <div class="mt-4 flex justify-center space-x-2">
                                <button
                                    v-for="year in availableYears"
                                    :key="year"
                                    class="mx-1 px-3 py-1 border rounded hover:bg-blue-100"
                                    :class="{
                                        'bg-blue-500 text-white': currentYear === year ,
                                        'bg-white text-black': currentYear !== year
                                    }"
                                    @click="changeYear(year)"
                                >
                                    {{ year }}年
                                </button>
                            </div>
                            <div class="mt-4 flex justify-center space-x-2">
                                <button
                                    v-for="month in 12"
                                    :key="month"
                                    class="px-3 py-1 border rounded hover:bg-blue-100"
                                    :class="{
                                        'bg-blue-500 text-white': currentMonth === month,
                                        'bg-white text-black': currentMonth !== month
                                    }"
                                    @click="changeMonths(month)"
                                >
                                    {{ month }}月
                                </button>
                            </div>
                        </div>

                        <!-- スマホ表示 -->
                        <div class="flex md:hidden items-center justify-between mb-4">
                            <button @click="prevMonth" class="px-2 py-1 border rounded text-sm">◀︎</button>
                            <span class="font-bold text-lg">{{ currentYear }}年 {{ currentMonth }}月</span>
                            <button @click="nextMonth" class="px-2 py-1 border rounded text-sm">▶︎</button>
                        </div>

                        <!-- <h2>{{ currentMonth }}月のカテゴリー別</h2> -->
                        <BarChart2
                            label="カテゴリー別"
                            apiUrl="/api/chart-data/category-monthly-single"
                            :month="currentMonth"
                            :year="currentYear"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
