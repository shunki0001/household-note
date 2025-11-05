<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-gray-100 px-4 sm:px-6"
    >
        <!-- ロゴ -->
        <div class="mb-6 sm:mb-8">
        <Link href="/">
            <ApplicationLogo class="h-16 w-16 sm:h-20 sm:w-20 fill-current text-gray-500" />
        </Link>
        </div>

        <!-- ログインフォーム -->
        <div
        class="w-full max-w-sm sm:max-w-md bg-white px-6 py-6 sm:py-8 shadow-md rounded-lg"
        >
        <slot />
        </div>
    </div>
</template>
