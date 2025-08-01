<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// カスタムバリデーションエラーを管理
const customErrors = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

// バリデーション関数
const validateForm = () => {
    let isValid = true;

    // エラーメッセージをリセット
    customErrors.name = '';
    customErrors.email = '';
    customErrors.password = '';
    customErrors.password_confirmation = '';

    // 名前のバリデーション
    if (!form.name || form.name.toString().trim() === '') {
        customErrors.name = '名前を入力して下さい';
        isValid = false;
    }

    // メールアドレスのバリデーション
    if (!form.email || form.email.toString().trim() === '') {
        customErrors.email = 'メールアドレスを入力して下さい';
        isValid = false;
    } else {
        // メールアドレスの形式チェック
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(form.email)) {
            customErrors.email = '正しいメールアドレスの形式で入力して下さい';
            isValid = false;
        }
    }

    // パスワードのバリデーション
    if (!form.password || form.password.toString().trim() === '') {
        customErrors.password = 'パスワードを入力して下さい';
        isValid = false;
    } else if (form.password.length < 8) {
        customErrors.password = 'パスワードは8文字以上で入力して下さい';
        isValid = false;
    }

    // パスワード確認のバリデーション
    if (!form.password_confirmation || form.password_confirmation.toString().trim() === '') {
        customErrors.password_confirmation = 'パスワード確認を入力して下さい';
        isValid = false;
    } else if (form.password !== form.password_confirmation) {
        customErrors.password_confirmation = 'パスワードが一致しません';
        isValid = false;
    }

    return isValid;
};

const submit = () => {
    // カスタムバリデーションを実行
    if (!validateForm()) {
        return; // バリデーションエラーがある場合は送信を中止
    }

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onError: (errors) => {
            // サーバーからのエラーをカスタムエラーに設定
            if (errors.name) {
                customErrors.name = errors.name;
            }
            if (errors.email) {
                customErrors.email = errors.email;
            }
            if (errors.password) {
                customErrors.password = errors.password;
            }
            if (errors.password_confirmation) {
                customErrors.password_confirmation = errors.password_confirmation;
            }
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="新規登録" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="名前" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="customErrors.name || form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="メールアドレス" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
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
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="customErrors.password || form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="もう一度入力"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="customErrors.password_confirmation || form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    登録済みの方はこちら
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    新規登録
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
