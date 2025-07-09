<script setup>
import BarChart2 from '@/Components/BarChart2.vue';
</script>

<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
            >
                <div class="p-6 text-gray-900">
                    カテゴリー別合計グラフ
                    <BarChart2
                        label="a"
                        apiUrl="/api/chart-data/category-monthly"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
