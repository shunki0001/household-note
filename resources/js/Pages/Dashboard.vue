<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { watch, onMounted, ref  } from 'vue';
import Toast from '@/Components/Toast.vue';
import Pagination from '@/Components/Pagination.vue';
import ExpenseForm from '@/Components/ExpenseForm.vue';
import BarChart from '@/Components/BarChart.vue';
import BarChart2 from '@/Components/BarChart2.vue';

const props = defineProps({
    expenses: Object,
    categories: Array,
});

const form = useForm({
    amount: '',
    date: '',
    title: '',
    category_id: '',
});

const submit = () => {
    form.post(route('expenses.store'), {
        onSuccess: () => form.reset(), // 登録後にフォームを初期化
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

const fetchMonthlyData = async () => {
    const response = await fetch('http://localhost:8001/api/chart/monthly');
    const data = await response.json();

    monthlyChartData.value = {
        labels: data.map(item => `${item.month}月`),
        datasets: [{
            label: '月別支出合計',
            data: data.map(item => item.total),
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        }],
        title: '月別支出グラフ'
    };
};

// const fetchCategoryData = async (month = 7) => {
//     const response = await fetch(`http://localhost:8001/api/chart/category/${month}`);
//     const data = await response.json();

//     categoryChartData.value = {
//         labels: data.map(item => item.category),
//         datasets: [{
//             label: `${month}月のカテゴリ別支出`,
//             data: data.map(item => item.total),
//             backgroundColor: 'rgba(255, 99, 132, 0.7)'
//         }],
//         title: `${month}月のカテゴリ別支出グラフ`
//     };
// };

onMounted(() => {
    fetchMonthlyData();
    // fetchCategoryData();
})

</script>

<template>
    <Head title="アカウントトップページ" />

    <AuthenticatedLayout>
        <Toast/>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        ここにCRUD処理を入力
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
                            :expense="{}"
                            :categories="props.categories"
                            :submit-url="route('expenses.store')"
                            :method="'post'"
                        />
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
                                        <Link :href="route('expenses.edit', expense.id)" class="text-blue-500 hover:underLine">編集</Link>
                                        <DeleteButton :expenseId="expense.id"/>
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

        <div class="p-4">
            <h2 class="text-xl font-bold mb-4">月別支出</h2>
            <BarChart v-if="monthlyChartData" :chartData="monthlyChartData" />

            <!-- <h2 class="text-xl font-bold mb-4">カテゴリー別支出</h2>
            <BarChart v-if="categoryChartData" :chartData="categoryChartData" /> -->
        </div>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <BarChart2/>
                        <BarChart2
                            label="a"
                            apiUrl="/api/chart-data/category-monthly"
                        />
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
