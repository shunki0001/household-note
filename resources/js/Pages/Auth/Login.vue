<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// カスタムバリデーションエラーを管理
const customErrors = reactive({
    email: '',
    password: ''
});

// バリデーション関数
const validateForm = () => {
    let isValid = true;

    // エラーメッセージをリセット
    customErrors.email = '';
    customErrors.password = '';

    // メールアドレスのバリデーション
    if (!form.email || form.email.toString().trim() === '') {
        customErrors.email = 'メールアドレスを入力して下さい';
        isValid = false;
    } else {
        // メールアドレスの形式チェック
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(form.email)) {
            customErrors.email = 'メールアドレスまたはパスワードが間違っています';
            isValid = false;
        }
    }

    // パスワードのバリデーション
    if (!form.password || form.password.toString().trim() === '') {
        customErrors.password = 'パスワードを入力して下さい';
        isValid = false;
    } else if (form.password.length < 8) {
        customErrors.password = 'メールアドレスまたはパスワードが間違っています';
        isValid = false;
    }

    return isValid;
};

const submit = () => {
    // カスタムバリデーションを実行
    if (!validateForm()) {
        return; // バリデーションエラーがある場合は送信を中止
    }

    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onError: (errors) => {
            // サーバーからのエラーをカスタムエラーに設定
            if (errors.email) {
                customErrors.email = errors.email;
            }
            if (errors.password) {
                customErrors.password = errors.password;
            }
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="ログイン" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="メールアドレス" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="customErrors.email || form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="パスワード" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="customErrors.password || form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                        >アカウントを記憶する</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    パスワードリセット
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    ログイン
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
