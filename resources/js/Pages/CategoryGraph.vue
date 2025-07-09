<script setup>
import BarChart2 from '@/Components/BarChart2.vue';
import { ref } from 'vue'

// const currentMonth = ref(1);
const currentMonth = ref(new Date().getMonth() + 1);

const changePage = (month) => {
    currentMonth.value = month;
}
</script>

<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
            >
                <div class="p-6 text-gray-900">
                    カテゴリー別合計グラフ ({{ currentMonth }}月)
                    <BarChart2
                        label="カテゴリー別"
                        apiUrl="/api/chart-data/category-monthly-single"
                        :month="currentMonth"
                    />

                    <!-- ページネーション -->
                    <div class="mt-4 flex justify-center space-x-2">
                        <button
                            v-for="month in 12"
                            :key="month"
                            class="px-3 py-1 border rounded hover:bg-blue-100"
                            :class="{
                                'bg-blue-500 text-white': currentMonth === month,
                                'bg-white text-black': currentMonth !== month
                            }"
                            @click="changePage(month)"
                        >
                            {{ month }}月
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
