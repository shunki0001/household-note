<template>
    <div class="mt-4 flex justify-center" v-if="links.length > 3">
    <nav>
        <ul class="inline-flex items-center">
        <li v-for="link in links" :key="link.label" class="mx-1">
            <Link
            v-if="link.url"
            :href="link.url"
            preserve-scroll
            class="w-10 h-10 flex items-center justify-center rounded-md border border-gray-400 text-gray-700 bg-white transition"
            :class="{ 'bg-black text-white': link.active }"
            >
            <span v-if="link.label === 'pagination.previous'">&lt;</span>
            <span v-else-if="link.label === 'pagination.next'">&gt;</span>
            <span v-else v-html="link.label" />
            </Link>

            <span
            v-else
            class="w-10 h-10 flex items-center justify-center rounded-md border border-gray-400 text-gray-400 bg-white"
            >
            <span v-if="link.label === 'pagination.previous'">&lt;</span>
            <span v-else-if="link.label === 'pagination.next'">&gt;</span>
            <span v-else v-html="link.label" />
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
