<!-- resources/js/Components/ExpenseList.vue -->
<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import DeleteButton from '@/Components/DeleteButton.vue';
import Pagination from '@/Components/Pagination.vue';
import axios from 'axios';

const props = defineProps({
    initialExpenses: Object,
    refreshKey: Number,
});

const emit = defineEmits(['expenses-updated']);
const page = usePage();
const currentPage = ref(props.initialExpenses.current_page || 1);
const expenseList = ref(props.initialExpenses.data ?? []);
const expenses = ref(props.initialExpenses);

const reloadExpenses = async () => {
try {
    const response = await axios.get(route('expenses.latestJson', { page: currentPage.value }));
    expenseList.value = response.data.expenses.data;
    expenses.value = response.data.expenses;
    emit('expenses-updated');
} catch (e) {
    console.error('再取得エラー', e);
}
};

// フラッシュメッセージの表示
watch(
    () => page.props.flash?.message,
        (message) => {
        if (message) {
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
    }
);

watch(
    () => props.refreshKey,
    () => {
        reloadExpenses();
    }
);

onMounted(() => {
    if (page.props.flash?.message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: page.props.flash.message,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
    }
});

defineExpose({ reloadExpenses });
</script>

<template>
<div>
    <h2 class="text-lg font-bold mb-4">最近の記録</h2>
    <table class="table-auto w-full text-left">
        <thead class="bg-gray-300">
            <tr>
                <th class="px-4 py-2">金額</th>
                <th class="px-4 py-2">日付</th>
                <th class="px-4 py-2">費用名</th>
                <th class="px-4 py-2">カテゴリー</th>
                <th class="px-4 py-2">操作</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="expense in expenseList" :key="expense.id">
                <td class="border px-4 py-2">{{ expense.amount }}</td>
                <td class="border px-4 py-2">{{ expense.date }}</td>
                <td class="border px-4 py-2">{{ expense.title }}</td>
                <td class="border px-4 py-2">{{ expense.category?.name ?? '未分類' }}</td>
                <td class="border px-4 py-2">
                <div class="flex space-x-2">
                    <Link :href="route('expenses.edit', { expense: expense.id, back: 'dashboard' })" class="inline-block px-4 py-2 text-white bg-green-400 rounded hover:bg-green-500 text-sm">編集</Link>
                    <DeleteButton :expenseId="expense.id" @deleted="reloadExpenses" />
                </div>
                </td>
            </tr>
        </tbody>
    </table>
    <Pagination :links="expenses.links" />
</div>
</template>
