<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// カスタムバリデーションエラーを管理
const customErrors = reactive({
    current_password: '',
    password: '',
    password_confirmation: ''
});

// バリデーション関数
const validateForm = () => {
    let isValid = true;

    // エラーメッセージをリセット
    customErrors.current_password = '';
    customErrors.password = '';
    customErrors.password_confirmation = '';

    // 現在のパスワードのバリデーション
    if (!form.current_password || form.current_password.toString().trim() === '') {
        customErrors.current_password = '現在のパスワードを入力して下さい';
        isValid = false;
    }

    // 新しいパスワードのバリデーション
    if (!form.password || form.password.toString().trim() === '') {
        customErrors.password = '新しいパスワードを入力して下さい';
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

const updatePassword = () => {
    // カスタムバリデーションを実行
    if (!validateForm()) {
        return; // バリデーションエラーがある場合は送信を中止
    }

    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: (errors) => {
            // サーバーからのエラーをカスタムエラーに設定
            if (errors.current_password) {
                customErrors.current_password = errors.current_password;
            }
            if (errors.password) {
                customErrors.password = errors.password;
            }
            if (errors.password_confirmation) {
                customErrors.password_confirmation = errors.password_confirmation;
            }

            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                パスワード変更
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                パスワードは８文字以上で設定して下さい
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" value="現在のパスワード" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />

                <InputError
                    :message="customErrors.current_password || form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div>
                <InputLabel for="password" value="新しいパスワード" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError :message="customErrors.password || form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="もう一度入力"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError
                    :message="customErrors.password_confirmation || form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <!-- <div class="flex items-center gap-4 justify-end sm:justify-start max-w-xl mr-8"> -->
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
