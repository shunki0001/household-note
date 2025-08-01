<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import DeleteButton from '@/Components/DeleteButton.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { watch, onMounted, ref, computed  } from 'vue';
import Toast from '@/Components/Toast.vue';
// import Pagination from '@/Components/Pagination.vue';
import ExpenseForm from '@/Components/ExpenseForm.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
// import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';
import ExpenseList from '@/Components/ExpenseList.vue';

const props = defineProps({
    expenses: Object,
    categories: Array,
    totalExpense: {
        type: [Number, String],
        default: 0,
    },
});

// 合計金額をリアルタイムで管理
const currentTotalExpense = ref(Number(props.totalExpense) || 0);

const formattedTotal = computed(() => {
    return currentTotalExpense.value.toLocaleString();
});

const currentPage = ref(props.expenses.current_page || 1)
const refreshKey = ref(0) // グラフ用のみに使用
const expenseListRef = ref(null)

// 一覧データを直接管理
const expenseList = ref(props.expenses?.data ?? []);

// 現在のページのpropsを取得
const page = usePage();

// 一覧データを更新する関数
const updateExpenseList = async () => {
    try {
        console.log('Dashboard: updateExpenseList called'); // デバッグログ
        const response = await axios.get(route('expenses.latestJson', { page: currentPage.value}));
        expenseList.value = response.data.expenses.data;
        console.log('Dashboard: expenseList updated with', expenseList.value.length, 'items'); // デバッグログ
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

const handleExpenseAdded = () => {
    console.log('handleExpenseAdded called'); // デバッグログ

    // 一覧データを直接更新
    updateExpenseList();

    // 合計金額を更新
    updateTotalExpense();

    // ExpenseListコンポーネントのreloadExpensesも呼び出し
    if (expenseListRef.value && typeof expenseListRef.value.reloadExpenses === 'function') {
        console.log('Calling reloadExpenses on ExpenseList'); // デバッグログ
        expenseListRef.value.reloadExpenses();
    } else {
        console.log('ExpenseList ref not available'); // デバッグログ
    }

    refreshKey.value++; // グラフ再取得
    console.log('refreshKey updated:', refreshKey.value); // デバッグログ
}

// 削除完了時の処理
const handleExpenseDeleted = () => {
    console.log('Dashboard handleExpenseDeleted called'); // デバッグログ

    // 合計金額を更新
    updateTotalExpense();

    refreshKey.value++; // グラフ再取得
    console.log('refreshKey updated after delete:', refreshKey.value); // デバッグログ
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

// グラフ設定
const monthlyChartData = ref(null);
const categoryChartData = ref(null);

const reloadDashboard = () => {
    router.visit(route('dashboard'), {
        preserveScroll: true, // スクロール位置を保持したい場合
        preserveState: true, // ページ遷移アニメーションを防ぎたい場合(任意)
    });
}

</script>

<template>
    <Head title="アカウントトップページ" />

    <AuthenticatedLayout>
        <!-- SPA構成では動作しない -->
        <Toast/>
        <!-- <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template> -->

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
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex flex-wrap lg:flex-nowrap gap-6">
                    <!-- 左：今月の家計状況 -->
                    <div class="w-full lg:w-3/5 bg-white shadow-sm sm:rounded-lg p-6 text-gray-900">
                    <!-- 今月の家計状況 -->
                    <h2 class="text-lg font-bold mb-4">今月の家計状況</h2>
                    <DoughnutChart
                        :refresh-key="refreshKey"
                    />
                    <p>今月の合計支出: {{ formattedTotal }}円</p>
                    <p>今月の合計収入: ◯◯円</p>
                    <p>収支: ◯◯円</p>
                    </div>

                    <!-- 右：かんたん入力&グラフ確認 -->
                    <div class="w-full lg:w-2/5 flex flex-col gap-6">
                        <!-- かんたん入力 -->
                        <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-900">
                            <h2 class="text-lg font-bold mb-4">かんたん入力</h2>
                            <ExpenseForm
                                :expense="{}"
                                :categories="props.categories"
                                :submitUrl="route('expenses.store')"
                                :method="'post'"
                                @expense-added="handleExpenseAdded"
                            />
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
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        <ExpenseList
                            ref="expenseListRef"
                            :initial-expenses="props.expenses"
                            :expense-list="expenseList"
                            @expense-deleted="handleExpenseDeleted"
                        />

                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
