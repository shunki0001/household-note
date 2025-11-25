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
                <div class="relative mx-auto w-full">
                    <img
                        src="/images/toppagelogo.png"
                        alt="アプリロゴ"
                        class="mx-auto mb-8 h-[400px] w-full max-w-full object-cover sm:h-[500px] md:h-[700px]"
                    />

                    <!-- h1を画像の上に重ねる -->
                    <h1
                        class="sm:tex-6xl absolute left-1/2 top-4 -translate-x-1/2 whitespace-nowrap rounded-lg px-2 py-1 text-center font-zen text-4xl font-extrabold leading-tight text-gray-800 sm:px-4 sm:py-2 md:text-8xl"
                    >
                        カケボノート
                    </h1>

                    <!-- pを画像の下部に重ねる -->
                    <p
                        class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-lg px-3 py-1 text-center font-zen text-base leading-relaxed text-gray-700 sm:bottom-6 sm:text-xl md:text-2xl"
                    >
                        面倒くさがりでも続く<br />
                        シンプルな家計簿
                    </p>
                </div>
            </section>

            <!-- アプリの特徴 -->
            <section
                class="mx-auto bg-gray-100 px-4 py-12 text-center sm:py-16"
            >
                <h2
                    class="tracking sub-title mx-auto mb-8 max-w-md text-2xl font-semibold sm:mb-10 sm:text-3xl"
                >
                    特徴
                </h2>
                <ul
                    class="space-y-4 text-base text-gray-700 sm:space-y-6 sm:text-xl md:text-2xl"
                >
                    <li>支出・収入の管理がかんたん</li>
                    <li>グラフで支出の傾向がすぐわかる</li>
                    <!-- <li>セキュリティ重視</li> -->
                </ul>
            </section>

            <!-- デモ画像 -->
            <section class="bg-gray-100 py-12 sm:py-16">
                <div
                    class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 text-center text-base sm:gap-8 sm:text-xl md:grid-cols-2 md:text-2xl"
                >
                    <div>
                        <p class="mb-4">今月の収支が一目でわかる！</p>
                        <img
                            src="/images/demo01.png"
                            alt="デモ画像1"
                            class="w-full rounded-xl object-cover shadow-lg"
                        />
                    </div>

                    <div>
                        <p class="mb-4">グラフで分析も!</p>
                        <img
                            src="/images/demo02.png"
                            alt="デモ画像2"
                            class="w-full rounded-xl object-cover shadow-lg"
                        />
                    </div>
                </div>
            </section>
        </div>
    </GuestWideLayout>
</template>
