<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { watch, onMounted  } from 'vue';
import Toast from '@/Components/Toast.vue';
import Pagination from '@/Components/Pagination.vue';


const props = defineProps({
    // expenses: Array,
    expenses: Object,
});

const form = useForm({
    amount: '',
    date: '',
    title: '',
    category: '',
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
                        <form @submit.prevent="submit">
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
                                <TextInput
                                    id="category"
                                    type="text"
                                    class="mt-1 block"
                                    v-model="form.category"
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.category"/>
                            </div>

                            <div class="mt-4 flex items-center">
                                <PrimaryButton
                                    class="ms-4"
                                >
                                    登録
                                </PrimaryButton>
                            </div>
                        </form>
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
                                    <td class="border px-4 py-2">{{ expense.category }}</td>
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
                        <div class="mt-4 flex justify-center">
                            <nav>
                                <ul class="inline-flex -space-x-px">
                                    <li v-for="link in expenses.links" :key="link.label">
                                        <Link
                                            v-if="link.url"
                                            :href="link.url"
                                            class="px-3 py-1 rounded-md text-gray-700 bg-white border border-gray-300"
                                            :class="{ 'bg-blue-500 text-white': link.active }"
                                        >
                                            <!-- ラベルを判定して日本語に変換 -->
                                            <span v-if="link.label === 'pagination.previous'">&lt; 前へ</span>
                                            <span v-else-if="link.label === 'pagination.next'">次へ &gt;</span>
                                            <span v-else>{{ link.label }}</span>
                                        </Link>
                                        <span
                                            v-else
                                            class="px-3 py-1 rounded-md text-gray-400 bg-white border border-gray-300"
                                        >
                                            <span v-if="link.label === 'pagination.previous'">&lt; 前へ</span>
                                            <span v-else-if="link.label === 'pagination.next'">次へ &gt;</span>
                                            <span v-else>{{ link.label }}</span>
                                        </span>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>
