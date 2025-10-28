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

const handleTransactionDeleted = (type) => {
    reloadTransactions();
    emit('transaction-deleted', type);
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
        <h2 class="text-lg sm:text-xl font-bold mb-4 text-center sm:text-left">最近の収支（直近5件）</h2>

        <!-- ✅ PC表示（表形式） -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full table-auto text-left border-collapse">
                <thead class="bg-gray-300 text-gray-700">
                    <tr>
                        <th class="px-3 py-2 whitespace-nowrap text-sm">種別</th>
                        <th class="px-3 py-2 whitespace-nowrap text-sm">金額</th>
                        <th class="px-3 py-2 whitespace-nowrap text-sm">日付</th>
                        <th class="px-3 py-2 whitespace-nowrap text-sm">費用名</th>
                        <th class="px-3 py-2 whitespace-nowrap text-sm">カテゴリー</th>
                        <th class="px-3 py-2 whitespace-nowrap text-sm">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="transaction in localTransactionList"
                        :key="transaction.type + '-' + transaction.id"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                {{ transaction.type === 'income' ? '収入' : '支出' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            {{ transaction.amount ? transaction.amount.toLocaleString() : '0' }}円
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ transaction.date }}</td>
                        <td class="px-3 py-2 truncate max-w-[120px]" :title="transaction.title">{{ transaction.title }}</td>
                        <td class="px-3 py-2 truncate max-w-[100px]" :title="transaction.category_name">
                            {{ transaction.category_name ?? '未分類' }}
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex flex-wrap gap-2">
                                <Link
                                    :href="transaction.type === 'income'
                                        ? route('incomes.edit', { id: transaction.id, back: 'dashboard' })
                                        : route('expenses.edit', { id: transaction.id, back: 'dashboard'})"
                                    class="inline-flex items-center justify-center px-3 py-1 text-white bg-green-400 rounded hover:bg-green-500 text-sm"
                                >
                                    編集
                                </Link>
                                <DeleteButton
                                    :transactionId="transaction.id"
                                    :transactionType="transaction.type"
                                    @deleted="() => handleTransactionDeleted(transaction.type)"
                                />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ✅ スマホ表示（カード型） -->
        <div class="sm:hidden space-y-4">
            <div
                v-for="transaction in localTransactionList"
                :key="transaction.type + '-' + transaction.id"
                class="border rounded-lg shadow-sm p-4 bg-white"
            >
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 text-sm">{{ transaction.date }}</span>
                    <span
                        :class="transaction.type === 'income' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'"
                    >
                        {{ transaction.type === 'income' ? '収入' : '支出' }}
                    </span>
                </div>

                <div class="mb-2">
                    <span class="text-gray-500 text-sm">金額：</span>
                    <span class="text-lg font-bold">
                        {{ transaction.amount ? transaction.amount.toLocaleString() : '0' }}円
                    </span>
                </div>

                <div class="text-gray-600 text-sm mb-1">
                    <span class="font-medium">費用名：</span>{{ transaction.title }}
                </div>

                <div class="text-gray-600 text-sm mb-2">
                    <span class="font-medium">カテゴリー：</span>{{ transaction.category_name ?? '未分類' }}
                </div>

                <div class="flex justify-end space-x-2 mt-2">
                    <Link
                        :href="transaction.type === 'income'
                            ? route('incomes.edit', { id: transaction.id, back: 'dashboard' })
                            : route('expenses.edit', { id: transaction.id, back: 'dashboard'})"
                        class="inline-block px-3 py-2 text-white bg-green-400 rounded hover:bg-green-500 text-sm"
                    >
                        編集
                    </Link>

                    <DeleteButton
                        :transactionId="transaction.id"
                        :transactionType="transaction.type"
                        @deleted="() => handleTransactionDeleted(transaction.type)"
                    />
                </div>
            </div>

            <div v-if="localTransactionList.length === 0" class="text-center py-4 text-gray-500">
                データがありません
            </div>
        </div>

        <!-- ページネーション -->
        <div class="mt-4">
            <Pagination :links="transactions.links" />
        </div>
    </div>
</template>
