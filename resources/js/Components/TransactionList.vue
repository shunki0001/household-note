<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import DeleteButton from '@/Components/DeleteButton.vue';
import Pagination from '@/Components/Pagination.vue';
import { onMounted, ref, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    initialTransactions: {
        type: Object,
        default: () => ({
            // data: [
            //     { id: 1, amount: 5000, date: '2025-08-01', title: '給料', category_name: '給与', type: 'income' },
            //     { id: 2, amount: 1200, date: '2025-08-02', title: '昼ごはん', category_name: '食費', type: 'expense' },
            //     { id: 3, amount: 300,  date: '2025-08-02', title: 'お菓子', category_name: '食費', type: 'expense' },
            //     { id: 4, amount: 2000, date: '2025-08-03', title: '副業報酬', category_name: '副業', type: 'income' },
            //     { id: 5, amount: 1600, date: '2025-08-03', title: '交通費', category_name: '交通', type: 'expense' },
            // ],
            data: [],
            current_page: 1,
            links: []
        })
    },
    transactionList: {
        type: Array,
        default: () => []
    },
});

const emit = defineEmits(['transaction-update', 'transaction-deleted']);
const page = usePage();
const currentPage = ref(props.initialTransactions.current_page || 1);
const localTransactionList = ref(props.initialTransactions.data ?? []);
const transactions = ref(props.initialTransactions);

// transactionList propsの変更を監視
watch(() => props.transactionList, (newTransactionList) => {
    if (newTransactionList && newTransactionList.length > 0) {
        localTransactionList.value = newTransactionList;
    }
}, { deep: true });

const reloadTransactions = async () => {
    try {
        const response = await axios.get(route('transaction.latestJson', { page: currentPage.value }));
        localTransactionList.value = response.data.transactions.data;
        transactions.value = response.data.transactions;
    } catch (e) {
        console.error('再取得エラー', e);
    }
};

const handleTransactionDeleted = () => {
    reloadTransactions();
    emit('transaction-deleted');
};

watch(() => page.props.flash?.message, (message) => {
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
});

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

defineExpose({ reloadTransactions });
</script>

<template>
    <div>
        <h2 class="text-lg font-bold mb-4">最近の収支（直近5件）</h2>
        <table class="table-auto w-full text-left">
            <thead class="bg-gray-300">
                <tr>
                    <th class="px-4 py-2">種別</th>
                    <th class="px-4 py-2">金額</th>
                    <th class="px-4 py-2">日付</th>
                    <th class="px-4 py-2">費用名</th>
                    <th class="px-4 py-2">カテゴリー</th>
                    <th class="px-4 py-2">操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="transaction in localTransactionList" :key="transaction.type + '-' + transaction.id">
                    <td class="border px-4 py-2">
                        <span :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                            {{ transaction.type === 'income' ? '収入' : '支出' }}
                        </span>
                    </td>
                    <td class="border px-4 py-2">{{ transaction.amount }}</td>
                    <td class="border px-4 py-2">{{ transaction.date }}</td>
                    <td class="border px-4 py-2">{{ transaction.title }}</td>
                    <td class="border px-4 py-2">{{ transaction.category_name ?? '未分類' }}</td>
                    <td class="border px-4 py-2">
                        <div class="flex space-x-2">
                            <Link href="#" class="inline-block px-4 py-2 text-white bg-green-400 rounded hover:bg-green-500 text-sm">編集</Link>
                            <DeleteButton :transactionId="transaction.id" @deleted="handleTransactionDeleted" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination :links="transactions.links" />
    </div>
</template>
