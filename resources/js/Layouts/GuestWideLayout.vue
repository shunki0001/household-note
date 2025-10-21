<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import Footer from '@/Layouts/Footer.vue';
</script>

<template>
    <!-- <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-900"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg dark:bg-white"
        >
            <slot />
        </div>
    </div> -->
    <nav
        class="border-b border-gray-100 bg-gray-100 dark:border-gray-700 dark:bg-gray-800"
    >
        <!-- Primary Navigation Menu -->
        <div class="px-4 sm:px-6 lg:px-8">
            <!-- ナビ全体を左右に分ける -->
            <div class="flex h-16 justify-between items-center">
                <!-- 左: ロゴ -->
                <div class="flex items-center">
                        <Link :href="route('dashboard')">
                            <ApplicationLogo
                                class="block h-11 w-auto fill-current text-gray-800 dark:text-gray-200"
                            />
                        </Link>
                </div>
                <!-- 右: ログイン・新規登録 -->
                <div class="flex items-center space-x-4">
                    <Link
                        href="/register"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition duration-300"
                    >
                        新規登録
                    </Link>

                    <Link
                        href="/login"
                        class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition duration-300"
                    >
                        ログイン
                    </Link>
                </div>
            </div>
        </div>

    </nav>
    <!-- <div class="min-h-screen bg-gray-100 text-gray-800">
        <slot />
    </div> -->
    <div>
        <slot />
    </div>
    <Footer />
</template>
