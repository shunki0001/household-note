<script setup>
import { reactive, watch, defineEmits } from 'vue';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    expense: {
        type: Object,
        default: () => ({})
    },
    categories: {
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

const emit = defineEmits(['expense-added']);

const form = reactive({
    amount: props.expense.amount ?? '',
    date: props.expense.date ?? '',
    title: props.expense.title ?? '',
    category_id: props.expense.category_id ?? '',
    back: props.back,
});

watch(() => props.expense, (newExpense) => {
    form.amount = newExpense.amount ?? '';
    form.date = newExpense.date ?? '';
    form.title = newExpense.title ?? '';
    form.category_id = newExpense.category_id ?? '';
});

const submit = async () => {
    try {
        const response = await axios.post(props.submitUrl, form);

        emit('expense-added');

        form.amount = '';
        form.date = '';
        form.title = '';
        form.category_id = '';
    } catch (error) {
        console.error('登録失敗', error);
    }
};
</script>

<template>
    <form @submit.prevent="submit">
        <!-- 金額 -->
        <div>
            <InputLabel for="amount" value="金額"/>
            <TextInput id="amount" type="number" class="mt-1 block" v-model="form.amount"/>
        </div>

        <!-- 日付 -->
        <div>
            <InputLabel for="date" value="日付"/>
            <TextInput id="date" type="date" class="mt-1 block" v-model="form.date"/>
        </div>

        <!-- 費用名 -->
        <div>
            <InputLabel for="title" value="費用名"/>
            <TextInput id="title" type="text" class="mt-1 block" v-model="form.title"/>
        </div>

        <!-- カテゴリー -->
        <div>
            <InputLabel for="category_id" value="カテゴリー"/>
            <select v-model="form.category_id" class="w-full border p-2 rounded" required>
                <option disabled value="">カテゴリーを選択</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
        </div>

        <!-- ボタン -->
        <div class="mt-4 flex items-center">
            <PrimaryButton class="ms-4">
                {{ props.method === 'post' ? '登録' : '更新' }}
            </PrimaryButton>
        </div>
    </form>
</template>
