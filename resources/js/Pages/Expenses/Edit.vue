<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    expense: Object,
});

const form = useForm({
    amount: props.expense.amount,
    date: props.expense.date,
    title: props.expense.title,
    category: props.expense.category,
});

const submit = () => {
    form.put(route('expenses.update', props.expense.id), {
        onSuccess: () => form.reset(),
    });
}

</script>

<template>
    <Head title="編集ページ" />

    <AuthenticatedLayout>
            <Toast/>
            <template #header>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    編集ページ
                </h2>
            </template>

            <div class="py-12">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8" >
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <form @submit.prevent="submit">
                                <div>
                                    <InputLabel for="amount" value="金額" />
                                    <TextInput
                                        id="amount"
                                        type="number"
                                        class="mt-1 block"
                                        v-model="form.amount"
                                    />
                                    <InputError class="mt-2" :message="form.errors.amount" />
                                </div>

                                <div>
                                    <InputLabel for="date" value="日付" />
                                    <TextInput
                                        id="date"
                                        type="date"
                                        class="mt-1 block"
                                        v-model="form.date"
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
                                    />
                                    <InputError class="mt-2" :message="form.errors.category"/>
                                </div>

                                <div class="mt-4 flex items-center">
                                    <PrimaryButton class="ms-4">
                                        更新
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
    </AuthenticatedLayout>

</template>
