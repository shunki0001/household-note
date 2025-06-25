<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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
</script>

<template>
    <Head title="アカウントトップページ" />

    <AuthenticatedLayout>
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

    </AuthenticatedLayout>
</template>
