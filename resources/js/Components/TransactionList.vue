<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import DeleteButton from '@/Components/DeleteButton.vue';
// import Pagination from '@/Components/Pagination.vue';
import { onMounted, ref, watch } from 'vue';
import axios from 'axios';
import { showAlert } from '@/utils/alert';

const props = defineProps({
    initialTransactions: {
        type: Object,
        default: () => ({
            data: [],
            current_page: 1,
            links: [],
        }),
    },
    transactionList: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['transaction-update', 'transaction-deleted']);
const page = usePage();
const currentPage = ref(props.initialTransactions.current_page || 1);
const localTransactionList = ref(props.initialTransactions.data ?? []);
const transactions = ref(props.initialTransactions);

// transactionList propsの変更を監視
watch(
    () => props.transactionList,
    (newTransactionList) => {
        if (newTransactionList && newTransactionList.length > 0) {
            localTransactionList.value = newTransactionList;
        }
    },
    { deep: true },
);

const reloadTransactions = async () => {
    try {
        const response = await axios.get(
            route('transaction.latestJson', { page: currentPage.value }),
        );
        localTransactionList.value = response.data.transactions.data;
        transactions.value = response.data.transactions;
    } catch (e) {
        if (process.env.NODE_ENV !== 'production') {
            console.error('再取得エラー', e);
        }
    }
};

const handleTransactionDeleted = (type) => {
    reloadTransactions();
    emit('transaction-deleted', type);
};

watch(
    () => page.props.flash?.message,
    (message) => {
        if (message) {
            showAlert(message, 'success');
        }
    },
);

onMounted(() => {
    if (page.props.flash?.message) {
        showAlert(page.props.flash.message, 'success');
    }
});

defineExpose({ reloadTransactions });
</script>

<template>
    <div>
        <h2 class="mb-4 text-center text-lg font-bold sm:text-left sm:text-xl">
            最近の収支（直近5件）
        </h2>

        <!-- ✅ PC表示（表形式） -->
        <div class="hidden overflow-x-auto sm:block">
            <table class="min-w-full table-auto border-collapse text-left">
                <thead class="bg-gray-300 text-gray-700">
                    <tr>
                        <th class="whitespace-nowrap px-3 py-2 text-sm">
                            種別
                        </th>
                        <th class="whitespace-nowrap px-3 py-2 text-sm">
                            金額
                        </th>
                        <th class="whitespace-nowrap px-3 py-2 text-sm">
                            日付
                        </th>
                        <th class="whitespace-nowrap px-3 py-2 text-sm">
                            費用名
                        </th>
                        <th class="whitespace-nowrap px-3 py-2 text-sm">
                            カテゴリー
                        </th>
                        <th class="whitespace-nowrap px-3 py-2 text-sm">
                            操作
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="transaction in localTransactionList"
                        :key="transaction.type + '-' + transaction.id"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="whitespace-nowrap px-3 py-2">
                            <span
                                :class="
                                    transaction.type === 'income'
                                        ? 'text-green-600'
                                        : 'text-red-600'
                                "
                            >
                                {{
                                    transaction.type === 'income'
                                        ? '収入'
                                        : '支出'
                                }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-2">
                            {{
                                transaction.amount
                                    ? transaction.amount.toLocaleString()
                                    : '0'
                            }}円
                        </td>
                        <td class="whitespace-nowrap px-3 py-2">
                            {{ transaction.date }}
                        </td>
                        <td
                            class="max-w-[120px] truncate px-3 py-2"
                            :title="transaction.title"
                        >
                            {{ transaction.title }}
                        </td>
                        <td
                            class="max-w-[100px] truncate px-3 py-2"
                            :title="transaction.category_name"
                        >
                            {{ transaction.category_name ?? '未分類' }}
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex flex-wrap gap-2">
                                <Link
                                    :href="
                                        transaction.type === 'income'
                                            ? route('incomes.edit', {
                                                    id: transaction.id,
                                                    back: 'dashboard',
                                                })
                                            : route('expenses.edit', {
                                                    id: transaction.id,
                                                    back: 'dashboard',
                                                })
                                    "
                                    class="inline-flex items-center justify-center rounded bg-green-400 px-3 py-1 text-sm text-white hover:bg-green-500"
                                >
                                    編集
                                </Link>
                                <DeleteButton
                                    :transaction-id="transaction.id"
                                    :transaction-type="transaction.type"
                                    @deleted="
                                        () =>
                                            handleTransactionDeleted(
                                                transaction.type,
                                            )
                                    "
                                />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ✅ スマホ表示（カード型） -->
        <div class="space-y-4 sm:hidden">
            <div
                v-for="transaction in localTransactionList"
                :key="transaction.type + '-' + transaction.id"
                class="rounded-lg border bg-white p-4 shadow-sm"
            >
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{
                        transaction.date
                    }}</span>
                    <span
                        :class="
                            transaction.type === 'income'
                                ? 'font-semibold text-green-600'
                                : 'font-semibold text-red-600'
                        "
                    >
                        {{ transaction.type === 'income' ? '収入' : '支出' }}
                    </span>
                </div>

                <div class="mb-2">
                    <span class="text-sm text-gray-500">金額：</span>
                    <span class="text-lg font-bold">
                        {{
                            transaction.amount
                                ? transaction.amount.toLocaleString()
                                : '0'
                        }}円
                    </span>
                </div>

                <div class="mb-1 text-sm text-gray-600">
                    <span class="font-medium">費用名：</span
                    >{{ transaction.title }}
                </div>

                <div class="mb-2 text-sm text-gray-600">
                    <span class="font-medium">カテゴリー：</span
                    >{{ transaction.category_name ?? '未分類' }}
                </div>

                <div class="mt-2 flex justify-end space-x-2">
                    <Link
                        :href="
                            transaction.type === 'income'
                                ? route('incomes.edit', {
                                    id: transaction.id,
                                    back: 'dashboard',
                                })
                                : route('expenses.edit', {
                                        id: transaction.id,
                                        back: 'dashboard',
                                    })
                        "
                        class="inline-block rounded bg-green-400 px-3 py-2 text-sm text-white hover:bg-green-500"
                    >
                        編集
                    </Link>

                    <DeleteButton
                        :transaction-id="transaction.id"
                        :transaction-type="transaction.type"
                        @deleted="
                            () => handleTransactionDeleted(transaction.type)
                        "
                    />
                </div>
            </div>

            <div
                v-if="localTransactionList.length === 0"
                class="py-4 text-center text-gray-500"
            >
                データがありません
            </div>
        </div>

        <!-- ページネーション -->
        <!-- <div class="mt-4">
            <Pagination :links="transactions.links" />
        </div> -->
    </div>
</template>
