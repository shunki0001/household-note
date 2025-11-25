<script setup>
import { defineEmits } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { useIncomeForm } from '@/composables/useIncomeForm';

const props = defineProps({
    income: {
        type: Object,
        default: () => ({}),
    },
    incomeCategories: {
        type: Array,
        default: () => [],
    },
    submitUrl: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        default: 'post',
    },
    back: {
        type: String,
        default: 'dashboard',
    },
});

const emit = defineEmits(['submitted']);
// const emit = defineEmits(['income-added', 'transactionsAdded']);
const { form, errors, submit } = useIncomeForm(props, emit);
</script>

<template>
    <form
        class="mx-auto max-w-md space-y-6 rounded-2xl bg-white p-6 shadow-md"
        @submit.prevent="submit"
    >
        <!-- 金額 -->
        <div>
            <InputLabel
                for="amount"
                value="金額"
                class="mb-1 text-base font-semibold text-gray-700"
            />
            <TextInput
                id="amount"
                v-model="form.amount"
                type="number"
                class="w-full rounded-lg border border-gray-300 p-2 text-base focus:outline-none focus:ring-2 focus:ring-indigo-400"
            />
            <InputError
                class="mt-1 text-sm text-red-500"
                :message="errors.amount"
            />
        </div>

        <!-- 日付 -->
        <div>
            <InputLabel
                for="income_date"
                value="日付"
                class="mb-1 text-base font-semibold text-gray-700"
            />
            <TextInput
                id="income_date"
                v-model="form.income_date"
                type="date"
                class="w-full rounded-lg border border-gray-300 p-2 text-base focus:outline-none focus:ring-2 focus:ring-indigo-400"
            />
            <InputError
                class="mt-1 text-sm text-red-500"
                :message="errors.income_date"
            />
        </div>

        <!-- カテゴリー -->
        <div>
            <InputLabel
                for="income_category_id"
                value="カテゴリー"
                class="mb-1 text-base font-semibold text-gray-700"
            />
            <select
                v-model="form.income_category_id"
                class="w-full rounded-lg border border-gray-300 p-2 text-base focus:outline-none focus:ring-2 focus:ring-indigo-400"
            >
                <option disabled value="">カテゴリーを選択</option>
                <option
                    v-for="income_category in incomeCategories"
                    :key="income_category.id"
                    :value="income_category.id"
                >
                    {{ income_category.name }}
                </option>
            </select>
            <InputError
                class="mt-1 text-sm text-red-500"
                :message="errors.income_category_id"
            />
        </div>

        <!-- ボタン（右端に寄せる） -->
        <div class="flex justify-end pt-2">
            <PrimaryButton
                class="rounded-lg bg-indigo-500 px-5 py-2 font-semibold text-white shadow-sm transition hover:bg-indigo-600"
            >
                {{ props.method === 'post' ? '登録' : '更新' }}
            </PrimaryButton>
        </div>
    </form>
</template>
