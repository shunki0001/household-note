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
    <div class="min-h-screen bg-gray-100 text-gray-800">
        <slot />
    </div>
    <Footer />
</template>
