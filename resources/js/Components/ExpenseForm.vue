<script setup>
import { defineEmits } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { useExpenseFrom } from '@/composables/useExpenseForm';

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
const { form, submit } = useExpenseFrom(props, emit);
</script>

<template>
    <form @submit.prevent="submit">
        <!-- 金額 -->
        <div>
            <InputLabel for="amount" value="金額"/>
            <TextInput id="amount" type="number" v-model="form.amount"/>
        </div>

        <!-- 日付 -->
        <div>
            <InputLabel for="date" value="日付"/>
            <TextInput id="date" type="date" v-model="form.date"/>
        </div>

        <!-- 費用名 -->
        <div>
            <InputLabel for="title" value="費用名"/>
            <TextInput id="title" type="text"v-model="form.title"/>
        </div>

        <!-- カテゴリー -->
        <div>
            <InputLabel for="category_id" value="カテゴリー"/>
            <select v-model="form.category_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 mt-1 block w-full max-w-xs" required>
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
