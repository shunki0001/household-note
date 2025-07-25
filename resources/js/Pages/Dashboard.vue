<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import DeleteButton from '@/Components/DeleteButton.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
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

const formattedTotal = computed(() => {
    return Number(props.totalExpense).toLocaleString();
});

const currentPage = ref(props.expenses.current_page || 1)
const refreshKey = ref(0)
const expenseListRef = ref(null)

const form = useForm({
    amount: '',
    date: '',
    title: '',
    category_id: '',
});

const submit = () => {
    form.post(route('expenses.store'), {
        onSuccess: () => {
            form.reset();
            refreshKey.value++;
        },
    });
}

// // 現在のページのpropsを取得
const page = usePage();

const expenseList = ref(props.expenses?.data ?? []);

const reloadExpenses = async () => {
    try {
        // const response = await axios.get(route('expenses.latest'));
        // expenseList.value = response.data.data;
        const response = await axios.get(route('expenses.latestJson', { page: currentPage.value}));
        expenseList.value = response.data.expenses.data;

        refreshKey.value++; // グラフ再描画
    } catch (e) {
        console.error('再取得エラー', e);
    }
}

const handleExpenseAdded = () => {
    // reloadExpenses(); // 一覧再取得
    expenseListRef.value?.reloadExpenses(); // 一覧再取得
    refreshKey.value++; // グラフ再取得
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
                                :submit-url="route('expenses.store')"
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
                            :ref="expenseListRef"
                            :initial-expenses="props.expenses"
                            :refresh-key="refreshKey"
                            @expenses-updated="refreshKey++"
                        />

                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
