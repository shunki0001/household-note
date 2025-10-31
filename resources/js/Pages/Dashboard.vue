<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { watch, onMounted, ref, computed  } from 'vue';
import Toast from '@/Components/Toast.vue';
import ExpenseForm from '@/Components/ExpenseForm.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
import TransactionList from '@/Components/TransactionList.vue';
import IncomeForm from '@/Components/IncomeForm.vue';

const props = defineProps({
    expenses: Object,
    categories: Array,
    income_categories: Array,
    totalExpense: {
        type: [Number, String],
        default: 0,
    },
    totalIncome: {
        type: [Number, String],
        default: 0,
    },
    transactions: Object, // ページネーション形式
    latestTransactions: Array, // 配列形式
});

// フォーム切り替え用の状態
const activeForm = ref('expense'); // 'expense' または 'income'

// フォーム切り替え関数
const switchToExpense = () => {
    activeForm.value = 'expense';
};

const switchToIncome = () => {
    activeForm.value = 'income';
};

// 収支の算出
const currentBalance = computed(() => {
    return currentTotalIncome.value - currentTotalExpense.value;
});

// 合計金額をリアルタイムで管理
const currentTotalExpense = ref(Number(props.totalExpense) || 0);
const currentTotalIncome = ref(Number(props.totalIncome) || 0);

const formattedTotalExpense = computed(() => {
    return currentTotalExpense.value.toLocaleString();
});

const formattedTotalIncome = computed(() => {
    return currentTotalIncome.value.toLocaleString();
});

const formattedBalance = computed(() => {
    return currentBalance.value.toLocaleString();
});

// const currentPage = ref(props.expenses.current_page || 1)
const currentPage = ref(props.transactions || 1)
const refreshKey = ref(0) // グラフ用のみに使用
const transactionListRef = ref(null)
const transactions = ref([])

// 一覧データを直接管理
// props からローカル state にコピー
const transactionList = ref(props.transactions?.data ?? []);

// 現在のページのpropsを取得
const page = usePage();
const latestTransactions = page.props.latestTransactions;

// 一覧データを更新する関数(収入 + 支出)
const updateTransactionList = async () => {
    try {
        console.log('Dashboard: updateTransactionList called'); // デバック
        const response = await axios.get(route('transaction.latestJson', { page: currentPage.value}));
        transactionList.value = response.data.transactions.data;
        console.log('Dashboard: transactionList update with', transactionList.value.length, 'items'); // デバック
    } catch (e) {
        console.error('Dashboard: 一覧更新エラー', e);
    }
}

// 合計金額を更新する関数
const updateTotalExpense = async () => {
    try {
        console.log('Dashboard: updateTotalExpense called'); // デバッグログ
        const response = await axios.get(route('dashboard.totalExpense'));
        currentTotalExpense.value = Number(response.data.totalExpense) || 0;
        console.log('Dashboard: totalExpense updated to', currentTotalExpense.value); // デバッグログ
    } catch (e) {
        console.error('Dashboard: 合計金額更新エラー', e);
    }
}

// 合計収入金額を更新する関数
const updateTotalIncome = async () => {
    try {
        console.log('Dashboard: updateTotalIncome called'); // デバックログ
        const response = await axios.get(route('dashboard.totalIncome'));
        currentTotalIncome.value = Number(response.data.totalIncome) || 0;
        console.log('Dashboard: totalIncome updated to', currentTotalIncome.value); // デバックログ
    } catch (e) {
        console.error('Dashboard: 合計収入金額更新エラー', e);
    }
}

const handleExpenseAdded = async () => {
    console.log('handleExpenseAdded called'); // デバッグログ

    // 合計支出更新 -> グラフ更新 -> 一覧更新
    await updateTotalExpense();
    await updateTransactionList();
    refreshKey.value++;
}

const handleIncomeAdded = async () => {
    console.log('handleIncomeAdded called'); // デバックログ

    // 合計支出更新 -> グラフ更新 -> 一覧更新
    await updateTotalIncome();
    await updateTransactionList();
    refreshKey.value++;
}

const fetchTransactions = async () => {
    const res = await axios.get(route('transaction.latestJson', { page: currentPage.value}));
    transactionList.value = res.data.transactions.data;
}

onMounted(fetchTransactions);

// 削除完了時の処理
const handleTransactionDeleted = async (type) => {
    console.log('Dashboard handleTransactionDeleted called for', type);

    if(type === 'income') {
        await updateTotalIncome();
    } else if (type === 'expense') {
        await updateTotalExpense();
    }

    await updateTransactionList();
    refreshKey.value++;
}

// フラッシュメッセージの監視
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

// onMountedを追加（ページ遷移時に即チェック）
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
        <Toast/>
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
                                        @submitted="handleExpenseAdded"
                                    />
                                </div>
                                <div v-else-if="activeForm === 'income'">
                                    <IncomeForm
                                        :income="{}"
                                        :income_categories="props.income_categories"
                                        :submitUrl="route('incomes.store')"
                                        :method="'post'"
                                        @submitted="handleIncomeAdded"
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
