<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

// カスタムバリデーションエラーを管理
const customErrors = reactive({
    name: '',
    email: ''
});

// バリデーション関数
// const validateForm = () => {
//     let isValid = true;

//     // エラーメッセージをリセット
//     customErrors.name = '';
//     customErrors.email = '';

//     // 名前のバリデーション
//     if (!form.name || form.name.toString().trim() === '') {
//         customErrors.name = '名前を入力して下さい';
//         isValid = false;
//     }

//     // メールアドレスのバリデーション
//     if (!form.email || form.email.toString().trim() === '') {
//         customErrors.email = 'メールアドレスを入力して下さい';
//         isValid = false;
//     } else {
//         // メールアドレスの形式チェック
//         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//         if (!emailRegex.test(form.email)) {
//             customErrors.email = '正しいメールアドレスの形式で入力して下さい';
//             isValid = false;
//         }
//     }

//     return isValid;
// };

const updateProfile = () => {
    // カスタムバリデーションを実行
    // if (!validateForm()) {
    //     return; // バリデーションエラーがある場合は送信を中止
    // }

    form.patch(route('profile.update'), {
        onError: (errors) => {
            // サーバーからのエラーをカスタムエラーに設定
            // if (errors.name) {
            //     customErrors.name = errors.name;
            // }
            // if (errors.email) {
            //     customErrors.email = errors.email;
            // }
            console.log(errors);
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                アカウント情報
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                アカウントの名前とメールドレスを編集できます。
            </p>
        </header>

        <form
            @submit.prevent="updateProfile"
            class="mt-6 space-y-6"
        >
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

            <div>
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

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    メールアドレスが正しくありません
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        こちらをクリックして認証メールを送信してください(未実装)
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    認証メールを送信しました。確認してください。(未実装)
                </div>
            </div>

            <div class="flex justify-end sm:justify-start max-w-xl">
                <PrimaryButton :disabled="form.processing">保存</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        保存中...
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
