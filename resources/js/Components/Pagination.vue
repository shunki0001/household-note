<template>
    <div class="mt-4 flex justify-center" v-if="links.length > 3">
    <nav>
        <ul class="inline-flex -space-x-px">
        <li v-for="link in links" :key="link.label">
            <Link
            v-if="link.url"
            :href="link.url"
            class="px-3 py-1 rounded-md text-gray-700 bg-white border border-gray-300"
            :class="{ 'bg-blue-500 text-white': link.active }"
            >
            <span v-if="link.label === 'pagination.previous'">&lt; 前へ</span>
            <span v-else-if="link.label === 'pagination.next'">次へ &gt;</span>
            <span v-else>{{ link.label }}</span>
            </Link>
            <span
            v-else
            class="px-3 py-1 rounded-md text-gray-400 bg-white border border-gray-300"
            >
            <span v-if="link.label === 'pagination.previous'">&lt; 前へ</span>
            <span v-else-if="link.label === 'pagination.next'">次へ &gt;</span>
            <span v-else>{{ link.label }}</span>
            </span>
        </li>
        </ul>
    </nav>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: Array
})
</script>
