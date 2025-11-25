<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { watch } from 'vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { DEFAULT_CURRENT_PAGE } from '@/config/constants';
import { useTransactions } from '@/composables/useTransactions';

const props = defineProps({
    transactions: {
        type: Object,
        default: () => ({
            data: [],
            current_page: DEFAULT_CURRENT_PAGE,
            links: [],
        }),
    },
    transactionList: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['transaction-update', 'transaction-deleted']);

const {
    transactions,
    month,
    year,
    changeMonth,
    fetchTransactions,
    updateTransactions,
} = useTransactions(props.transactions.data ?? []);

// 削除処理
const handleTransactionDeleted = (type) => {
    fetchTransactions();
    emit('transaction-deleted', type);
};

watch(
    () => props.transactionList,
    (newList) => updateTransactions(newList),
);
</script>

<template>
    <Head title="一覧ページ" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                一覧ページ
            </h2>
        </template>
        <div class="px-2 py-6 sm:px-0 sm:py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white p-4 shadow-sm sm:rounded-lg sm:p-6"
                >
                    <!-- 月切り替えボタン -->
                    <div
                        class="mb-6 flex flex-row items-center justify-center space-x-3"
                    >
                        <button
                            class="flex h-8 w-10 items-center justify-center rounded bg-gray-200 text-xs hover:bg-gray-300"
                            @click="changeMonth(-1)"
                        >
                            ◀︎
                        </button>

                        <h2
                            class="w-60 truncate text-center text-base font-bold sm:w-80"
                        >
                            {{ year }}年{{ month }}月の支出一覧
                        </h2>

                        <button
                            class="flex h-8 w-10 items-center justify-center rounded bg-gray-200 text-xs hover:bg-gray-300"
                            @click="changeMonth(1)"
                        >
                            ▶︎
                        </button>
                    </div>

                    <!-- ✅ PC表示（表形式） -->
                    <div class="hidden overflow-x-auto sm:block">
                        <table
                            class="min-w-full table-auto border border-gray-300 text-right text-sm sm:text-base"
                        >
                            <thead class="bg-gray-300">
                                <tr>
                                    <th class="w-28 border px-3 py-2 sm:w-36">
                                        種別
                                    </th>
                                    <th class="w-28 border px-3 py-2 sm:w-36">
                                        日付
                                    </th>
                                    <th class="w-32 border px-3 py-2 sm:w-40">
                                        金額
                                    </th>
                                    <th class="w-36 border px-3 py-2 sm:w-40">
                                        費用名
                                    </th>
                                    <th class="w-32 border px-3 py-2 sm:w-36">
                                        カテゴリ
                                    </th>
                                    <th
                                        class="w-28 border px-3 py-2 text-center sm:w-32"
                                    >
                                        操作
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="transaction in transactions"
                                    :key="
                                        transaction.type + '-' + transaction.id
                                    "
                                    class="hover:bg-gray-50"
                                >
                                    <td class="border px-3 py-2">
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
                                    <td class="border px-3 py-2">
                                        {{ transaction.date }}
                                    </td>
                                    <td class="border px-3 py-2">
                                        {{
                                            transaction.amount
                                                ? transaction.amount.toLocaleString()
                                                : '0'
                                        }}円
                                    </td>
                                    <td class="border px-3 py-2">
                                        {{ transaction.title }}
                                    </td>
                                    <td class="border px-3 py-2">
                                        {{
                                            transaction.category_name ||
                                            '未分類'
                                        }}
                                    </td>
                                    <td class="border px-3 py-2">
                                        <div class="flex justify-end space-x-2">
                                            <Link
                                                :href="
                                                    transaction.type ===
                                                    'income'
                                                        ? route(
                                                              'incomes.edit',
                                                              {
                                                                  id: transaction.id,
                                                                  back: 'list',
                                                              },
                                                          )
                                                        : route(
                                                              'expenses.edit',
                                                              {
                                                                  id: transaction.id,
                                                                  back: 'list',
                                                              },
                                                          )
                                                "
                                                class="inline-block rounded bg-green-400 px-3 py-2 text-center text-sm text-white hover:bg-green-500"
                                            >
                                                編集
                                            </Link>

                                            <DeleteButton
                                                :transaction-id="transaction.id"
                                                :transaction-type="
                                                    transaction.type
                                                "
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
                            v-for="transaction in transactions"
                            :key="transaction.type + '-' + transaction.id"
                            class="rounded-lg border bg-white p-4 shadow-sm"
                        >
                            <div class="mb-2 flex justify-between">
                                <span class="font-semibold text-gray-600">
                                    {{ transaction.date }}
                                </span>
                                <span
                                    :class="
                                        transaction.type === 'income'
                                            ? 'font-semibold text-green-600'
                                            : 'font-semibold text-red-600'
                                    "
                                >
                                    {{
                                        transaction.type === 'income'
                                            ? '収入'
                                            : '支出'
                                    }}
                                </span>
                            </div>

                            <div class="mb-2">
                                <span class="text-sm text-gray-500"
                                    >金額：</span
                                >
                                <span class="text-lg font-bold">
                                    {{
                                        transaction.amount
                                            ? transaction.amount.toLocaleString()
                                            : '0'
                                    }}円
                                </span>
                            </div>

                            <div class="mb-1 text-sm text-gray-600">
                                <span class="font-medium">カテゴリ：</span
                                >{{ transaction.category_name || '未分類' }}
                            </div>

                            <div class="mb-3 text-sm text-gray-600">
                                <span class="font-medium">費用名：</span
                                >{{ transaction.title }}
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Link
                                    :href="
                                        transaction.type === 'income'
                                            ? route('incomes.edit', {
                                                  id: transaction.id,
                                                  back: 'list',
                                              })
                                            : route('expenses.edit', {
                                                  id: transaction.id,
                                                  back: 'list',
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
                                        () =>
                                            handleTransactionDeleted(
                                                transaction.type,
                                            )
                                    "
                                />
                            </div>
                        </div>

                        <div
                            v-if="transactions.length === 0"
                            class="py-4 text-center text-gray-500"
                        >
                            データがありません
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
