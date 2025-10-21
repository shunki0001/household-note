<script setup>
import GuestWideLayout from '@/Layouts/GuestWideLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
<Head title="Topページ" />

<GuestWideLayout>
    <div class="min-h-screen w-screen bg-gray-100 text-gray-800">
    <!-- <div class="min-h-screen w-screen bg-gray-50 text-gray-800"> -->
        <!-- Hero Section -->
        <section>
            <div class="relative w-full mx-auto">
                <img
                    src="/images/toppagelogo.png"
                    alt="アプリロゴ"
                    class="mx-auto w-full max-w-full h-[700px] object-cover mb-8"
                />

                <!-- h1を画像の上に重ねる -->
                <h1
                    class="absolute top-4 left-1/2 -translate-x-1/2 text-8xl font-extrabold text-gray-800 px-4 py-2 rounded-lg font-zen"
                >
                    カケボノート
                </h1>

                <!-- pを画像の下部に重ねる -->
                <p
                    class="absolute bottom-6 left-1/2 -translate-x-1/2 text-2xl text-gray-700 px-4 py-2 rounded-lg leading-relaxed font-zen"
                >
                    面倒くさがりでも続く<br /> シンプルな家計簿
                </p>
            </div>
        </section>

        <!-- アプリの特徴 -->
        <section class="py-16 px-4 mx-auto text-center bg-gray-100">
            <h2 class="text-3xl font-semibold mb-10 tracking max-w-md mx-auto sub-title">特徴</h2>
            <ul class="space-y-6 text-2xl text-gray-700">
                <li>支出・収入の管理がかんたん</li>
                <li>グラフで支出の傾向がすぐわかる</li>
                <li>セキュリティ重視</li>
            </ul>
        </section>

        <!-- デモ画像 -->
        <section class="py-16 bg-gray-100">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 px-4 text-2xl text-center">
                <div>
                    <p>今月の収支が一目でわかる！</p>
                    <img
                        src="/images/demo05.jpg"
                        alt="デモ画像1"
                        class="rounded-xl shadow-lg object-cover w-full"
                    />
                </div>

                <div>
                    <p>グラフで分析も!</p>
                    <img
                        src="/images/demo06.jpg"
                        alt="デモ画像2"
                        class="rounded-xl shadow-lg object-cover w-full"
                    />
                </div>

            </div>
        </section>
    </div>

</GuestWideLayout>
</template>

