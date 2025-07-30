<script setup>
import GuestWideLayout from '@/Layouts/GuestWideLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
<Head title="Topページ" />

<GuestWideLayout>
    <div class="min-h-screen w-screen bg-gray-50 text-gray-800">
        <!-- Hero Section -->
        <section class="text-center pt-8 pb-20 bg-white shadow-md w-full">
            <h1 class="text-5xl font-extrabold mb-4 tracking-tight max-w-md mx-auto">カケボノート</h1>
            <img
                src="/images/toppagelogo.png"
                alt="アプリロゴ"
                class="mx-auto w-full max-w-full h-[400px] object-contain mb-8"
            />
            <!-- <h1 class="title">カケボノート</h1> -->
            <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                簡単・シンプル<br />あなたのお金を見える化
            </p>

            <div class="flex justify-center space-x-6">
                <a
                    href="/register"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition duration-300"
                >
                    新規登録
                </a>
                <a
                    href="/login"
                    class="px-6 py-3 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition duration-300"
                >
                    ログイン
                </a>
            </div>
        </section>

        <!-- アプリの特徴 -->
        <section class="py-16 px-4 max-w-6xl mx-auto text-center">
            <h2 class="text-3xl font-semibold mb-10 tracking max-w-md mx-auto sub-title">特徴</h2>
            <ul class="space-y-6 text-lg text-gray-700">
                <li>支出・収入の管理がかんたん</li>
                <li>グラフで支出の傾向がすぐわかる</li>
                <li>セキュリティ重視</li>
            </ul>
        </section>

        <!-- デモ画像 -->
        <section class="py-16 bg-gray-100">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 px-4">
                <div>
                    <p>今月の支出が一目でわかる！</p>
                    <img
                        src="/images/demo.png"
                        alt="デモ画像1"
                        class="rounded-xl shadow-lg object-cover w-full"
                    />
                </div>

                <div>
                    <p>グラフで分析も!</p>
                    <img
                        src="/images/demo-1.png"
                        alt="デモ画像2"
                        class="rounded-xl shadow-lg object-cover w-full"
                    />
                </div>

            </div>
        </section>
    </div>

</GuestWideLayout>
</template>
