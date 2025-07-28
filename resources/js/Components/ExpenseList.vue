<!-- resources/js/Components/ExpenseList.vue -->
<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import DeleteButton from '@/Components/DeleteButton.vue';
import Pagination from '@/Components/Pagination.vue';
import axios from 'axios';

const props = defineProps({
    initialExpenses: Object,
    expenseList: {
        type: Array,
        default: () => []
    },
    // refreshKey: Number, // 無限ループの原因となるため削除
});

const emit = defineEmits(['expenses-updated', 'expense-deleted']);
const page = usePage();
const currentPage = ref(props.initialExpenses.current_page || 1);
const localExpenseList = ref(props.initialExpenses.data ?? []);
const expenses = ref(props.initialExpenses);

// expenseList propsの変更を監視
watch(() => props.expenseList, (newExpenseList) => {
    console.log('ExpenseList: expenseList props changed', newExpenseList); // デバッグログ
    if (newExpenseList && newExpenseList.length > 0) {
        localExpenseList.value = newExpenseList;
        console.log('ExpenseList: localExpenseList updated with', localExpenseList.value.length, 'items'); // デバッグログ
    }
}, { deep: true });

const reloadExpenses = async () => {
try {
    console.log('ExpenseList reloadExpenses called'); // デバッグログ
    console.log('Current page:', currentPage.value); // デバッグログ

    const response = await axios.get(route('expenses.latestJson', { page: currentPage.value }));
    console.log('API response received:', response.data); // デバッグログ

    // データを更新
    localExpenseList.value = response.data.expenses.data;
    expenses.value = response.data.expenses;

    console.log('ExpenseList updated with', localExpenseList.value.length, 'items'); // デバッグログ
    console.log('Updated expenseList:', localExpenseList.value); // デバッグログ

    // emit('expenses-updated'); // 無限ループの原因となるため削除
    // emit('expense-deleted');
} catch (e) {
    console.error('再取得エラー', e);
}
};

// 削除完了時の処理
const handleExpenseDeleted = () => {
    console.log('ExpenseList handleExpenseDeleted called'); // デバッグログ
    reloadExpenses();
    emit('expense-deleted'); // 親コンポーネントに削除完了を通知
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

// refreshKeyの変更監視を削除（無限ループの原因）
// watch(
//     () => props.refreshKey,
//     () => {
//         reloadExpenses();
//     }
// );

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
            <tr v-for="expense in localExpenseList" :key="expense.id">
                <td class="border px-4 py-2">{{ expense.amount }}</td>
                <td class="border px-4 py-2">{{ expense.date }}</td>
                <td class="border px-4 py-2">{{ expense.title }}</td>
                <td class="border px-4 py-2">{{ expense.category?.name ?? '未分類' }}</td>
                <td class="border px-4 py-2">
                <div class="flex space-x-2">
                    <Link :href="route('expenses.edit', { expense: expense.id, back: 'dashboard' })" class="inline-block px-4 py-2 text-white bg-green-400 rounded hover:bg-green-500 text-sm">編集</Link>
                    <DeleteButton :expenseId="expense.id" @deleted="handleExpenseDeleted" />
                </div>
                </td>
            </tr>
        </tbody>
    </table>
    <Pagination :links="expenses.links" />
</div>
</template>
