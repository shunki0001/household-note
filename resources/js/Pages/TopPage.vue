<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';

</script>
<template>
    <Head title="Topページ"/>

    <GuestLayout>
        <div class="min-h-screen bg-gray-50 text0gray-800">
            <!-- Hero Section -->
            <section class="text-center py-16 bg-white shadow-md">
                <img src="/images/toppagelogo.png" alt="アプリロゴ" class="mx-auto w-full max-w-screen-lg h-[400px] object-contain mb-6">
                <h1 class="text-4xl font-bold mb-4">カケボノート</h1>
                <p class="text-lg text-gray-600">かんたん・シンプル<br>あなたのお金を見える化</p>
                <a href="/register">新規登録</a>
                <a href="/login">ログイン</a>
            </section>

            <!-- アプリの特徴 -->
            <section class="py-12 px-4 max-w-4xl mx-auto">
                <h2 class="text-xl font-semibold text-center mb-8">アプリの特徴</h2>
                <ul class="space-y-4 text-lg list-disc list-inside">
                        <li class="text-center">支出・収入の管理がかんたん</li>
                        <li class="text-center">グラフで支出の傾向がすぐわかる</li>
                        <li class="text-center">セキュリティ重視</li>
                </ul>
            </section>

            <!-- デモ画像 -->
            <section class="py-12 bg-gray-100">
                <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 px-4">
                    <img src="" alt="デモ画像1" class="rounded-lg shadow-md">
                    <img src="" alt="デモ画像2" class="rounded-lg shadow-md">
                </div>
            </section>
        </div>
    </GuestLayout>

</template>
