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
            <select v-model="form.category_id" class="w-full border p-2 rounded w-full max-w-xs dark:bg-gray-200" required>
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
