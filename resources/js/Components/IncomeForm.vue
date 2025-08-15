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
        default: () => ({})
    },
    income_categories: {
        type: Array,
        default: () => []
    },
    submitUrl: {
        type: String,
        required: true
    },
    method: {
        type: String,
        default: 'post'
    },
    back: {
        type: String,
        default: 'dashboard'
    },
});

const emit = defineEmits(['submitted']);
// const emit = defineEmits(['income-added', 'transactionsAdded']);
const { form, errors, submit } = useIncomeForm(props, emit);
</script>

<template>
    <form @submit.prevent="submit">
        <!-- 金額 -->
        <div>
            <InputLabel for="amount" value="金額"/>
            <!-- <TextInput id="amount" type="number" min="0" v-model="form.amount"/> -->
            <TextInput id="amount" type="number" v-model="form.amount"/>
            <InputError class="mt-2" :message="errors.amount"/>
        </div>

        <!-- 日付 -->
        <div>
            <InputLabel for="income_date" value="日付"/>
            <TextInput id="income_date" type="date" v-model="form.income_date"/>
            <InputError class="mt-2" :message="errors.income_date"/>
        </div>

        <!-- カテゴリー -->
        <div>
            <InputLabel for="income_category_id" value="カテゴリー"/>
            <select v-model="form.income_category_id" class="w-full border p-2 rounded w-full max-w-xs dark:bg-gray-200">
                <option disabled value="">カテゴリーを選択</option>
                <option v-for="income_category in income_categories" :key="income_category.id" :value="income_category.id">
                    {{ income_category.name }}
                </option>
            </select>
            <InputError class="mt-2" :message="errors.income_category_id"/>
        </div>

        <!-- ボタン -->
        <div class="mt-4 flex items-center">
            <PrimaryButton class="ms-4">
                {{ props.method === 'post' ? '登録' : '更新' }}
            </PrimaryButton>
        </div>
    </form>
</template>
