<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import InputError from '@/Components/InputError.vue';
// import InputLabel from '@/Components/InputLabel.vue';
// import PrimaryButton from '@/Components/PrimaryButton.vue';
// import TextInput from '@/Components/TextInput.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { watch, onMounted  } from 'vue';
import Toast from '@/Components/Toast.vue';
import Pagination from '@/Components/Pagination.vue';
import ExpenseForm from '@/Components/ExpenseForm.vue';


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
                        <!-- <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="amount" value="金額"/>

                                <TextInput
                                    id="amount"
                                    type="number"
                                    class="mt-1 block"
                                    v-model="form.amount"
                                    autofocus
                                    autocomplete="username"
                                />

                                <InputError class="mt-2" :message="form.errors.amount"/>
                            </div>

                            <div>
                                <InputLabel for="date" value="日付"/>
                                <TextInput
                                    id="date"
                                    type="date"
                                    v-model="form.date"
                                    class="mt-1 block"
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.date"/>
                            </div>

                            <div>
                                <InputLabel for="title" value="費用名"/>
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block"
                                    v-model="form.title"
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.title"/>
                            </div>

                            <div>
                                <InputLabel for="category" value="カテゴリー"/>
                                <select v-model="form.category_id">
                                    <option disabled value="">カテゴリを選択</option>
                                    <option
                                        v-for="category in props.categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.category"/>
                            </div>

                            <div class="mt-4 flex items-center">
                                <PrimaryButton
                                    class="ms-4"
                                >
                                    登録
                                </PrimaryButton>
                            </div>
                        </form> -->
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

    </AuthenticatedLayout>
</template>
