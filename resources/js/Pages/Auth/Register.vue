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
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onError: (errors) => {
            if (process.env.NODE_ENV !== 'production') {
                console.log(errors);
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
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autofocus
                    autocomplete="name"
                />

                <InputError
                    class="mt-2"
                    :message="customErrors.name || form.errors.name"
                />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="メールアドレス" />

                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    autocomplete="username"
                />

                <InputError
                    class="mt-2"
                    :message="customErrors.email || form.errors.email"
                />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="パスワード" />

                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="customErrors.password || form.errors.password"
                />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="もう一度入力" />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="
                        customErrors.password_confirmation ||
                        form.errors.password_confirmation
                    "
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
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
