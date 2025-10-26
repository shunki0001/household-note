<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    transactions: {
        type: Object,
        default: () => ({
            data: [],
            current_page: 1,
            links: []
        })
    },
    transactionList: {
        type: Array,
        default: () => []
    },
})

// フラッシュメッセージの取得
const page = usePage();

const expenses = ref([]);
const month = ref(new Date().getMonth() + 1); // JSは0始まり
const year = ref(new Date().getFullYear());
const localTransactionList = ref(props.transactions.data ?? []);

const emit = defineEmits(['transaction-update', 'transaction-deleted']);

const handleTransactionDeleted = (type) => {
    fetchTransactions();
    emit('transaction-deleted', type);
}

watch(() => props.transactionList, (newTransactionList) => {
    if (newTransactionList && newTransactionList.length > 0) {
        localTransactionList.value = newTransactionList;
    }
}, { deep: true });

const fetchTransactions = async () => {
    try {
        const response = await axios.get('/api/report-data/monthly-transactions', {
            params: {
                year: year.value,
                month: month.value,
            },
        });
        console.log('取得データ:' , response.data)
        localTransactionList.value = response.data.transactions.data ?? response.data.transactions ?? [];
    } catch (error) {
        console.error('データ取得失敗', error);
    }
};

// ページ読み込み時に実行
onMounted(() => {
    fetchTransactions();
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

    fetchTransactions();
}
</script>

<template>
    <Head title="一覧ページ" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                一覧ページ
            </h2>
        </template>
        <div class="py-6 sm:py-12 px-2 sm:px-0">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">

                    <!-- 月切り替えボタン -->
                    <div class="flex flex-row items-center justify-center space-x-3 mb-6">
                        <button
                            @click="changeMonth(-1)"
                            class="w-10 h-8 bg-gray-200 rounded hover:bg-gray-300 text-xs flex items-center justify-center"
                        >
                            ◀︎
                        </button>

                        <h2 class="text-base font-bold text-center w-60 sm:w-80 truncate">
                            {{ year }}年{{ month }}月の支出一覧
                        </h2>

                        <button
                            @click="changeMonth(1)"
                            class="w-10 h-8 bg-gray-200 rounded hover:bg-gray-300 text-xs flex items-center justify-center"
                        >
                            ▶︎
                        </button>
                    </div>


                    <!-- ✅ PC表示（表形式） -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full table-auto border border-gray-300 text-right text-sm sm:text-base">
                            <thead class="bg-gray-300">
                                <tr>
                                    <th class="border px-3 py-2 w-28 sm:w-36">種別</th>
                                    <th class="border px-3 py-2 w-28 sm:w-36">日付</th>
                                    <th class="border px-3 py-2 w-32 sm:w-40">金額</th>
                                    <th class="border px-3 py-2 w-36 sm:w-40">費用名</th>
                                    <th class="border px-3 py-2 w-32 sm:w-36">カテゴリ</th>
                                    <th class="border px-3 py-2 text-center w-28 sm:w-32">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="transaction in localTransactionList"
                                    :key="transaction.type + '-' + transaction.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="border px-3 py-2">
                                        <span :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                            {{ transaction.type === 'income' ? '収入' : '支出' }}
                                        </span>
                                    </td>
                                    <td class="border px-3 py-2">{{ transaction.date }}</td>
                                    <td class="border px-3 py-2">{{ transaction.amount ? transaction.amount.toLocaleString() : '0' }}円</td>
                                    <td class="border px-3 py-2">{{ transaction.title }}</td>
                                    <td class="border px-3 py-2">{{ transaction.category_name || '未分類' }}</td>
                                    <td class="border px-3 py-2">
                                        <div class="flex justify-end space-x-2">
                                            <Link
                                                :href="transaction.type === 'income'
                                                    ? route('incomes.edit', { id: transaction.id, back: 'list' })
                                                    : route('expenses.edit', { id: transaction.id, back: 'list' })
                                                "
                                                class="inline-block px-3 py-2 text-white bg-green-400 rounded hover:bg-green-500 text-sm text-center"
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
                            <div class="flex justify-between mb-2">
                                <span class="font-semibold text-gray-600">
                                    {{ transaction.date }}
                                </span>
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
                                <span class="font-medium">カテゴリ：</span>{{ transaction.category_name || '未分類' }}
                            </div>

                            <div class="text-gray-600 text-sm mb-3">
                                <span class="font-medium">費用名：</span>{{ transaction.title }}
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Link
                                    :href="transaction.type === 'income'
                                        ? route('incomes.edit', { id: transaction.id, back: 'list' })
                                        : route('expenses.edit', { id: transaction.id, back: 'list' })
                                    "
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

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
