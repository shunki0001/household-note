<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

// フラッシュメッセージの取得
const page = usePage();

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
onMounted(() => {
    fetchExpenses();
    const message = page.props.flash?.message;
    if(message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
    }
});

// 月変更ロジック
const changeMonth = (delta) => {
    month.value += delta;

    if(month.value > 12) {
        month.value = 1;
        year.value += 1;
    } else if (month.value < 1) {
        month.value = 12;
        year.value -= 1;
    }

    fetchExpenses();
}
</script>

<template>
    <Head title="一覧ページ" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg bg-white p-6">
                    <h2 class="text-xl font-bold mb-4">{{ year }}年{{ month }}月の支出一覧</h2>
                    <button @click="changeMonth(-1)" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">前月</button>
                    <button @click="changeMonth(1)" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">次月</button>

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
                                    <Link :href="route('expenses.edit', {expense: expense.id, back: 'list'})" class="text-blue-500 hover:underLine">編集</Link>
                                    <DeleteButton :expenseId="expense.id" @deleted="fetchExpenses"/>
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
    </AuthenticatedLayout>

</template>
