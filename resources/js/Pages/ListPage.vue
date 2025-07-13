<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import DeleteButton from '@/Components/DeleteButton.vue';

const expenses = ref([]);
const month = ref(new Date().getMonth() + 1); // JSは0始まり
const year = ref(new Date().getFullYear());

const fetchExpenses = async () => {
    try {
        const response =await axios.get('/api/chart-data/monthly-expenses', {
            params: {
                year: year.value,
                month: month.value,
            },
        });
        expenses.value = response.data;
    } catch (error) {
        console.log('データ取得失敗', error);
    }
};

// ページ読み込み時に実行
onMounted(fetchExpenses);
</script>

<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg bg-white p-6">
                <h2 class="text-xl font-bold mb-4">{{ year }}年{{ month }}月の支出一覧</h2>

                <table class="min-w-full table-auto border border-gray-300 text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">日付</th>
                            <th class="border px-4 py-2">金額</th>
                            <th class="border px-4 py-2">カテゴリ</th>
                            <th class="border px-4 py-2">費用名</th>
                            <th class="border px-4 py-2">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="expense in expenses" :key="expense.id">
                            <td class="border px-4 py-2">{{ expense.date }}</td>
                            <td class="border px-4 py-2">{{ expense.amount.toLocaleString() }}円</td>
                            <td class="border px-4 py-2">{{ expense.category?.name || '未分類' }}</td>
                            <td class="border px-4 py-2">{{ expense.title }}</td>

                            <td class="border px-4 py-2 space-x-2">
                                <button class="text-blue-500 hover:underline">編集</button>
                                <DeleteButton :expenseId="expense.id" @deleted="fetchExpenses"/>
                                <!-- <button class="text-red-500 hover:underline">削除</button> -->
                            </td>
                        </tr>
                        <tr v-if="expenses.length === 0">
                            <td colspan="5" class="text-center py-4 text-gray-500">データがありません</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
