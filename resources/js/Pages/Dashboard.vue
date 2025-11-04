<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { watch, onMounted, ref, computed  } from 'vue';
import ExpenseForm from '@/Components/ExpenseForm.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
import TransactionList from '@/Components/TransactionList.vue';
import IncomeForm from '@/Components/IncomeForm.vue';
import { showAlert } from '@/utils/alert';
import { useFinance } from '@/composables/useFinance';
import { fetchTransactions } from '@/services/transactionService';

const props = defineProps({
    totalExpense: Number,
    totalIncome: Number,
    transactions: Object,
    categories: Array,
    income_categories: Array,
});

const {
    totalExpense: currentTotalExpense,
    totalIncome: currentTotalIncome,
    formattedExpense,
    formattedIncome,
    formattedBalance,
    updateExpense,
    updateIncome,
} = useFinance(props.totalExpense, props.totalIncome);

const currentPage = ref(1);
const transactionList = ref(props.transactions?.data ?? []); // 一覧データを直接管理
const refreshKey = ref(0) // グラフ用のみに使用
const page = usePage(); // 現在のページのpropsを取得

// フォーム切り替え用の状態
const activeForm = ref('expense'); // 'expense' または 'income'
// フォーム切り替え関数
const switchToExpense = () => {
    activeForm.value = 'expense';
};
const switchToIncome = () => {
    activeForm.value = 'income';
};

const formattedTotalExpense = computed(() => {
    return currentTotalExpense.value.toLocaleString();
});

const formattedTotalIncome = computed(() => {
    return currentTotalIncome.value.toLocaleString();
});

const transactionListRef = ref(null)
const transactions = ref([])

const updateTransactionList = async () => {
    transactionList.value = await fetchTransactions(currentPage.value);
};

const handleTransactionAdded = async (type) => {
    if (type === 'income') {
        await updateIncome();
    } else {
        await updateExpense();
    }

    await updateTransactionList();
    refreshKey.value++;
};

const handleTransactionDeleted = async (type) => {
    if (type === 'income') {
        await updateIncome();
    } else if (type === 'expense') {
        await updateExpense();
    }

    await updateTransactionList();
    refreshKey.value++;
};

// フラッシュメッセージの監視
watch(
    () => page.props.flash?.message,
    (message) => {
        if (message) {
            showAlert(message, 'success');
        }
    }
);

onMounted(updateTransactionList);

</script>

<template>
    <Head title="マイページ" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                マイページ
            </h2>
        </template>
        <!-- <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        ここにCRUD処理を入力
                    </div>
                </div>
            </div>
        </div> -->

        <!-- ドーナツグラフ&今月の収支状況 -->
        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-wrap lg:flex-nowrap gap-6">
                    <!-- 左：今月の家計状況 -->
                    <div class="w-full lg:w-3/5 bg-white shadow-sm sm:rounded-lg p-6 text-gray-900">
                        <h2 class="text-xl font-bold mb-6 text-center">今月の家計状況</h2>

                        <DoughnutChart :refresh-key="refreshKey" class="mb-6" />

                        <div class="space-y-2 text-center">
                            <p class="text-lg font-semibold">
                                今月の合計支出：
                                <span class="text-red-500 text-xl font-bold">{{ formattedTotalExpense }}円</span>
                            </p>

                            <p class="text-lg font-semibold">
                                今月の合計収入：
                                <span class="text-green-500 text-xl font-bold">{{ formattedTotalIncome }}円</span>
                            </p>

                            <p
                                class="text-2xl font-extrabold mt-4"
                                :class="parseInt(formattedBalance.replace(/,/g, '')) < 0 ? 'text-red-600' : 'text-green-600'"
                            >
                                収支：{{ formattedBalance }}円
                            </p>
                        </div>
                    </div>


                    <!-- 右：かんたん入力&グラフ確認 -->
                    <div class="w-full lg:w-2/5 flex flex-col gap-6">
                        <!-- かんたん入力 -->
                        <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-900">
                            <h2 class="text-lg font-bold mb-4">かんたん入力</h2>

                            <!-- フォーム切り替えボタン -->
                            <div class="flex mb-4 border rounded-lg overflow-hidden">
                                <button
                                    @click="switchToExpense"
                                    :class="[
                                        'flex-1 px-4 py-2 text-sm font-medium transition-colors',
                                        activeForm === 'expense'
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                >
                                    支出
                                </button>
                                <button
                                    @click="switchToIncome"
                                    :class="[
                                        'flex-1 px-4 py-2 text-sm font-medium transition-colors',
                                        activeForm === 'income'
                                            ? 'bg-green-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                >
                                    収入
                                </button>
                            </div>

                            <!-- フォーム表示エリア -->
                            <div class="min-h-[400px]">
                                <div v-if="activeForm === 'expense'">
                                    <ExpenseForm
                                        :expense="{}"
                                        :categories="props.categories"
                                        :submitUrl="route('expenses.store')"
                                        :method="'post'"
                                        @submitted="handleTransactionAdded"
                                    />
                                </div>
                                <div v-else-if="activeForm === 'income'">
                                    <IncomeForm
                                        :income="{}"
                                        :income_categories="props.income_categories"
                                        :submitUrl="route('incomes.store')"
                                        :method="'post'"
                                        @submitted="handleTransactionAdded"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- 履歴・グラフへのリンク -->
                        <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-900">
                            <h2 class="text-lg font-bold mb-4">履歴、グラフで確認</h2>
                            <div class="space-y-2">
                                <Link href="/list" class="btn-icon-text">一覧ページに移動</Link>
                                <Link href="/graph/monthly" class="btn-icon-text">月別支出グラフ</Link>
                                <Link href="/graph/category" class="btn-icon-text">カテゴリー別支出グラフ</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 最近の記録 -->
        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <TransactionList
                            :initial-transactions="props.transactions"
                            :transaction-list="transactionList"
                            @transaction-deleted="handleTransactionDeleted"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
