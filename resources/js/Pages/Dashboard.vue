<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { watch, onMounted, ref, computed  } from 'vue';
import Toast from '@/Components/Toast.vue';
import Pagination from '@/Components/Pagination.vue';
import ExpenseForm from '@/Components/ExpenseForm.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';

const props = defineProps({
    expenses: Object,
    categories: Array,
    totalExpense: {
        type: [Number, String],
        default: 0,
    },
    // refreshKey: Number,
});

const formattedTotal = computed(() => {
    return Number(props.totalExpense).toLocaleString();
});

const refreshKey = ref(0)

const form = useForm({
    amount: '',
    date: '',
    title: '',
    category_id: '',
});
const formKey = ref(0);


// const submit = () => {
//     form.post(route('expenses.store'), {
//         onSuccess: () => form.reset(), // 登録後にフォームを初期化
//     });
// }

const submit = () => {
    form.post(route('expenses.store'), {
        onSuccess: () => {
            form.reset();
            // formKey.value++;
            refreshKey.value++;
        },
    });
}

// // 現在のページのpropsを取得
const page = usePage();

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

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                    今月の家計状況
                    <DoughnutChart
                        :refresh-key="refreshKey"
                    />
                    <!-- <p>今月の合計支出: {{ totalExpense.toLocaleString() }}円</p> -->
                    <p>今月の合計支出: {{ formattedTotal }}円</p>
                    <p>今月の合計収入: ◯◯円</p>
                    <p>収支: ◯◯円</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <ExpenseForm
                            :key="formKey"
                            :expense="{}"
                            :categories="props.categories"
                            :submit-url="route('expenses.store')"
                            :method="'post'"
                            @expense-added="refreshKey++"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        グラフ・一覧ページに移動するボタンを設置
                        <div class="mt-4">
                            <Link href="/list">
                                <PrimaryButton class="ms-4">一覧ページに移動</PrimaryButton>
                            </Link>
                        </div>
                        <div class="mt-4">
                            <Link href="/graph/monthly">
                                <PrimaryButton class="ms-4">月別支出グラフ</PrimaryButton>
                            </Link>
                        </div>
                        <div class="mt-4">
                            <Link href="/graph/category">
                                <PrimaryButton class="ms-4">カテゴリー別支出グラフ</PrimaryButton>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 一覧表示部分 -->
        <!-- <pre>{{ expenses }}</pre> -->
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                    最近の記録
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">金額</th>
                                    <th class="px-4 py-2">日付</th>
                                    <th class="px-4 py-2">費用名</th>
                                    <th class="px-4 py-2">カテゴリー</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="expense in expenses.data" :key="expense.id">
                                    <td class="border px-4 py-2">{{ expense.amount }}</td>
                                    <td class="border px-4 py-2">{{ expense.date }}</td>
                                    <td class="border px-4 py-2">{{ expense.title }}</td>
                                    <td class="border px-4 py-2">{{ expense.category?.name ?? '未分類' }}</td>
                                    <td class="border px-4 py-2">
                                        <!-- 要修正->コンポーネント化 -->
                                        <!-- 編集ボタン -->
                                        <Link :href="route('expenses.edit', { expense: expense.id, back: 'dashboard' })" class="text-blue-500 hover:underLine">編集</Link>
                                        <DeleteButton :expenseId="expense.id" @deleted="refreshKey++"/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- ページネーションリンクボタン -->
                        <Pagination :links="expenses.links"/>
                    </div>
                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>
