<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import IncomeForm from '@/Components/IncomeForm.vue';
import Toast from '@/Components/Toast.vue';

const props = defineProps({
    income: Object,
    income_categories: Array,
    back: String,
});

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
                <div class="max-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <IncomeForm
                                :income="props.income"
                                :income_categories="props.income_categories"
                                :submit-url="route('incomes.update', props.income.id)"
                                :method="'put'"
                                :back="back"
                            />
                        </div>
                    </div>
                </div>
            </div>
    </AuthenticatedLayout>
</template>
