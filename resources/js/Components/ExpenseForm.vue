<script setup>
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    expense: Object,    // 初期データ
    categories: Array,  // カテゴリー一覧
    submitUrl: String,  // 送信先ルート
    method: String,     // POST or PUT
});

const form = useForm({
    amount: props.expense.amount ?? '',
    date: props.expense.date ?? '',
    title: props.expense.title ?? '',
    category_id: props.expense.category_id ?? '',
});

const submit = () => {
    if(props.method === 'post') {
        form.post(props.submitUrl);
    } else if (props.method === 'put') {
        form.put(props.submitUrl);
    }
}
</script>

<template>
    <form @submit.prevent="submit">
        <!-- 金額 -->
        <div>
            <InputLabel for="amount" value="金額"/>
            <TextInput id="amount" type="number" class="mt-1 block" v-model="form.amount"/>
            <InputError class="mt-2" :message="form.errors.amount"/>
        </div>
        <!-- 日付 -->
        <div>
            <InputLabel for="date" value="日付"/>
            <TextInput id="date" type="date" class="mt-1 block" v-model="form.date"/>
            <InputError class="mt-2" :message="form.errors.date"/>
        </div>
        <!-- 費用名 -->
        <div>
            <InputLabel for="title" value="費用名"/>
            <TextInput id="title" type="text" class="mt-1 block" v-model="form.title"/>
            <InputError class="mt-2" :message="form.errors.title"/>
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
            <InputError class="mt-2" :message="form.errors.category_id"/>
        </div>

        <!-- 送信ボタン -->
        <div class="mt-4 flex items-center">
            <PrimaryButton class="ms-4">
                {{ props.method === 'post' ? '登録' : '更新' }}
            </PrimaryButton>
        </div>
    </form>
</template>
